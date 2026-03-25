<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex flex-wrap gap-2 p-3.5 bg-light border-b border-[#eaeaea] items-end shrink-0">
            <div class="flex flex-col">
                <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Date</span>
                <input type="date" v-model="searchDate" class="p-[6px_10px] border-2 border-white rounded-lg text-xs font-sans transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <button @click="searchDate = ''" class="px-3 py-1.5 bg-card border-2 border-light text-dark rounded-lg font-bold text-xs hover:-translate-y-px transition-transform shadow-sm">
                Clear Filter
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <div class="bg-linear-to-br from-dark to-[#2d3a4a] text-white p-3 rounded-lg flex justify-between items-center mb-3">
                <h3 class="text-xs text-white/70 font-medium">Total Sales</h3>
                <div class="text-lg font-extrabold">₱ {{ totalSales.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr>
                            <th class="text-left p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Date</th>
                            <th class="text-left p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Time</th>
                            <th class="text-center p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Type</th>
                            <th class="text-left p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Desc</th>
                            <th class="text-right p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Details</th>
                            <th class="text-right p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Amount</th>
                            <th class="text-left p-1.5 bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px]">Staff</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredHistory.length === 0">
                            <td colspan="7" class="text-center p-4 text-gray text-xs">No records found</td>
                        </tr>
                        <tr v-for="record in filteredHistory" :key="record.id" class="border-b border-light hover:bg-[#f8faf9] transition-colors">
                            <td class="p-[5px_8px] text-[0.65rem] whitespace-nowrap">{{ record.date }}</td>
                            <td class="p-[5px_8px] text-[0.65rem] whitespace-nowrap">{{ record.time }}</td>
                            <td class="p-[5px_8px] text-center">
                                <span :class="['px-2 py-0.5 rounded-full text-[0.52rem] font-bold uppercase tracking-[0.3px]', record.cat === 'Fuel' ? 'bg-primary-light text-primary' : 'bg-[#f5eeff] text-[#7c3aed]']">
                                    {{ record.cat }}
                                </span>
                            </td>
                            <td class="p-[5px_8px] text-[0.65rem] font-bold whitespace-nowrap">{{ record.item }}</td>
                            <td class="p-[5px_8px] text-[0.65rem] text-right whitespace-nowrap">{{ record.det }}</td>
                            <td class="p-[5px_8px] text-[0.65rem] font-bold text-right font-mono whitespace-nowrap">₱{{ record.amt.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
                            <td class="p-[5px_8px] text-[0.65rem] whitespace-nowrap">{{ record.staff }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const searchDate = ref('');

const historyData = ref([
    { id: 1, date: '2026-02-24', time: '08:30 AM', cat: 'Fuel', item: 'Front Diesel', det: '10.00 L', amt: 558, staff: 'DODONG' },
    { id: 2, date: '2026-02-25', time: '01:30 PM', cat: 'Fuel', item: 'Back Regular', det: '37.54 L', amt: 2095, staff: 'KENNETH' },
    { id: 3, date: '2026-02-25', time: '02:00 PM', cat: 'Item', item: 'CASTROL ACTIV 4T', det: '1 Qty', amt: 278, staff: 'KENNETH' }
]);

const filteredHistory = computed(() => {
    if (!searchDate.value) return historyData.value;
    return historyData.value.filter(record => record.date === searchDate.value);
});

const totalSales = computed(() => {
    return filteredHistory.value.reduce((sum, record) => sum + record.amt, 0);
});
</script>
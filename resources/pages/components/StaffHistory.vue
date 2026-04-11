<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full relative">
        
        <div class="flex flex-wrap gap-3 p-3.5 bg-light border-b border-light items-end shrink-0">
            <div class="flex flex-col gap-1">
                <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Filter By</span>
                <select v-model="filterType" @change="resetFilterValue" class="p-[6px_10px] border-2 border-white rounded-lg text-xs font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                    <option value="day">Specific Day</option>
                    <option value="month">Whole Month</option>
                    <option value="year">Whole Year</option>
                </select>
            </div>

            <div class="flex flex-col gap-1" v-if="filterType === 'day'">
                <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Select Date</span>
                <input type="date" v-model="filterValue" class="p-[5px_10px] border-2 border-white rounded-lg text-xs font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>

            <div class="flex flex-col gap-1" v-if="filterType === 'month'">
                <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Select Month</span>
                <input type="month" v-model="filterValue" class="p-[5px_10px] border-2 border-white rounded-lg text-xs font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>

            <div class="flex flex-col gap-1" v-if="filterType === 'year'">
                <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Select Year</span>
                <select v-model="filterValue" class="p-[6px_10px] border-2 border-white rounded-lg text-xs font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white">
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                </select>
            </div>

            <button @click="clearFilter" class="px-3 py-1.5 bg-card border-2 border-light text-dark rounded-lg font-bold text-xs hover:-translate-y-px transition-transform shadow-sm h-8">
                Clear All
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 no-scrollbar relative">
            
            <div v-if="isLoading" class="absolute inset-0 flex justify-center items-center bg-white/50 backdrop-blur-sm z-20">
                <i class="fa-solid fa-spinner fa-spin text-primary text-3xl"></i>
            </div>

            <div class="bg-linear-to-br from-dark to-[#2d3a4a] text-white p-4 rounded-xl flex justify-between items-center mb-4 shadow-sm">
                <div>
                    <h3 class="text-[0.7rem] text-white/70 font-bold uppercase tracking-[1px] mb-0.5">Sales History</h3>
                    <p class="text-[0.65rem] text-white/50 font-medium">
                        {{ displayFilterText }}
                    </p>
                </div>
                <div class="text-[1.3rem] font-black font-mono tracking-tight">₱ {{ totalSales.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</div>
            </div>

            <div class="border border-light rounded-lg overflow-hidden bg-white">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-light border-b border-light">
                        <tr>
                            <th class="text-left p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Date</th>
                            <th class="text-center p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Type</th>
                            <th class="text-left p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Description</th>
                            <th class="text-right p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Volume/Qty</th>
                            <th class="text-right p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Amount</th>
                            <th class="text-left p-2.5 text-gray text-[0.6rem] font-extrabold uppercase tracking-[0.5px]">Staff</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="filteredAudit.length === 0 && !isLoading">
                            <td colspan="6" class="text-center p-8 text-gray italic text-[0.75rem] font-bold">No sales records found for this period.</td>
                        </tr>
                        <tr v-for="record in filteredAudit" :key="record.id" class="border-b border-light/50 hover:bg-[#f8faf9] transition-colors">
                            <td class="p-[8px_10px] text-[0.68rem] whitespace-nowrap font-medium text-dark">
                                {{ record.date }} <span class="text-[0.55rem] text-gray ml-1">{{ record.time }}</span>
                            </td>
                            <td class="p-[8px_10px] text-center">
                                <span :class="['px-2 py-0.5 rounded-full text-[0.52rem] font-extrabold uppercase tracking-[0.5px]', record.cat === 'Fuel' ? 'bg-primary-light text-primary border border-[#b8e5d0]' : 'bg-[#f5eeff] text-[#7c3aed] border border-[#e9dbff]']">
                                    {{ record.cat }}
                                </span>
                            </td>
                            <td class="p-[8px_10px] text-[0.7rem] font-bold whitespace-nowrap text-dark">{{ record.item }}</td>
                            <td class="p-[8px_10px] text-[0.7rem] text-right whitespace-nowrap font-mono font-bold text-gray">{{ record.det }}</td>
                            <td class="p-[8px_10px] text-[0.75rem] font-black text-right font-mono whitespace-nowrap" :class="record.cat === 'Fuel' ? 'text-primary' : 'text-blue'">₱{{ record.amt.toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
                            <td class="p-[8px_10px] text-[0.65rem] whitespace-nowrap font-bold text-gray">{{ record.staff }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const auditData = ref([]);

// Filtering Logic
const filterType = ref('day'); // 'day', 'month', 'year'
const filterValue = ref(new Date().toISOString().split('T')[0]); // Default to today

const fetchHistory = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/sales-history');
        auditData.value = response.data || [];
    } catch (error) {
        console.error("Error fetching history:", error);
    } finally {
        isLoading.value = false;
    }
};

const resetFilterValue = () => {
    const d = new Date();
    if (filterType.value === 'day') {
        filterValue.value = d.toISOString().split('T')[0]; // YYYY-MM-DD
    } else if (filterType.value === 'month') {
        filterValue.value = d.toISOString().substring(0, 7); // YYYY-MM
    } else if (filterType.value === 'year') {
        filterValue.value = d.getFullYear().toString(); // YYYY
    }
};

const clearFilter = () => {
    filterType.value = 'day';
    filterValue.value = ''; // Show absolutely everything
};

// Computed property to format the text in the dark banner
const displayFilterText = computed(() => {
    if (!filterValue.value) return 'All Time Record';
    
    if (filterType.value === 'day') {
        return new Date(filterValue.value).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    } else if (filterType.value === 'month') {
        const [year, month] = filterValue.value.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    } else if (filterType.value === 'year') {
        return `Year ${filterValue.value}`;
    }
    return '';
});

const filteredAudit = computed(() => {
    let data = [...auditData.value];
    
    if (filterValue.value) {
        data = data.filter(record => {
            if (filterType.value === 'day') {
                return record.date === filterValue.value;
            } else if (filterType.value === 'month') {
                return record.date.startsWith(filterValue.value); // Checks if '2026-02-24' starts with '2026-02'
            } else if (filterType.value === 'year') {
                return record.date.startsWith(filterValue.value); // Checks if '2026-02-24' starts with '2026'
            }
            return true;
        });
    }
    
    return data.sort((a, b) => b.date.localeCompare(a.date));
});

const totalSales = computed(() => {
    return filteredAudit.value.reduce((sum, record) => sum + record.amt, 0);
});

onMounted(() => {
    fetchHistory();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Shift Reports</h3>
            <button @click="syncData" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-sync"></i> Sync
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="text-left p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Date</th>
                        <th class="text-left p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Shift</th>
                        <th class="text-left p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Gasman</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Gross</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Deduct</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Remit</th>
                        <th class="text-center p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Status</th>
                        <th class="text-center p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="shifts.length === 0">
                        <td colspan="8" class="text-center p-4 text-gray text-xs">No shifts available</td>
                    </tr>
                    <tr v-for="s in reversedShifts" :key="s.id" class="border-b border-light hover:bg-[#f8faf9] transition-colors">
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.date }}</td>
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.shift }}</td>
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.gm }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono">₱{{ (s.gf + s.gi).toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-danger">₱{{ s.td.toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-dark">₱{{ s.rem.toLocaleString() }}</td>
                        <td class="p-2 text-center">
                            <span :class="['px-2 py-0.5 rounded-full text-[0.52rem] font-bold uppercase tracking-wide', s.status === 'Pending' ? 'bg-[#fef8ec] text-[#d4880f]' : 'bg-primary-light text-primary']">
                                {{ s.status }}
                            </span>
                        </td>
                        <td class="p-2 text-center">
                            <button @click="openModal(s)" :class="['px-2 py-1 rounded-md font-bold text-[0.58rem] transition-transform hover:-translate-y-px', s.status === 'Pending' ? 'bg-linear-to-br from-primary to-primary-hover text-white shadow-sm' : 'bg-card border-2 border-light text-dark']">
                                {{ s.status === 'Pending' ? 'Review' : 'View' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="selectedShift" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[8000] flex justify-center items-center p-4">
            <div class="bg-card w-full max-w-[700px] rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col max-h-[85vh] animate-[scaleIn_0.3s_ease-out]">
                
                <div class="p-[16px_20px] border-b border-light flex justify-between items-start shrink-0">
                    <div>
                        <h3 class="text-base font-extrabold">Shift Audit</h3>
                        <div class="text-[0.65rem] text-gray mt-0.5 font-medium">{{ selectedShift.gm }} | {{ selectedShift.date }} | {{ selectedShift.shift }}</div>
                    </div>
                    <button @click="selectedShift = null" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-light transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="p-[16px_20px] overflow-y-auto flex-1">
                    <div class="bg-primary-light p-3 rounded-lg flex justify-between mb-3.5 border border-[#c8e8da]">
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Fuel Sales</span><strong class="text-[0.88rem] font-extrabold">₱{{ selectedShift.gf.toLocaleString() }}</strong></div>
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Item Sales</span><strong class="text-[0.88rem] font-extrabold">₱{{ selectedShift.gi.toLocaleString() }}</strong></div>
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Deductions</span><strong class="text-[0.88rem] font-extrabold text-danger">₱{{ selectedShift.td.toLocaleString() }}</strong></div>
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Cash Due</span><strong class="text-[0.88rem] font-extrabold text-success">₱{{ selectedShift.rem.toLocaleString() }}</strong></div>
                    </div>

                    <h4 class="text-[0.72rem] font-bold border-b-2 border-light pb-1 mb-2">Fuel Dispensed</h4>
                    <table class="w-full text-xs mb-3">
                        <tr v-for="(f, i) in selectedShift.fuel" :key="i" class="border-b border-light/50"><td class="py-1">{{ f.pump }} Pump</td><td class="py-1">{{ f.fuel }}</td><td class="py-1 text-right">{{ f.liters.toFixed(2) }} L</td><td class="py-1 text-right font-bold">₱{{ f.amount.toLocaleString() }}</td></tr>
                        <tr v-if="!selectedShift.fuel.length"><td colspan="4" class="text-center py-2 text-gray">None</td></tr>
                    </table>

                    <h4 class="text-[0.72rem] font-bold border-b-2 border-light pb-1 mb-2">Items Sold</h4>
                    <table class="w-full text-xs mb-3">
                        <tr v-for="(it, i) in selectedShift.items" :key="i" class="border-b border-light/50"><td class="py-1">{{ it.name }}</td><td class="py-1 text-center">{{ it.qty }}</td><td class="py-1 text-right font-bold">₱{{ it.total.toLocaleString() }}</td></tr>
                        <tr v-if="!selectedShift.items.length"><td colspan="3" class="text-center py-2 text-gray">None</td></tr>
                    </table>
                </div>

                <div class="p-[12px_20px] border-t border-light flex justify-end gap-2 shrink-0">
                    <button @click="selectedShift = null" class="px-4 py-2 bg-card border-2 border-light rounded-lg text-xs font-bold text-dark hover:bg-light transition-colors">Cancel</button>
                    <button v-if="selectedShift.status === 'Pending'" @click="approveShift" class="px-4 py-2 bg-linear-to-br from-success to-primary-hover text-white rounded-lg text-xs font-bold flex items-center gap-1.5 hover:-translate-y-px transition-transform shadow-sm">
                        <i class="fa-solid fa-check"></i> Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const shifts = ref([
    {
        id: 1, date: "2026-02-25", shift: "12NN - 9PM", gm: "Kenneth", status: "Approved",
        fuel: [{ pump: "Back", fuel: "Regular", liters: 37.54, amount: 2095 }],
        items: [{ name: "CASTROL ACTIV 4T 1L", qty: 1, price: 278, total: 278 }],
        ded: { expense: 0, po: 0 }, gf: 2095, gi: 278, td: 0, rem: 2373
    },
    {
        id: 2, date: "2026-02-26", shift: "3AM - 12NN", gm: "Dodong", status: "Pending",
        fuel: [{ pump: "Front", fuel: "Diesel", liters: 50.00, amount: 2790 }],
        items: [], ded: { expense: 150 }, gf: 2790, gi: 0, td: 150, rem: 2640
    }
]);

const reversedShifts = computed(() => [...shifts.value].reverse());
const selectedShift = ref(null);

const syncData = () => { alert('Syncing with Server...'); };
const openModal = (shift) => { selectedShift.value = shift; };
const approveShift = () => {
    if(selectedShift.value) {
        selectedShift.value.status = 'Approved';
        alert('Shift Approved!');
        selectedShift.value = null;
    }
};
</script>
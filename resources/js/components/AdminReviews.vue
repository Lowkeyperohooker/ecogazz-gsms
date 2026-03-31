<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Shift Reports</h3>
            <button @click="syncData" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-sync"></i> Sync
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div v-if="isLoading" class="flex justify-center items-center py-10">
                <div class="text-gray text-sm font-bold animate-pulse">Loading shifts...</div>
            </div>
            <table v-else class="w-full border-collapse">
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
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.shift_date || s.date }}</td>
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.schedule || s.shift }}</td>
                        <td class="p-2 text-xs whitespace-nowrap">{{ s.user?.name || s.gasman || s.gm }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono">₱{{ parseFloat(s.gross_sales || s.gf + s.gi).toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-danger">₱{{ parseFloat(s.total_deductions || s.td).toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-dark">₱{{ parseFloat(s.net_remittance || s.rem).toLocaleString() }}</td>
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

        <div v-if="selectedShift" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-8000 flex justify-center items-center p-4">
            <div class="bg-card w-full max-w-175 rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col max-h-[85vh] animate-[scaleIn_0.3s_ease-out]">
                
                <div class="p-[16px_20px] border-b border-light flex justify-between items-start shrink-0">
                    <div>
                        <h3 class="text-base font-extrabold">Shift Audit</h3>
                        <div class="text-[0.65rem] text-gray mt-0.5 font-medium">{{ selectedShift.user?.name || selectedShift.gm }} | {{ selectedShift.shift_date || selectedShift.date }} | {{ selectedShift.schedule || selectedShift.shift }}</div>
                    </div>
                    <button @click="selectedShift = null" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-light transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="p-[16px_20px] overflow-y-auto flex-1">
                    <div class="bg-primary-light p-3 rounded-lg flex justify-between mb-3.5 border border-[#c8e8da]">
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Gross Sales</span><strong class="text-[0.88rem] font-extrabold">₱{{ parseFloat(selectedShift.gross_sales || 0).toLocaleString() }}</strong></div>
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Deductions</span><strong class="text-[0.88rem] font-extrabold text-danger">₱{{ parseFloat(selectedShift.total_deductions || 0).toLocaleString() }}</strong></div>
                        <div class="text-center flex-1"><span class="block text-[0.52rem] text-primary uppercase font-bold mb-1 tracking-wide">Cash Due</span><strong class="text-[0.88rem] font-extrabold text-success">₱{{ parseFloat(selectedShift.net_remittance || 0).toLocaleString() }}</strong></div>
                    </div>

                    <h4 class="text-[0.72rem] font-bold border-b-2 border-light pb-1 mb-2">Fuel Dispensed</h4>
                    <table class="w-full text-xs mb-3">
                        <tr v-for="(f, i) in (selectedShift.pumpReadings || selectedShift.fuel)" :key="i" class="border-b border-light/50">
                            <td class="py-1">{{ f.pump || 'Pump' }}</td>
                            <td class="py-1">{{ f.fuel_type || f.fuel }}</td>
                            <td class="py-1 text-right">{{ parseFloat(f.liters_sold || f.liters).toFixed(2) }} L</td>
                            <td class="py-1 text-right font-bold">₱{{ parseFloat(f.total_amount || f.amount).toLocaleString() }}</td>
                        </tr>
                        <tr v-if="!(selectedShift.pumpReadings || selectedShift.fuel)?.length"><td colspan="4" class="text-center py-2 text-gray">None</td></tr>
                    </table>

                    <h4 class="text-[0.72rem] font-bold border-b-2 border-light pb-1 mb-2">Items Sold</h4>
                    <table class="w-full text-xs mb-3">
                        <tr v-for="(it, i) in (selectedShift.itemSales || selectedShift.items)" :key="i" class="border-b border-light/50">
                            <td class="py-1">{{ it.product_name || it.name }}</td>
                            <td class="py-1 text-center">{{ it.quantity || it.qty }}</td>
                            <td class="py-1 text-right font-bold">₱{{ parseFloat(it.total_amount || it.total).toLocaleString() }}</td>
                        </tr>
                        <tr v-if="!(selectedShift.itemSales || selectedShift.items)?.length"><td colspan="3" class="text-center py-2 text-gray">None</td></tr>
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
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const shifts = ref([]);
const selectedShift = ref(null);
const isLoading = ref(true);

const reversedShifts = computed(() => [...shifts.value].reverse());

const fetchShifts = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/shifts');
        shifts.value = response.data.data || response.data; 
    } catch (error) {
        console.error("Error fetching shifts:", error);
    } finally {
        isLoading.value = false;
    }
};

const syncData = () => {
    fetchShifts();
};

const openModal = (shift) => { 
    selectedShift.value = shift; 
};

const approveShift = async () => {
    if(!selectedShift.value) return;
    
    try {
        await axios.put(`/api/shifts/${selectedShift.value.id}`, {
            status: 'Approved'
        });
        
        selectedShift.value.status = 'Approved';
        alert('Shift Approved Successfully!');
        selectedShift.value = null;
        
    } catch (error) {
        alert('Failed to approve shift.');
        console.error(error);
    }
};

onMounted(() => {
    fetchShifts();
});
</script>
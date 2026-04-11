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
                        <td class="p-2 text-xs whitespace-nowrap font-bold text-dark">{{ s.user?.name || s.gasman || s.gm }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono">₱{{ parseFloat(s.gross_sales || 0).toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-danger">₱{{ parseFloat(s.total_deductions || 0).toLocaleString() }}</td>
                        <td class="p-2 text-xs text-right font-bold font-mono text-success">₱{{ parseFloat(s.net_remittance || 0).toLocaleString() }}</td>
                        <td class="p-2 text-center">
                            <span :class="['px-2 py-0.5 rounded-full text-[0.52rem] font-bold uppercase tracking-wide', s.status === 'Pending' ? 'bg-[#fef8ec] text-[#d4880f] border border-[#f0d9a8]' : 'bg-success/10 text-success border border-success/20']">
                                <i :class="s.status === 'Pending' ? 'fa-solid fa-clock mr-0.5' : 'fa-solid fa-check mr-0.5'"></i> {{ s.status }}
                            </span>
                        </td>
                        <td class="p-2 text-center">
                            <button @click="openModal(s)" :class="['px-3 py-1.5 rounded-md font-bold text-[0.65rem] transition-transform hover:-translate-y-px', s.status === 'Pending' ? 'bg-linear-to-br from-primary to-primary-hover text-white shadow-[0_2px_8px_rgba(61,187,145,0.3)]' : 'bg-card border border-light text-dark hover:bg-light']">
                                {{ s.status === 'Pending' ? 'Review' : 'View' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="selectedShift" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-8000 flex justify-center items-center p-4">
            <div class="bg-card w-full max-w-187.5 rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col max-h-[90vh] animate-[scaleIn_0.3s_ease-out]">
                
                <div class="p-[16px_20px] border-b border-light flex justify-between items-start shrink-0 bg-[#fcfdfd] rounded-t-[18px]">
                    <div>
                        <h3 class="text-base font-extrabold flex items-center gap-2">
                            Shift Audit
                            <span v-if="selectedShift.status === 'Approved'" class="text-success text-[0.75rem] bg-success/10 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check-circle"></i> Approved</span>
                        </h3>
                        <div class="text-[0.65rem] text-gray mt-0.5 font-bold uppercase tracking-[0.5px]">
                            {{ selectedShift.user?.name || selectedShift.gasman }} | {{ selectedShift.shift_date }} | {{ selectedShift.schedule }}
                        </div>
                    </div>
                    <button @click="selectedShift = null" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-danger hover:text-white hover:border-danger transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="p-[16px_20px] overflow-y-auto flex-1 no-scrollbar">
                    
                    <div class="bg-primary-light/30 p-3 rounded-xl flex justify-between mb-4 border border-primary/20 shadow-sm">
                        <div class="text-center flex-1"><span class="block text-[0.55rem] text-gray uppercase font-bold mb-1 tracking-wide">Gross Sales</span><strong class="text-[0.95rem] font-extrabold font-mono">₱{{ parseFloat(selectedShift.gross_sales || 0).toLocaleString() }}</strong></div>
                        <div class="text-center flex-1 border-l border-primary/20"><span class="block text-[0.55rem] text-gray uppercase font-bold mb-1 tracking-wide">Deductions</span><strong class="text-[0.95rem] font-extrabold text-danger font-mono">-₱{{ parseFloat(selectedShift.total_deductions || 0).toLocaleString() }}</strong></div>
                        <div class="text-center flex-1 border-l border-primary/20 bg-primary-light/50 rounded-r-lg"><span class="block text-[0.55rem] text-primary uppercase font-bold mb-1 tracking-wide">Expected Cash</span><strong class="text-[1.1rem] font-black text-success font-mono">₱{{ parseFloat(selectedShift.net_remittance || 0).toLocaleString() }}</strong></div>
                    </div>

                    <h4 class="text-[0.72rem] font-bold text-gray uppercase tracking-[0.5px] border-b-2 border-light pb-1 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-gas-pump"></i> Fuel & Meter Breakdown</h4>
                    <table class="w-full text-left text-xs mb-4">
                        <thead class="text-[0.6rem] text-gray uppercase tracking-[0.5px]">
                            <tr>
                                <th class="py-1">Pump/Fuel</th>
                                <th class="py-1 text-right">Start</th>
                                <th class="py-1 text-right">Calib</th>
                                <th class="py-1 text-right">Close</th>
                                <th class="py-1 text-right text-blue">Sold (L)</th>
                                <th class="py-1 text-right text-success">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(f, i) in (selectedShift.pump_readings || selectedShift.pumpReadings || [])" :key="i" class="border-b border-light/50 hover:bg-[#f8faf9]">
                                <td class="py-1.5 font-bold text-dark">{{ f.fuel_config?.pump?.name || 'Pump' }} — {{ f.fuel_config?.fuel_type || 'Fuel' }}</td>
                                <td class="py-1.5 text-right font-mono">{{ parseFloat(f.start_meter || 0).toFixed(2) }}</td>
                                <td class="py-1.5 text-right font-mono text-warning">{{ parseFloat(f.calibration || 0).toFixed(2) }}</td>
                                <td class="py-1.5 text-right font-mono font-bold">{{ parseFloat(f.close_meter || 0).toFixed(2) }}</td>
                                <td class="py-1.5 text-right font-mono font-bold text-blue">{{ parseFloat(f.liters_sold || 0).toFixed(2) }}</td>
                                <td class="py-1.5 text-right font-mono font-bold text-success">₱{{ parseFloat(f.total_amount || 0).toLocaleString() }}</td>
                            </tr>
                            <tr v-if="!(selectedShift.pump_readings || selectedShift.pumpReadings)?.length"><td colspan="6" class="text-center py-3 text-gray italic">No fuel transactions.</td></tr>
                        </tbody>
                    </table>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-[0.72rem] font-bold text-gray uppercase tracking-[0.5px] border-b-2 border-light pb-1 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-box"></i> Items Sold</h4>
                            <table class="w-full text-xs">
                                <tr v-for="(it, i) in (selectedShift.item_sales || selectedShift.itemSales || [])" :key="i" class="border-b border-light/50">
                                    <td class="py-1.5 font-bold text-dark">{{ it.product?.name || it.product_name }}</td>
                                    <td class="py-1.5 text-center text-gray">x{{ it.quantity || it.qty }}</td>
                                    <td class="py-1.5 text-right font-bold text-success font-mono">₱{{ parseFloat(it.total_amount || it.amount).toLocaleString() }}</td>
                                </tr>
                                <tr v-if="!(selectedShift.item_sales || selectedShift.itemSales)?.length"><td colspan="3" class="text-center py-3 text-gray italic">None</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 class="text-[0.72rem] font-bold text-gray uppercase tracking-[0.5px] border-b-2 border-light pb-1 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-file-invoice-dollar"></i> Deductions</h4>
                            <table class="w-full text-xs">
                                <tr v-for="(ded, i) in (selectedShift.deductions || [])" :key="i" class="border-b border-light/50">
                                    <td class="py-1.5 font-bold text-dark uppercase">{{ ded.category }}</td>
                                    <td class="py-1.5 text-right font-bold text-danger font-mono">-₱{{ parseFloat(ded.amount).toLocaleString() }}</td>
                                </tr>
                                <tr v-if="!(selectedShift.deductions)?.length"><td colspan="2" class="text-center py-3 text-gray italic">None</td></tr>
                            </table>
                        </div>
                    </div>

                </div>

                <div class="p-[12px_20px] border-t border-light flex justify-end gap-2 shrink-0 bg-light rounded-b-[18px]">
                    <button @click="selectedShift = null" class="px-5 py-2 bg-white border border-light rounded-lg text-[0.7rem] font-bold text-gray hover:text-dark hover:border-gray transition-colors shadow-sm">Close</button>
                    <button v-if="selectedShift.status === 'Pending'" @click="approveShift" class="px-5 py-2 bg-linear-to-br from-success to-primary-hover text-white rounded-lg text-[0.75rem] font-bold flex items-center gap-1.5 hover:-translate-y-px transition-transform shadow-[0_4px_12px_rgba(61,187,145,0.3)]">
                        <i class="fa-solid fa-check-double"></i> Approve Remittance
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
        // Supports nested pagination structures if Laravel uses them
        shifts.value = response.data.data || response.data || []; 
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
    
    if(!confirm('Are you sure you want to approve this remittance? This cannot be undone.')) return;
    
    try {
        await axios.put(`/api/shifts/${selectedShift.value.id}`, {
            status: 'Approved'
        });
        
        selectedShift.value.status = 'Approved';
        alert('Shift Approved Successfully!');
        
    } catch (error) {
        alert('Failed to approve shift.');
        console.error(error);
    }
};

onMounted(() => {
    fetchShifts();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
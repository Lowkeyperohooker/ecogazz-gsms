<template>
    <div class="flex flex-col gap-3 h-full overflow-y-auto">
        <div class="bg-card rounded-xl shadow-sm border border-light shrink-0">
            <div class="flex justify-between items-center p-[12px_16px] border-b border-light">
                <h3 class="text-[0.85rem] font-bold">Pump Configuration</h3>
                <button @click="pushConfig" :disabled="isSaving" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm disabled:opacity-50">
                    <i class="fa-solid fa-spinner fa-spin" v-if="isSaving"></i>
                    <i class="fa-solid fa-upload" v-else></i> 
                    {{ isSaving ? 'Pushing...' : 'Push' }}
                </button>
            </div>
            <div class="bg-[#fef8ec] p-[8px_14px] border-l-4 border-warning text-[0.62rem] text-[#b8860b] font-medium">
                <strong>Warning:</strong> Editing meters updates the starting values for the next shift.
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">Pump</th>
                            <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">Type</th>
                            <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">Fuel</th>
                            <th class="text-right p-2 text-gray text-xs font-bold uppercase tracking-wide">Cost/L</th>
                            <th class="text-right p-2 text-gray text-xs font-bold uppercase tracking-wide">Sell/L</th>
                            <th class="text-right p-2 text-gray text-xs font-bold uppercase tracking-wide">Margin</th>
                            <th class="text-right p-2 text-gray text-xs font-bold uppercase tracking-wide">Meter Start</th>
                        </tr>
                    </thead>
                    <tbody v-if="isLoading">
                        <tr><td colspan="7" class="text-center p-4 text-gray text-xs"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</td></tr>
                    </tbody>
                    <tbody v-else>
                        <tr v-for="c in flatConfigs" :key="c.id" class="border-b border-light hover:bg-[#f8faf9]">
                            <td class="p-2 text-xs font-bold">{{ c.pump_name }}</td>
                            <td class="p-2 text-xs text-gray">{{ c.pump_type }}</td>
                            <td class="p-2 text-xs font-bold" :class="getFuelColor(c.fuel_type)">{{ c.fuel_type }}</td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.cost_price" class="w-17.5 p-1 border-2 border-light rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.selling_price" class="w-17.5 p-1 border-2 border-light rounded-md text-xs text-right font-mono font-bold transition-all focus:outline-none focus:border-primary">
                            </td>
                            <td :class="['p-2 text-right font-mono font-bold', (c.selling_price - c.cost_price) < 3 ? 'text-warning' : 'text-success']">
                                ₱{{ (c.selling_price - c.cost_price).toFixed(2) }}
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.current_meter" class="w-22.5 p-1 border-2 border-light rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-card rounded-xl shadow-sm border border-light flex flex-col flex-1 min-h-75">
            <div class="p-[12px_16px] border-b border-light shrink-0 flex justify-between items-center">
                <h3 class="text-[0.85rem] font-bold">Recent Meter Logs</h3>
                <span class="text-[0.6rem] text-gray bg-light px-2 py-0.5 rounded-full font-bold">Auto-generated from Shifts</span>
            </div>
            <div class="flex-1 flex items-center justify-center text-gray text-xs opacity-70">
                <p><i class="fa-solid fa-clock-rotate-left mr-1"></i> Shift log history will appear here.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const isSaving = ref(false);
const flatConfigs = ref([]);

// Helper for UI colors
const getFuelColor = (fuel) => {
    const f = fuel.toLowerCase();
    if(f.includes('diesel')) return 'text-[#d17a00]';
    if(f.includes('premium')) return 'text-danger';
    return 'text-blue';
};

// Fetch Pumps & Flatten into a single editable array
const fetchPumps = async () => {
    try {
        const response = await axios.get('/api/pumps');
        const pumps = response.data;
        
        let configs = [];
        pumps.forEach(pump => {
            const fc = pump.fuel_configs || pump.fuelConfigs || [];
            fc.forEach(c => {
                configs.push({
                    id: c.id,
                    pump_name: pump.name,
                    pump_type: pump.type,
                    fuel_type: c.fuel_type,
                    cost_price: parseFloat(c.cost_price),
                    selling_price: parseFloat(c.selling_price),
                    current_meter: parseFloat(c.current_meter)
                });
            });
        });
        flatConfigs.value = configs;
    } catch (error) {
        console.error("Error fetching pumps:", error);
    } finally {
        isLoading.value = false;
    }
};

// Push configurations to Laravel
const pushConfig = async () => {
    isSaving.value = true;
    try {
        const payload = { configs: flatConfigs.value };
        await axios.post('/api/pumps/update-configs', payload);
        alert('Configurations Successfully Pushed to Terminals!');
    } catch (error) {
        console.error('Error saving configs:', error);
        alert('Failed to save configurations.');
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    fetchPumps();
});
</script>
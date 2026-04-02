<template>
    <div class="flex flex-col gap-3 h-full overflow-hidden">
        
        <div class="bg-card rounded-xl shadow-sm border border-light p-[12px_16px] shrink-0 flex justify-between items-center">
            <div>
                <h3 class="text-[0.85rem] font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-gauge-high text-primary"></i> Pump Meter Readings
                </h3>
                <p class="text-[0.65rem] text-gray mt-0.5">Record your closing meters for this shift.</p>
            </div>
            <button @click="saveReadings" :disabled="isSaving || pumps.length === 0" class="px-4 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" v-if="isSaving"></i>
                <i class="fa-solid fa-save" v-else></i> 
                {{ isSaving ? 'Saving...' : 'Save Readings' }}
            </button>
        </div>

        <div v-if="isLoading" class="flex justify-center items-center h-32">
            <i class="fa-solid fa-spinner fa-spin text-primary text-2xl"></i>
        </div>

        <div v-else-if="pumps.length === 0" class="flex flex-col items-center justify-center p-10 bg-card rounded-xl border border-light text-gray mt-2">
            <i class="fa-solid fa-database text-4xl mb-3 opacity-50"></i>
            <p class="text-[0.9rem] font-bold text-dark">No pumps found.</p>
        </div>

        <div v-else class="flex-1 flex flex-col gap-2.5 min-h-0 overflow-hidden">
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 shrink-0">
                <button 
                    v-for="pump in pumps" :key="pump.id"
                    @click="activePumpId = pump.id"
                    :class="[
                        'p-[8px_10px] border-2 rounded-xl flex flex-col items-center justify-center gap-1 transition-all',
                        activePumpId === pump.id 
                            ? 'border-primary bg-primary-light shadow-[0_4px_15px_rgba(61,187,145,0.15)]' 
                            : 'border-light bg-card hover:border-primary/50 hover:bg-primary-light/50'
                    ]"
                >
                    <i :class="['fa-solid text-[1.2rem]', pump.type === 'Digital' ? 'fa-display' : 'fa-gauge', activePumpId === pump.id ? 'text-primary' : 'text-gray']"></i>
                    <span :class="['text-[0.7rem] font-bold tracking-[0.5px] uppercase leading-tight', activePumpId === pump.id ? 'text-dark' : 'text-gray']">{{ pump.name }}</span>
                    <span :class="['text-[0.5rem] font-bold uppercase tracking-[0.5px]', activePumpId === pump.id ? 'text-primary' : 'text-gray opacity-70']">{{ pump.type }}</span>
                </button>
            </div>

            <div v-if="activePump" class="bg-card rounded-xl shadow-sm border border-light overflow-y-auto flex-1 no-scrollbar animate-[fadeIn_0.3s_ease-out]">
                
                <div class="bg-light p-[10px_16px] border-b border-light flex justify-between items-center sticky top-0 z-10">
                    <h4 class="text-[0.8rem] font-bold text-dark uppercase tracking-[0.5px]">
                        <i class="fa-solid fa-gas-pump mr-1 text-gray"></i> {{ activePump.name }} PUMP
                    </h4>
                    <span class="text-[0.6rem] font-bold text-gray uppercase bg-white px-2 py-1 rounded-md border border-light shadow-sm">
                        {{ activePump.type }}
                    </span>
                </div>
                
                <div class="p-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div v-for="config in activePump.fuel_configs" :key="config.id" class="border border-light rounded-lg p-3 relative bg-[#fcfdfd] transition-all hover:border-primary/50 hover:shadow-sm">
                        
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[0.8rem] font-extrabold text-dark uppercase tracking-[0.5px]">{{ config.fuel_type }}</span>
                            <span class="text-[0.65rem] font-bold text-white bg-primary px-2 py-0.5 rounded-md shadow-sm">₱{{ parseFloat(config.selling_price).toFixed(2) }}/L</span>
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Start Meter</label>
                                <span class="text-[0.75rem] font-mono font-bold text-dark bg-light px-2 py-0.5 rounded">{{ parseFloat(config.current_meter).toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Close Meter</label>
                                <input type="number" step="any" v-model="config.close_meter" @input="calc(config)" placeholder="0.00" class="w-24 p-1.5 border-2 border-light rounded-lg text-right font-mono text-[0.75rem] font-bold transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Calibration (L)</label>
                                <input type="number" step="any" v-model="config.calibration" @input="calc(config)" placeholder="0.00" class="w-20 p-1 border-2 border-light rounded-lg text-right font-mono text-[0.7rem] transition-all focus:outline-none focus:border-warning focus:ring-2 focus:ring-warning/20">
                            </div>
                            
                            <div class="h-px bg-light w-full my-1.5"></div>
                            
                            <div class="flex justify-between items-center">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Liters Sold</label>
                                <span class="text-[0.8rem] font-mono font-bold text-blue">{{ config.liters_sold || '0.00' }} L</span>
                            </div>
                            <div class="flex justify-between items-center bg-primary-light/30 p-1.5 rounded-lg border border-primary/20">
                                <label class="text-[0.62rem] text-primary font-extrabold uppercase tracking-[0.5px]">Total Amount</label>
                                <span class="text-[0.9rem] font-mono font-extrabold text-success">₱{{ config.total_amount || '0.00' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const isSaving = ref(false);
const pumps = ref([]);
const activePumpId = ref(null);

const activePump = computed(() => pumps.value.find(p => p.id === activePumpId.value));

const fetchPumps = async () => {
    try {
        const response = await axios.get('/api/pumps');
        
        if (!response.data || response.data.length === 0) {
            pumps.value = [];
            return;
        }

        const uniquePumps = [];
        const seenPumps = new Set();
        
        response.data.forEach(pump => {
            // THE FIX: Combine Name AND Type to create a truly unique key!
            const uniqueKey = `${pump.name}-${pump.type}`;

            if (!seenPumps.has(uniqueKey)) {
                seenPumps.add(uniqueKey);
                
                const configs = pump.fuel_configs || pump.fuelConfigs || [];
                pump.fuel_configs = configs.map(config => ({
                    ...config,
                    close_meter: '',
                    calibration: '',
                    liters_sold: '0.00',
                    total_amount: '0.00'
                }));
                uniquePumps.push(pump);
            }
        });

        pumps.value = uniquePumps;
        
        if (uniquePumps.length > 0) {
            activePumpId.value = uniquePumps[0].id;
        }
        
    } catch (error) {
        console.error("Error fetching pumps:", error);
    } finally {
        isLoading.value = false;
    }
};

const calc = (config) => {
    const start = parseFloat(config.current_meter) || 0;
    const close = parseFloat(config.close_meter) || 0;
    const calib = parseFloat(config.calibration) || 0;

    let net = close - start;
    if (net < 0) net = 0;

    const sold = net - calib;
    config.liters_sold = sold > 0 ? sold.toFixed(2) : '0.00';
    config.total_amount = sold > 0 ? (sold * config.selling_price).toFixed(2) : '0.00';
};

const saveReadings = async () => {
    isSaving.value = true;
    
    try {
        let readingsPayload = [];
        pumps.value.forEach(pump => {
            pump.fuel_configs.forEach(config => {
                if (config.close_meter) {
                    readingsPayload.push({
                        id: config.id,
                        close_meter: parseFloat(config.close_meter)
                    });
                }
            });
        });

        if (readingsPayload.length === 0) {
            alert("Please enter at least one Close Meter before saving.");
            isSaving.value = false;
            return;
        }

        await axios.post('/api/pumps/save-readings', { readings: readingsPayload });
        alert("Meters updated successfully! The next shift's starting meters are now set.");
        
        await fetchPumps();

    } catch (error) {
        console.error("Error saving readings:", error);
        alert("Failed to save meters to the database. Please try again.");
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => fetchPumps());
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
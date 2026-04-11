<template>
    <div class="flex flex-col gap-3 h-full overflow-hidden">
        
        <div class="bg-card rounded-xl shadow-sm border border-light p-[12px_16px] shrink-0 flex justify-between items-center">
            <div>
                <h3 class="text-[0.85rem] font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-gauge-high text-primary"></i> Live Pump Readings
                </h3>
                <p class="text-[0.65rem] text-gray mt-0.5">Meters update automatically based on POS transactions and calibration.</p>
            </div>
            <div class="px-3 py-1.5 bg-success/10 text-success rounded-lg font-bold text-xs flex items-center gap-1.5 border border-success/20">
                <i class="fa-solid fa-link"></i> Synced with POS
            </div>
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
                            
                            <div class="flex justify-between items-center mt-1">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Calibration (L)</label>
                                <span class="text-[0.75rem] font-mono font-bold text-warning">{{ getCalibration(config.id).toFixed(2) }}</span>
                            </div>

                            <div class="flex justify-between items-center mt-1">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Close Meter</label>
                                <input type="number" step="any" 
                                    v-model="manualOverrides[config.id]" 
                                    @input="updateOverride(config.id, manualOverrides[config.id])"
                                    :placeholder="getAutoClose(config)" 
                                    :class="['w-24 p-1.5 border-2 rounded-lg text-right font-mono text-[0.75rem] font-bold transition-all focus:outline-none focus:ring-2', manualOverrides[config.id] ? 'border-primary text-primary focus:border-primary focus:ring-primary/20 bg-primary-light/10' : 'border-light text-gray focus:border-primary focus:ring-primary/20 bg-white']"
                                    title="Type a number here to manually override the POS math"
                                >
                            </div>
                            
                            <div class="h-px bg-light w-full my-1.5"></div>
                            
                            <div class="flex justify-between items-center">
                                <label class="text-[0.62rem] text-gray font-bold uppercase tracking-[0.5px]">Liters Sold</label>
                                <span class="text-[0.8rem] font-mono font-bold text-blue">{{ getPosLiters(config.id).toFixed(2) }} L</span>
                            </div>
                            <div class="flex justify-between items-center bg-primary-light/30 p-1.5 rounded-lg border border-primary/20">
                                <label class="text-[0.62rem] text-primary font-extrabold uppercase tracking-[0.5px]">Total Amount</label>
                                <span class="text-[0.9rem] font-mono font-extrabold text-success">₱{{ getPosAmount(config.id).toFixed(2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const pumps = ref([]);
const activePumpId = ref(null);

const cart = ref([]);
const manualOverrides = reactive({}); // Stores gasman's manual edits

const activePump = computed(() => pumps.value.find(p => p.id === activePumpId.value));

const loadSharedState = () => {
    const savedCart = localStorage.getItem('gas_pos_cart');
    if (savedCart) cart.value = JSON.parse(savedCart);

    const savedOverrides = localStorage.getItem('gas_pos_overrides');
    if (savedOverrides) Object.assign(manualOverrides, JSON.parse(savedOverrides));
};

const handleStorageChange = (e) => {
    if (e.key === 'gas_pos_cart') cart.value = JSON.parse(e.newValue || '[]');
    if (e.key === 'gas_pos_overrides') {
        Object.keys(manualOverrides).forEach(k => delete manualOverrides[k]);
        Object.assign(manualOverrides, JSON.parse(e.newValue || '{}'));
    }
};

const fetchPumps = async () => {
    try {
        const response = await axios.get('/api/pumps');
        if (!response.data || response.data.length === 0) return pumps.value = [];

        const uniquePumps = [];
        const seenPumps = new Set();
        
        response.data.forEach(pump => {
            const uniqueKey = `${pump.name}-${pump.type}`;
            if (!seenPumps.has(uniqueKey)) {
                seenPumps.add(uniqueKey);
                uniquePumps.push(pump);
            }
        });

        pumps.value = uniquePumps;
        if (uniquePumps.length > 0) activePumpId.value = uniquePumps[0].id;
        
    } catch (error) {
        console.error("Error fetching pumps:", error);
    } finally {
        isLoading.value = false;
    }
};

// Reads the transactions directly from the POS Cart
const getPosLiters = (configId) => cart.value.filter(c => c.cat === 'Fuel' && c.config_id === configId).reduce((sum, c) => sum + c.liters, 0);
const getPosAmount = (configId) => cart.value.filter(c => c.cat === 'Fuel' && c.config_id === configId).reduce((sum, c) => sum + c.amount, 0);
const getCalibration = (configId) => cart.value.filter(c => c.cat === 'Calib' && c.config_id === configId).reduce((sum, c) => sum + c.liters, 0);

// Calculates the expected "Perfect" Close Meter
const getAutoClose = (config) => {
    return (parseFloat(config.current_meter) + getPosLiters(config.id) + getCalibration(config.id)).toFixed(2);
};

// Saves the manual override to Local Storage so POS can send it!
const updateOverride = (configId, value) => {
    if (value === '' || value === null) delete manualOverrides[configId];
    else manualOverrides[configId] = value;
    localStorage.setItem('gas_pos_overrides', JSON.stringify(manualOverrides));
};

onMounted(() => {
    loadSharedState();
    window.addEventListener('storage', handleStorageChange);
    fetchPumps();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
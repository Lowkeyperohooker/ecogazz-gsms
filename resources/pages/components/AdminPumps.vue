<template>
    <div class="flex flex-col gap-3 h-full overflow-hidden">

        <div
            class="bg-card rounded-xl shadow-sm border border-light p-[12px_16px] shrink-0 flex justify-between items-center">
            <div>
                <h3 class="text-[0.85rem] font-bold flex items-center gap-1.5 text-dark">
                    <i class="fa-solid fa-sliders text-primary"></i> Master Pump Configurations
                </h3>
                <p class="text-[0.65rem] text-gray mt-0.5">Manage fuel pricing and correct system meters for all pumps.
                </p>
            </div>
            <button @click="saveConfigs" :disabled="isSaving || pumps.length === 0"
                class="px-4 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-[0_4px_12px_rgba(61,187,145,0.3)] disabled:opacity-50">
                <i class="fa-solid fa-spinner fa-spin" v-if="isSaving"></i>
                <i class="fa-solid fa-save" v-else></i>
                {{ isSaving ? 'Saving...' : 'Save Configurations' }}
            </button>
        </div>

        <div v-if="isLoading" class="flex justify-center items-center h-32">
            <i class="fa-solid fa-spinner fa-spin text-primary text-2xl"></i>
        </div>

        <div v-else-if="pumps.length === 0"
            class="flex flex-col items-center justify-center p-10 bg-card rounded-xl border border-light text-gray mt-2">
            <i class="fa-solid fa-database text-4xl mb-3 opacity-50"></i>
            <p class="text-[0.9rem] font-bold text-dark">No pumps found.</p>
        </div>

        <div v-else class="flex-1 flex flex-col gap-2.5 min-h-0 overflow-hidden">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 shrink-0">
                <button v-for="pump in pumps" :key="pump.id" @click="activePumpId = pump.id" :class="[
                    'p-[8px_10px] border-2 rounded-xl flex flex-col items-center justify-center gap-1 transition-all',
                    activePumpId === pump.id
                        ? 'border-primary bg-primary-light shadow-[0_4px_15px_rgba(61,187,145,0.15)]'
                        : 'border-light bg-card hover:border-primary/50 hover:bg-primary-light/50'
                ]">
                    <i
                        :class="['fa-solid text-[1.2rem]', pump.type === 'Digital' ? 'fa-display' : 'fa-gauge', activePumpId === pump.id ? 'text-primary' : 'text-gray']"></i>
                    <span
                        :class="['text-[0.7rem] font-bold tracking-[0.5px] uppercase leading-tight', activePumpId === pump.id ? 'text-dark' : 'text-gray']">{{
                            pump.name }}</span>
                    <span
                        :class="['text-[0.5rem] font-bold uppercase tracking-[0.5px]', activePumpId === pump.id ? 'text-primary' : 'text-gray opacity-70']">{{
                            pump.type }}</span>
                </button>
            </div>

            <div v-if="activePump"
                class="bg-card rounded-xl shadow-sm border border-light overflow-y-auto flex-1 no-scrollbar animate-[fadeIn_0.3s_ease-out]">

                <div
                    class="bg-light p-[10px_16px] border-b border-light flex justify-between items-center sticky top-0 z-10">
                    <h4 class="text-[0.8rem] font-bold text-dark uppercase tracking-[0.5px]">
                        <i class="fa-solid fa-gas-pump mr-1 text-gray"></i> {{ activePump.name }} PUMP
                    </h4>
                    <span
                        class="text-[0.6rem] font-bold text-gray uppercase bg-white px-2 py-1 rounded-md border border-light shadow-sm">
                        {{ activePump.type }}
                    </span>
                </div>

                <div class="p-3 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                    <div v-for="config in activePump.fuel_configs" :key="config.id"
                        class="border border-light rounded-xl p-4 relative bg-[#fcfdfd] transition-all hover:border-primary/50 hover:shadow-sm">

                        <div class="flex justify-between items-center mb-4 border-b border-light pb-2">
                            <span
                                class="text-[0.9rem] font-black text-dark uppercase tracking-[0.5px] flex items-center gap-1.5">
                                <span :class="['w-3 h-3 rounded-full', getBgClass(config.fuel_type)]"></span>
                                {{ config.fuel_type }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[0.6rem] text-gray font-bold uppercase tracking-[0.5px]">Cost
                                    Price</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray font-bold text-[0.75rem]">₱</span>
                                    <input type="number" step="any" v-model="config.cost_price"
                                        class="w-full p-[6px_8px_6px_20px] border-2 border-light rounded-lg text-right font-mono text-[0.8rem] font-bold transition-all focus:outline-none focus:border-warning focus:ring-2 focus:ring-warning/20 bg-white">
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[0.6rem] text-primary font-bold uppercase tracking-[0.5px]">Selling
                                    Price</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 text-primary font-bold text-[0.75rem]">₱</span>
                                    <input type="number" step="any" v-model="config.selling_price"
                                        class="w-full p-[6px_8px_6px_20px] border-2 border-primary/30 rounded-lg text-right font-mono text-[0.85rem] font-extrabold text-primary transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-primary-light/10 shadow-inner">
                                </div>
                            </div>
                        </div>

                        <div class="h-px bg-light w-full my-3"></div>

                        <div class="flex flex-col gap-1.5">
                            <label
                                class="text-[0.6rem] text-gray font-bold uppercase tracking-[0.5px] flex justify-between items-end">
                                Current System Meter
                                <span class="text-[0.5rem] font-medium opacity-70 normal-case">(Liters)</span>
                            </label>
                            <input type="number" step="any" v-model="config.current_meter"
                                class="w-full p-2 border-2 border-light rounded-lg text-right font-mono text-[0.9rem] font-bold transition-all focus:outline-none focus:border-blue focus:ring-2 focus:ring-blue/20 text-dark bg-white shadow-inner">
                            <p class="text-[0.55rem] text-gray leading-tight mt-1">Update this if the physical pump
                                drift causes a discrepancy.</p>
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

const getBgClass = (fuelType) => {
    if (!fuelType) return 'bg-gray';
    const type = fuelType.toLowerCase();
    if (type.includes('diesel')) return 'bg-[#f5a623]';
    if (type.includes('premium')) return 'bg-danger';
    return 'bg-blue';
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

                // Format decimals for the inputs
                const configs = pump.fuel_configs || pump.fuelConfigs || [];
                pump.fuel_configs = configs.map(config => ({
                    ...config,
                    cost_price: parseFloat(config.cost_price).toFixed(2),
                    selling_price: parseFloat(config.selling_price).toFixed(2),
                    current_meter: parseFloat(config.current_meter).toFixed(2)
                }));
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

const saveConfigs = async () => {
    isSaving.value = true;

    try {
        let payload = [];
        pumps.value.forEach(pump => {
            pump.fuel_configs.forEach(config => {
                payload.push({
                    id: config.id,
                    cost_price: parseFloat(config.cost_price),
                    selling_price: parseFloat(config.selling_price),
                    current_meter: parseFloat(config.current_meter)
                });
            });
        });

        // Hitting the update-configs route you already have in api.php!
        await axios.post('/api/pumps/update-configs', { configs: payload });
        alert("Pump configurations updated successfully! New prices are now live.");

        await fetchPumps();

    } catch (error) {
        console.error("Error saving configurations:", error);
        alert("Failed to save configurations to the database. Please try again.");
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    fetchPumps();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
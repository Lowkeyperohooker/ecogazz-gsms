<template>
    <div class="bg-card rounded-xl p-4 border border-light shadow-sm overflow-y-auto">
        <h3 class="text-sm mb-1.5 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-gauge-high text-primary"></i> Pump Meter Readings
        </h3>
        <p class="text-xs text-gray mb-3 font-medium">Calibration updates meter but does not charge fuel</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="(fuels, pumpName) in groupedPumps" :key="pumpName" class="bg-light rounded-xl p-3">
                <h4 class="mb-2 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-gas-pump text-primary"></i> {{ pumpName.toUpperCase() }} PUMP
                </h4>
                
                <div class="grid grid-cols-[65px_1fr_1fr_55px_65px] gap-1 items-center mb-1.5 font-bold text-gray text-[0.55rem] uppercase tracking-wide">
                    <div>Fuel</div>
                    <div>Start</div>
                    <div>Close</div>
                    <div>Calib</div>
                    <div class="text-right">Result</div>
                </div>
                
                <div v-for="fuel in fuels" :key="fuel.id" class="grid grid-cols-[65px_1fr_1fr_55px_65px] gap-1 items-center mb-1.5 text-xs">
                    <div class="font-bold">{{ fuel.name }}</div>
                    <input type="number" step="any" v-model="fuel.start" class="p-1 border-2 border-white rounded-md font-bold text-right transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <input type="number" step="any" :value="(parseFloat(fuel.start) || 0) + fuel.sold + (parseFloat(fuel.calib) || 0)" readonly class="p-1 border-2 border-transparent bg-card rounded-md font-bold text-right text-dark">
                    <input type="number" step="any" v-model="fuel.calib" class="p-1 border-2 border-warning bg-[#fef8ec] text-[#b8860b] rounded-md font-bold text-right transition-all focus:outline-none focus:border-warning">
                    <span class="font-extrabold text-primary text-right text-sm">
                        {{ (fuel.sold + (parseFloat(fuel.calib) || 0)).toFixed(2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Mock Data structure based on your PUMPS array
const pumpsData = ref([
    { id: 1, pump: 'Front', name: 'Diesel', start: 1500, sold: 0, calib: 0 },
    { id: 2, pump: 'Front', name: 'Premium', start: 2000, sold: 0, calib: 0 },
    { id: 3, pump: 'Front', name: 'Regular', start: 1000, sold: 0, calib: 0 },
    { id: 4, pump: 'Back', name: 'Diesel', start: 800, sold: 0, calib: 0 },
    { id: 5, pump: 'Back', name: 'Premium', start: 500, sold: 0, calib: 0 },
    { id: 6, pump: 'Back', name: 'Regular', start: 300, sold: 0, calib: 0 }
]);

const groupedPumps = computed(() => {
    return pumpsData.value.reduce((acc, curr) => {
        if (!acc[curr.pump]) acc[curr.pump] = [];
        acc[curr.pump].push(curr);
        return acc;
    }, {});
});
</script>
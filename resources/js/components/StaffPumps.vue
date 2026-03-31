<template>
    <div class="bg-card rounded-xl p-4 border border-light shadow-sm overflow-y-auto">
        <h3 class="text-sm mb-1.5 font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-gauge-high text-primary"></i> Pump Meter Readings
        </h3>
        <p class="text-xs text-gray mb-3 font-medium">Calibration updates meter but does not charge fuel</p>
        
        <div v-if="isLoading" class="flex justify-center items-center py-10">
            <div class="text-gray text-sm font-bold animate-pulse">Loading pump data from server...</div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="(fuels, pumpName) in groupedPumps" :key="pumpName" class="bg-light rounded-xl p-3">
                </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const pumpsData = ref([]);
const isLoading = ref(true);

// Fetch data from Laravel API
const fetchPumpReadings = async () => {
    try {
        isLoading.value = true;
        // Make sure this endpoint matches your routes/api.php
        const response = await axios.get('/api/pumps/latest-readings');
        
        // Assuming your API returns an array of pump objects
        pumpsData.value = response.data;
    } catch (error) {
        console.error('Error fetching pump readings:', error);
        alert('Failed to load pump data from server.');
    } finally {
        isLoading.value = false;
    }
};

// Group the pumps by name (Front/Back) for the UI
const groupedPumps = computed(() => {
    return pumpsData.value.reduce((acc, curr) => {
        // Adjust 'pump' to match the actual column name from your database (e.g., curr.pump_name or curr.location)
        const pumpName = curr.pump || 'Unknown'; 
        if (!acc[pumpName]) acc[pumpName] = [];
        acc[pumpName].push(curr);
        return acc;
    }, {});
});

// Fetch data as soon as the component mounts
onMounted(() => {
    fetchPumpReadings();
});
</script>
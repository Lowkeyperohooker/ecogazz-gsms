<template>
  <div class="flex h-screen bg-[#f4f6f8] font-sans text-gray-800">
    
    <aside class="w-64 bg-[#004d40] text-white flex flex-col shadow-xl z-10">
      <div class="p-6 border-b border-[#26a69a] border-opacity-30">
        <h2 class="text-xl font-bold tracking-wider">EcoGazz</h2>
        <p class="text-sm text-[#b2dfdb]">Station Manager 2026</p>
      </div>
      
      <nav class="flex-1 p-4 space-y-2">
        <a href="#" class="block px-4 py-3 rounded-md hover:bg-[#00695c] transition-colors">Dashboard</a>
        <a href="#" class="block px-4 py-3 rounded-md bg-[#00695c] border-l-4 border-[#26a69a] font-medium transition-colors">Pump Sales</a>
        <a href="#" class="block px-4 py-3 rounded-md hover:bg-[#00695c] transition-colors">Inventory</a>
        <a href="#" class="block px-4 py-3 rounded-md hover:bg-[#00695c] transition-colors">Purchase Orders</a>
        <a href="#" class="block px-4 py-3 rounded-md hover:bg-[#00695c] transition-colors">Settings</a>
      </nav>

      <div class="p-4 border-t border-[#26a69a] border-opacity-30">
        <button class="w-full text-left px-4 py-2 hover:text-[#f57c00] transition-colors">Logout</button>
      </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
      <div class="max-w-4xl mx-auto">
        
        <header class="mb-8 flex justify-between items-end">
          <div>
            <h1 class="text-3xl font-bold text-[#004d40]">Daily Pump Shift Report</h1>
            <p class="text-gray-500 mt-1">Record accurate mechanical and digital pump readings.</p>
          </div>
        </header>

        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 border-t-4 border-[#00695c]">
          
          <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md font-medium">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="mb-6 p-4 bg-teal-50 border-l-4 border-[#00695c] text-[#00695c] rounded-md font-medium">
            {{ successMessage }}
          </div>

          <form @submit.prevent="submitReading" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              <div>
                <label class="block text-sm font-semibold text-[#004d40] mb-1">Select Pump</label>
                <select v-model="form.pump_id" @change="updateSuggestedStart" class="w-full rounded-md border-gray-300 shadow-sm p-2.5 border focus:border-[#00695c] focus:ring focus:ring-[#00695c] focus:ring-opacity-20 transition">
                  <option value="" disabled>Select a pump...</option>
                  <option v-for="pump in pumps" :key="pump.pump_id" :value="pump.pump_id">
                    {{ pump.pump_name }} ({{ pump.pump_type }})
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#004d40] mb-1">Fuel Type</label>
                <select v-model="form.fuel_type" class="w-full rounded-md border-gray-300 shadow-sm p-2.5 border focus:border-[#00695c] focus:ring focus:ring-[#00695c] focus:ring-opacity-20 transition">
                  <option value="Diesel">Diesel</option>
                  <option value="Premium">Premium</option>
                  <option value="Regular">Regular</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#004d40] mb-1">Starting Reading (L)</label>
                <input type="number" step="0.01" v-model="form.starting_reading" readonly 
                       class="w-full rounded-md border-gray-300 bg-gray-100 shadow-inner p-2.5 border cursor-not-allowed text-gray-600 font-mono" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#004d40] mb-1">Closing Reading (L)</label>
                <input type="number" step="0.01" v-model="form.closing_reading" required 
                       class="w-full rounded-md border-gray-300 shadow-sm p-2.5 border focus:border-[#00695c] focus:ring focus:ring-[#00695c] focus:ring-opacity-20 transition font-mono text-lg" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#004d40] mb-1">Calibration (L)</label>
                <input type="number" step="0.01" v-model="form.calibration" 
                       class="w-full rounded-md border-gray-300 shadow-sm p-2.5 border focus:border-[#00695c] focus:ring focus:ring-[#00695c] focus:ring-opacity-20 transition font-mono" />
              </div>
            </div>

            <div class="flex justify-end pt-6 mt-6 border-t border-gray-100">
              <button type="submit" :disabled="isSubmitting" class="bg-[#00695c] hover:bg-[#004d40] text-white font-bold py-3 px-8 rounded-md shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                <span v-if="isSubmitting">Saving to Database...</span>
                <span v-else>Save Shift Report</span>
              </button>
            </div>
          </form>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const pumps = ref([]);
const errorMessage = ref('');
const successMessage = ref('');
const isSubmitting = ref(false);

const form = ref({
  shift_id: 1, 
  pump_id: '',
  fuel_type: 'Diesel',
  starting_reading: 0,
  closing_reading: '',
  calibration: 0
});

const fetchLatestReadings = async () => {
  try {
    const response = await fetch('/api/pumps/latest-readings', {
        headers: { 'Accept': 'application/json' }
    });
    pumps.value = await response.json();
  } catch (error) {
    errorMessage.value = "Failed to load pump data. Make sure 'php artisan serve' is running.";
  }
};

const updateSuggestedStart = () => {
  const selected = pumps.value.find(p => p.pump_id === form.value.pump_id);
  if (selected) {
    form.value.starting_reading = selected.suggested_start_reading;
  }
};

const submitReading = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const response = await fetch('/api/pump-readings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();

    if (!response.ok) {
      errorMessage.value = data.message || "An error occurred.";
    } else {
      successMessage.value = "Shift Report Saved! Net Liters: " + data.data.net_liters;
      form.value.closing_reading = '';
      form.value.calibration = 0;
      fetchLatestReadings(); 
    }
  } catch (error) {
    errorMessage.value = "Network error. Please try again.";
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchLatestReadings();
});
</script>
<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Pricing & Margins</h3>
            <button @click="saveChanges" :disabled="isSaving" class="px-3 py-1.5 bg-linear-to-br from-success to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm disabled:opacity-60 disabled:hover:translate-y-0">
                <i class="fa-solid fa-spinner fa-spin" v-if="isSaving"></i>
                <i class="fa-solid fa-save" v-else></i> Save
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10">
                    <tr>
                        <th class="text-left p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Brand</th>
                        <th class="text-left p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Product</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Cost</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Sell</th>
                        <th class="text-right p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Margin</th>
                        <th class="text-center p-2 bg-light text-gray text-xs font-bold uppercase tracking-wide">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, idx) in inventory" :key="idx" class="border-b border-light/50 hover:bg-light/50 transition-all">
                        <td class="p-2 text-xs font-bold">{{ item.brand }}</td>
                        <td class="p-2 text-xs">{{ item.name }}</td>
                        <td class="p-2 text-right">
                            <input type="number" step="any" v-model="item.cost_price" class="w-full max-w-20 p-1 border-2 border-light/50 bg-white/50 rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                        </td>
                        <td class="p-2 text-right">
                            <input type="number" step="any" v-model="item.selling_price" class="w-full max-w-20 p-1 border-2 border-light/50 bg-white/50 rounded-md text-xs text-right font-mono font-bold transition-all focus:outline-none focus:border-primary">
                        </td>
                        <td :class="['p-2 text-right font-mono font-bold', (item.selling_price - item.cost_price) < 30 ? 'text-warning' : 'text-success']">
                            ₱{{ (item.selling_price - item.cost_price).toFixed(2) }}
                        </td>
                        <td class="p-2 text-center text-xs font-bold">{{ item.stock_quantity }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const inventory = ref([]);
const isSaving = ref(false);

const fetchInventory = async () => {
    try {
        const response = await axios.get('/api/products');
        inventory.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error fetching inventory:", error);
    }
};

const saveChanges = async () => {
    try {
        isSaving.value = true;
        await axios.post('/api/products/bulk-update', {
            products: inventory.value.map(item => ({
                id: item.id,
                cost_price: item.cost_price,
                selling_price: item.selling_price
            }))
        });
        alert('Prices and margins saved to database!');
    } catch (error) {
        alert('Failed to save prices.');
        console.error(error);
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    fetchInventory();
});
</script>
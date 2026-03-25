<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Purchase Orders</h3>
            <button @click="addPO" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-plus"></i> Add P.O.
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10 bg-linear-to-br from-blue to-[#4a6ae8]">
                    <tr>
                        <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Date</th>
                        <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Fuel</th>
                        <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Liters</th>
                        <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Price/L</th>
                        <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Add</th>
                        <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Total/L</th>
                        <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Amount</th>
                        <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide pl-4">Receiver</th>
                        <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Gasman</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="purchaseOrders.length === 0">
                        <td colspan="9" class="text-center p-4 text-gray text-xs">No purchase orders available</td>
                    </tr>
                    <tr v-for="po in purchaseOrders" :key="po.id" class="border-b border-light hover:bg-[#f8faf9] transition-colors">
                        <td class="p-2 text-xs whitespace-nowrap">{{ po.dt }}</td>
                        <td class="p-2 text-xs font-bold whitespace-nowrap">{{ po.fu }}</td>
                        <td class="p-2 text-xs text-right font-mono">{{ po.lt }}</td>
                        <td class="p-2 text-xs text-right font-mono">₱{{ po.pr.toFixed(2) }}</td>
                        <td class="p-2 text-xs text-right font-mono text-danger">₱{{ po.ad.toFixed(2) }}</td>
                        <td class="p-2 text-xs text-right font-mono text-danger">₱{{ (po.pr + po.ad).toFixed(2) }}</td>
                        <td class="p-2 text-xs text-right font-mono font-bold text-danger">₱{{ (po.lt * (po.pr + po.ad)).toLocaleString() }}</td>
                        <td class="p-2 text-xs whitespace-nowrap pl-4">{{ po.rc }}</td>
                        <td class="p-2 text-xs whitespace-nowrap">{{ po.gs }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const purchaseOrders = ref([
    { id: 1, dt: "20-Jan-26", fu: "DIESEL", lt: 200, pr: 55.80, ad: 0, rc: "BRYAN BACOY", gs: "DODONG" }
]);

const addPO = () => {
    alert('Add P.O. feature coming soon!');
};
</script>
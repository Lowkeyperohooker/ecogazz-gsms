<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Pricing & Margins</h3>
            <button @click="saveChanges" class="px-3 py-1.5 bg-linear-to-br from-success to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-save"></i> Save
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
                    <tr v-for="(item, idx) in inventory" :key="idx" :style="{ backgroundColor: item.bg }" class="border-b border-light/50 hover:brightness-95 transition-all">
                        <td class="p-2 text-xs font-bold">{{ item.b }}</td>
                        <td class="p-2 text-xs">{{ item.i }}</td>
                        <td class="p-2 text-right">
                            <input type="number" step="any" v-model="item.c" class="w-full max-w-[80px] p-1 border-2 border-white/50 bg-white/50 rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                        </td>
                        <td class="p-2 text-right">
                            <input type="number" step="any" v-model="item.s" class="w-full max-w-[80px] p-1 border-2 border-white/50 bg-white/50 rounded-md text-xs text-right font-mono font-bold transition-all focus:outline-none focus:border-primary">
                        </td>
                        <td :class="['p-2 text-right font-mono font-bold', (item.s - item.c) < 30 ? 'text-warning' : 'text-success']">
                            ₱{{ (item.s - item.c).toFixed(2) }}
                        </td>
                        <td class="p-2 text-center text-xs font-bold">{{ item.st }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const inventory = ref([
    { b:"PRYCEGAS", i:"11KG SET", c:2300, s:2600, st:5, bg:"#fff8ee" },
    { b:"PRYCEGAS", i:"11KG REFILL", c:950, s:1100, st:12, bg:"#fff8ee" },
    { b:"PETRON", i:"11KG REFILL", c:980, s:1110, st:15, bg:"#f5f7f6" },
    { b:"CALTEX", i:"TEXAMATIC 1L", c:240, s:290, st:20, bg:"#f8f8f8" },
    { b:"SHELL", i:"2T 1L", c:180, s:216, st:11, bg:"#fffce8" }
]);

const saveChanges = () => {
    alert('Prices and margins saved to database!');
};
</script>
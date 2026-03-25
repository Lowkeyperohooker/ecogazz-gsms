<template>
    <div class="flex flex-col gap-3 h-full overflow-y-auto">
        <div class="bg-card rounded-xl shadow-sm border border-light shrink-0">
            <div class="flex justify-between items-center p-[12px_16px] border-b border-light">
                <h3 class="text-[0.85rem] font-bold">Pump Configuration</h3>
                <button @click="pushConfig" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                    <i class="fa-solid fa-upload"></i> Push
                </button>
            </div>
            <div class="bg-[#fef8ec] p-[8px_14px] border-l-4 border-warning text-[0.62rem] text-[#b8860b] font-medium">
                <strong>Warning:</strong> Editing meters resets staff terminals.
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
                            <th class="text-right p-2 text-gray text-xs font-bold uppercase tracking-wide">Start</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in pumpConfigs" :key="c.id" class="border-b border-light hover:bg-[#f8faf9]">
                            <td class="p-2 text-xs">{{ c.lb }}</td>
                            <td class="p-2 text-xs">{{ c.tp }}</td>
                            <td class="p-2 text-xs">{{ c.fuel }}</td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.co" class="w-[70px] p-1 border-2 border-light rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.pr" class="w-[70px] p-1 border-2 border-light rounded-md text-xs text-right font-mono font-bold transition-all focus:outline-none focus:border-primary">
                            </td>
                            <td :class="['p-2 text-right font-mono font-bold', (c.pr - c.co) < 3 ? 'text-warning' : 'text-success']">
                                ₱{{ (c.pr - c.co).toFixed(2) }}
                            </td>
                            <td class="p-2 text-right">
                                <input type="number" step="any" v-model="c.st" class="w-[80px] p-1 border-2 border-light rounded-md text-xs text-right font-mono transition-all focus:outline-none focus:border-primary">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-card rounded-xl shadow-sm border border-light flex flex-col flex-1 min-h-[300px]">
            <div class="p-[12px_16px] border-b border-light shrink-0">
                <h3 class="text-[0.85rem] font-bold">Meter Logs</h3>
            </div>
            <div class="flex-1 overflow-y-auto">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-linear-to-br from-blue to-[#4a6ae8]">
                        <tr>
                            <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Date</th>
                            <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Pump</th>
                            <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide">Fuel</th>
                            <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Start</th>
                            <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Close</th>
                            <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Net L</th>
                            <th class="text-right p-2 text-white text-xs font-bold uppercase tracking-wide">Total</th>
                            <th class="text-left p-2 text-white text-xs font-bold uppercase tracking-wide pl-4">Gasman</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="pumpLogs.length === 0">
                            <td colspan="8" class="text-center p-4 text-gray text-xs">No logs available</td>
                        </tr>
                        <tr v-for="log in pumpLogs" :key="log.id" class="border-b border-light hover:bg-[#f8faf9]">
                            <td class="p-2 text-xs whitespace-nowrap">{{ log.date }}</td>
                            <td class="p-2 text-xs">{{ log.pump }}</td>
                            <td class="p-2 text-xs font-bold">{{ log.fuel }}</td>
                            <td class="p-2 text-xs text-right font-mono">{{ log.start.toFixed(2) }}</td>
                            <td class="p-2 text-xs text-right font-mono">{{ log.close.toFixed(2) }}</td>
                            <td class="p-2 text-xs text-right font-mono font-bold text-primary">{{ log.net.toFixed(2) }} L</td>
                            <td class="p-2 text-xs text-right font-mono">₱{{ log.amount.toLocaleString() }}</td>
                            <td class="p-2 text-xs pl-4">{{ log.gm }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const pumpConfigs = ref([
    { id:1, lb:"FRONT", tp:"Digital", fuel:"Diesel", pr:55.80, co:52.00, st:1500 },
    { id:2, lb:"FRONT", tp:"Digital", fuel:"Premium", pr:56.50, co:52.50, st:2000 },
    { id:3, lb:"BACK", tp:"Mechanical", fuel:"Regular", pr:55.80, co:52.00, st:300 }
]);

const pumpLogs = ref([
    { id:1, date: "2026-02-25 | 12NN - 9PM", pump: "BACK (Mechanical)", fuel: "Regular", start: 262.46, close: 300.00, net: 37.54, amount: 2095, gm: "Kenneth" },
    { id:2, date: "2026-02-26 | 3AM - 12NN", pump: "FRONT (Digital)", fuel: "Diesel", start: 1450.00, close: 1500.00, net: 50.00, amount: 2790, gm: "Dodong" }
]);

const pushConfig = () => { alert('Configuration Pushed!'); };
</script>
<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="flex gap-1 bg-card p-1 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] shrink-0 mb-2">
            <button 
                @click="activeMode = 'fuel'"
                :class="[
                    'flex-1 p-[10px_14px] rounded-lg font-bold text-[0.78rem] flex items-center justify-center gap-2 transition-all relative',
                    activeMode === 'fuel' 
                        ? 'bg-linear-to-br from-primary to-primary-hover text-white shadow-[0_4px_15px_rgba(61,187,145,0.3)]' 
                        : 'bg-transparent text-gray hover:bg-light hover:text-dark'
                ]"
            >
                <i class="fa-solid fa-gas-pump"></i> Fuel Sales
                <span :class="['px-1.5 py-px rounded-[10px] text-[0.55rem] font-bold', activeMode === 'fuel' ? 'bg-white/25' : 'bg-light text-gray']">
                    {{ fuelCartCount }}
                </span>
            </button>
            <button 
                @click="activeMode = 'prod'"
                :class="[
                    'flex-1 p-[10px_14px] rounded-lg font-bold text-[0.78rem] flex items-center justify-center gap-2 transition-all relative',
                    activeMode === 'prod' 
                        ? 'bg-linear-to-br from-blue to-[#4a6ae8] text-white shadow-[0_4px_15px_rgba(91,124,250,0.3)]' 
                        : 'bg-transparent text-gray hover:bg-light hover:text-dark'
                ]"
            >
                <i class="fa-solid fa-oil-can"></i> Products
                <span :class="['px-1.5 py-px rounded-[10px] text-[0.55rem] font-bold', activeMode === 'prod' ? 'bg-white/25' : 'bg-light text-gray']">
                    {{ prodCartCount }}
                </span>
            </button>
        </div>

        <div class="flex-1 flex flex-col md:flex-row gap-2 overflow-hidden min-h-0">
            <div class="flex-[1.1] flex flex-col gap-1.5 overflow-hidden min-w-0">
                <div class="flex flex-wrap gap-3 bg-card p-[8px_14px] rounded-lg border border-light shadow-[0_2px_8px_rgba(0,0,0,0.04)] shrink-0 items-center">
                    <div class="flex flex-col">
                        <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Date</span>
                        <span class="text-[0.75rem] font-bold">{{ today }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Schedule</span>
                        <select v-model="shift.schedule" class="border-none bg-transparent font-bold text-[0.72rem] text-dark cursor-pointer p-0 focus:outline-none">
                            <option>3AM - 12NN</option>
                            <option>12NN - 9PM</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[0.55rem] text-gray uppercase font-bold tracking-[0.5px]">Gasman</span>
                        <select v-model="shift.gasman" class="border-none bg-transparent font-bold text-[0.72rem] text-dark cursor-pointer p-0 focus:outline-none">
                            <option>DODONG</option>
                            <option>KENNETH</option>
                            <option>FRANCIS</option>
                            <option>ROXAS</option>
                        </select>
                    </div>
                </div>

                <div v-if="activeMode === 'fuel'" class="flex-1 flex flex-col gap-2 overflow-y-auto">
                    <div class="grid grid-cols-2 gap-1.5 shrink-0">
                        <button 
                            v-for="pump in ['Front', 'Back']" :key="pump"
                            @click="selectPump(pump)"
                            :class="[
                                'p-[12px_14px] border-2 rounded-lg flex flex-col items-center gap-1.5 transition-all',
                                selectedPump === pump ? 'border-primary bg-primary-light shadow-[0_4px_15px_rgba(61,187,145,0.15)]' : 'border-light bg-card hover:border-primary hover:bg-primary-light'
                            ]"
                        >
                            <i :class="['fa-solid text-[1.4rem] text-primary', pump === 'Front' ? 'fa-display' : 'fa-gauge']"></i>
                            <span class="text-[0.75rem] font-bold text-dark tracking-[0.5px] uppercase">{{ pump }} PUMP</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-2 shrink-0">
                        <div 
                            v-for="fuel in fuels" :key="fuel.key"
                            @click="selectFuel(fuel)"
                            :class="[
                                'bg-card border-2 rounded-xl p-3.5 cursor-pointer transition-all relative overflow-hidden',
                                selectedFuel?.key === fuel.key ? 'border-primary bg-primary-light' : 'border-light hover:border-primary hover:-translate-y-0.5 shadow-sm'
                            ]"
                        >
                            <div :class="['w-8.5 h-8.5 rounded-lg flex items-center justify-center text-[0.8rem] font-extrabold text-white mb-2', fuel.bgClass]">
                                {{ fuel.icon }}
                            </div>
                            <h4 class="text-[0.75rem] font-bold mb-1 tracking-[0.5px]">{{ fuel.name }}</h4>
                            <div class="text-[1rem] font-extrabold text-primary">₱{{ fuel.price.toFixed(2) }}</div>
                            <div class="text-[0.55rem] text-gray font-medium mt-0.5">per liter</div>
                        </div>
                    </div>

                    <div v-if="selectedFuel" class="bg-card rounded-xl p-3.5 border border-light shadow-sm shrink-0 animate-[slideUp_0.3s_ease-out]">
                        <h4 class="text-[0.72rem] font-bold mb-2.5 flex items-center gap-1.5">
                            {{ selectedPump }} Pump — {{ selectedFuel.name }}
                            <span :class="['px-2 py-0.5 rounded-full text-[0.52rem] font-bold text-white', selectedFuel.bgClass]">₱{{ selectedFuel.price }}/L</span>
                        </h4>
                        
                        <div class="flex flex-wrap gap-1 mb-2">
                            <button v-for="amt in [100, 200, 300, 500, 1000]" :key="amt" @click="setAmount(amt)" class="px-2.5 py-1.25 border-[1.5px] border-light rounded-full bg-card text-[0.6rem] font-bold text-dark hover:border-primary hover:bg-primary-light hover:text-primary transition-all active:scale-95">
                                ₱{{ amt }}
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-2.5">
                            <div class="flex flex-col">
                                <label class="text-[0.55rem] font-bold text-gray uppercase tracking-[0.5px] mb-0.75">Amount (₱)</label>
                                <input type="number" v-model="fuelAmount" @input="calcFromAmount" placeholder="0.00" class="p-[10px_12px] border-2 border-light rounded-lg text-[0.9rem] font-bold text-right transition-all focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[0.55rem] font-bold text-gray uppercase tracking-[0.5px] mb-0.75">Liters (L)</label>
                                <input type="number" v-model="fuelLiters" @input="calcFromLiters" placeholder="0.00" class="p-[10px_12px] border-2 border-light rounded-lg text-[0.9rem] font-bold text-right transition-all focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/15">
                            </div>
                        </div>

                        <button @click="addFuelToCart" class="w-full p-2.75 bg-linear-to-br from-primary to-primary-hover text-white border-none rounded-lg font-bold text-[0.82rem] flex items-center justify-center gap-2 transition-all hover:-translate-y-px shadow-[0_6px_20px_rgba(61,187,145,0.3)] active:translate-y-0">
                            <i class="fa-solid fa-plus-circle"></i> Add to Transaction
                        </button>
                    </div>
                </div>

                <div v-if="activeMode === 'prod'" class="flex-1 flex flex-col gap-1.5 overflow-hidden">
                    <div class="flex gap-1.5 shrink-0 items-center">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-search absolute left-2.75 top-1/2 -translate-y-1/2 text-gray text-[0.75rem]"></i>
                            <input type="text" v-model="searchQuery" placeholder="Search products..." class="w-full p-[8px_12px_8px_34px] border-2 border-light rounded-lg text-[0.75rem] font-medium bg-card transition-all focus:outline-none focus:border-blue focus:ring-4 focus:ring-blue/15">
                        </div>
                    </div>
                    
                    <div class="flex gap-1 overflow-x-auto shrink-0 pb-0.5 no-scrollbar">
                        <button 
                            v-for="(items, brand) in products" :key="brand"
                            @click="currentBrand = brand"
                            :class="[
                                'px-3 py-1.5 border-2 rounded-full font-semibold text-[0.62rem] whitespace-nowrap transition-all shadow-sm',
                                currentBrand === brand 
                                    ? 'bg-linear-to-br from-blue to-[#4a6ae8] text-white border-transparent shadow-[0_4px_12px_rgba(91,124,250,0.25)]' 
                                    : 'border-transparent bg-card text-gray hover:border-blue hover:text-blue'
                            ]"
                        >
                            {{ brand }} <span class="text-[0.5rem] opacity-70 ml-0.5">({{ items.length }})</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5 overflow-y-auto flex-1 p-0.5 content-start min-h-0">
                        <div v-for="(prod, idx) in filteredProducts" :key="idx" class="bg-card border-2 border-light rounded-xl p-2.5 flex flex-col gap-0.75 transition-all hover:border-blue hover:-translate-y-0.5 shadow-sm relative overflow-hidden animate-[fadeIn_0.4s_ease-out]">
                            <div class="text-[0.5rem] text-gray font-semibold uppercase tracking-[0.5px]">{{ currentBrand }}</div>
                            <div class="font-bold text-[0.66rem] leading-[1.2] text-dark">{{ prod.n }}</div>
                            <div class="text-blue font-extrabold text-[0.82rem] mt-0.5">₱{{ prod.p.toFixed(2) }}</div>
                            <div :class="['text-[0.5rem] font-medium flex items-center gap-0.75', prod.s < 10 ? 'text-danger' : 'text-gray']">
                                <i :class="['fa-solid text-[0.4rem]', prod.s < 10 ? 'fa-triangle-exclamation' : 'fa-box']"></i> Stock: {{ prod.s }}
                            </div>
                            <div class="flex gap-1 mt-1.5">
                                <input type="number" v-model="prod.addQty" min="1" class="w-9 p-[5px_2px] border-2 border-light rounded-lg text-center font-bold text-[0.7rem] transition-all focus:outline-none focus:border-blue focus:ring-[3px] focus:ring-blue/15">
                                <button @click="addProductToCart(prod)" class="flex-1 p-1.5 bg-linear-to-br from-blue to-[#4a6ae8] text-white border-none rounded-lg font-bold text-[0.62rem] flex items-center justify-center gap-1 transition-all hover:-translate-y-px shadow-[0_4px_12px_rgba(91,124,250,0.3)] active:translate-y-0">
                                    <i class="fa-solid fa-cart-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-[0.9] flex flex-col overflow-hidden min-w-0">
                <div class="bg-card rounded-xl p-2.5 border border-light shadow-sm flex-1 flex flex-col overflow-hidden min-h-0">
                    <div class="flex justify-between items-center mb-1.5 shrink-0">
                        <h3 class="text-[0.78rem] font-bold flex items-center gap-1.5"><i class="fa-solid fa-receipt text-primary"></i> Transaction</h3>
                        <span class="bg-primary-light text-primary px-2 py-0.5 rounded-full text-[0.6rem] font-bold">{{ cart.length }} items</span>
                    </div>

                    <div class="flex-1 overflow-y-auto border border-light rounded-lg min-h-0">
                        <div v-if="cart.length === 0" class="flex flex-col items-center justify-center py-7.5 px-2.5 text-gray h-full">
                            <i class="fa-solid fa-cart-shopping text-2xl mb-2 opacity-30"></i>
                            <span class="text-[0.65rem] font-medium">No transactions yet</span>
                        </div>
                        <table v-else class="w-full border-collapse">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th class="text-left p-[6px_8px] bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px] whitespace-nowrap">Item</th>
                                    <th class="text-right p-[6px_8px] bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px] whitespace-nowrap">Qty/L</th>
                                    <th class="text-right p-[6px_8px] bg-light text-gray text-[0.56rem] font-bold uppercase tracking-[0.5px] whitespace-nowrap">Total</th>
                                    <th class="w-7 bg-light"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in reversedCart" :key="item.id" class="hover:bg-[#f8faf9] transition-colors border-b border-light">
                                    <td :class="['p-[5px_8px] text-[0.65rem] font-bold whitespace-nowrap border-l-4', item.cat === 'Fuel' ? 'border-primary' : 'border-blue']">
                                        <span :class="['inline-flex px-1.25 py-px rounded-lg text-[0.45rem] font-bold mr-1', item.cat === 'Fuel' ? 'bg-primary-light text-primary' : 'bg-[#eef2ff] text-blue']">
                                            {{ item.cat === 'Fuel' ? '⛽' : '📦' }}
                                        </span>
                                        {{ item.desc }}
                                    </td>
                                    <td class="text-right p-[5px_8px] text-[0.65rem] whitespace-nowrap">{{ item.qty }}</td>
                                    <td class="text-right p-[5px_8px] text-[0.65rem] font-bold whitespace-nowrap">₱{{ item.amount.toFixed(2) }}</td>
                                    <td class="text-center p-[5px_8px]">
                                        <button @click="removeFromCart(item.id)" class="w-5.5 h-5.5 rounded-md bg-[#fef0f0] text-danger text-[0.6rem] flex items-center justify-center transition-all hover:bg-danger hover:text-white hover:scale-110">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-2 gap-2 my-1.5 shrink-0">
                        <div class="bg-linear-to-br from-primary-light to-[#d4f2e5] border border-[#b8e5d0] p-[8px_10px] rounded-lg text-center">
                            <div class="text-[0.5rem] text-gray uppercase font-bold tracking-[0.5px]">Gross Sales</div>
                            <div class="text-[0.9rem] font-extrabold text-success">₱{{ grossSales.toFixed(2) }}</div>
                        </div>
                        <div class="bg-light p-[8px_10px] rounded-lg text-center">
                            <div class="text-[0.5rem] text-gray uppercase font-bold tracking-[0.5px]">Total Liters</div>
                            <div class="text-[0.9rem] font-extrabold text-blue">{{ totalLiters.toFixed(2) }} L</div>
                        </div>
                    </div>

                    <div class="shrink-0 my-1">
                        <div @click="showDeductions = !showDeductions" class="flex items-center gap-1.5 cursor-pointer py-1 text-[0.62rem] font-bold text-gray transition-colors hover:text-dark">
                            <i :class="['fa-solid fa-chevron-down transition-transform', showDeductions ? 'rotate-180' : '']"></i> Deductions
                        </div>
                        <div v-show="showDeductions" class="grid grid-cols-3 gap-1 py-1.5 animate-[slideUp_0.3s_ease-out]">
                            <div v-for="(val, key) in deductions" :key="key" class="flex flex-col">
                                <label class="text-[0.48rem] text-gray font-bold uppercase mb-0.5 tracking-[0.3px]">{{ key }}</label>
                                <input type="number" v-model="deductions[key]" class="p-[5px_4px] border-2 border-light rounded-md font-bold text-[0.68rem] text-right focus:outline-none focus:border-primary focus:ring-[3px] focus:ring-primary/15">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 shrink-0 my-1">
                        <div class="bg-primary-light border border-[#b8e5d0] p-[8px_10px] rounded-lg text-center">
                            <div class="text-[0.48rem] text-gray uppercase font-bold tracking-[0.3px]">Expected Remit</div>
                            <div class="text-[0.88rem] font-extrabold text-success">₱{{ netRemittance.toFixed(2) }}</div>
                        </div>
                        <div class="bg-[#fef8ec] border border-[#f0d9a8] p-[8px_10px] rounded-lg text-center">
                            <div class="text-[0.48rem] text-gray uppercase font-bold tracking-[0.3px]">Over/Short</div>
                            <div class="text-[0.88rem] font-extrabold text-warning">₱0.00</div>
                        </div>
                    </div>

                    <div class="flex gap-1.5 justify-end shrink-0 mt-1">
                        <button @click="clearCart" class="p-[8px_18px] rounded-lg font-bold border-none cursor-pointer text-[0.72rem] flex items-center gap-1.5 transition-all bg-danger text-white hover:-translate-y-px shadow-sm">
                            <i class="fa-solid fa-trash"></i> Clear
                        </button>
                        <button @click="submitShift" :disabled="isSubmitting" class="p-[8px_18px] rounded-lg font-bold border-none cursor-pointer text-[0.72rem] flex items-center gap-1.5 transition-all bg-linear-to-br from-primary to-primary-hover text-white hover:-translate-y-px shadow-sm disabled:opacity-60 disabled:hover:translate-y-0 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-spinner fa-spin" v-if="isSubmitting"></i>
                            <i class="fa-solid fa-paper-plane" v-else></i>
                            {{ isSubmitting ? 'Submitting...' : 'Submit Shift' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';

// State
const activeMode = ref('fuel'); // 'fuel' or 'prod'
const today = new Date().toISOString().split('T')[0];
const shift = reactive({ schedule: '3AM - 12NN', gasman: 'DODONG' });
const isSubmitting = ref(false);

// Cart
const cart = ref([]);
const reversedCart = computed(() => [...cart.value].reverse());
const fuelCartCount = computed(() => cart.value.filter(i => i.cat === 'Fuel').length);
const prodCartCount = computed(() => cart.value.filter(i => i.cat === 'Item').length);

// --- FUEL LOGIC ---
const fuels = [
    { key: 'Diesel', name: 'DIESEL', price: 55.80, icon: 'D', bgClass: 'bg-linear-to-br from-[#f5a623] to-[#e8941a]' },
    { key: 'Premium', name: 'PREMIUM', price: 56.50, icon: 'P', bgClass: 'bg-linear-to-br from-danger to-[#d32f2f]' },
    { key: 'Regular', name: 'REGULAR', price: 55.80, icon: 'R', bgClass: 'bg-linear-to-br from-blue to-[#4a6ae8]' }
];
const selectedPump = ref('Front');
const selectedFuel = ref(null);
const fuelAmount = ref('');
const fuelLiters = ref('');

const selectPump = (pump) => {
    selectedPump.value = pump;
    selectedFuel.value = null;
};
const selectFuel = (fuel) => {
    selectedFuel.value = fuel;
    fuelAmount.value = '';
    fuelLiters.value = '';
};
const setAmount = (amt) => {
    fuelAmount.value = amt;
    calcFromAmount();
};
const calcFromAmount = () => {
    if (!selectedFuel.value) return;
    const v = parseFloat(fuelAmount.value) || 0;
    fuelLiters.value = v ? (v / selectedFuel.value.price).toFixed(2) : '';
};
const calcFromLiters = () => {
    if (!selectedFuel.value) return;
    const v = parseFloat(fuelLiters.value) || 0;
    fuelAmount.value = v ? (v * selectedFuel.value.price).toFixed(2) : '';
};
const addFuelToCart = () => {
    if (!selectedFuel.value) return alert('Select fuel');
    const lit = parseFloat(fuelLiters.value) || 0;
    const amt = parseFloat(fuelAmount.value) || 0;
    if (lit <= 0) return;

    cart.value.push({
        id: Date.now(), cat: 'Fuel',
        desc: `${selectedPump.value} — ${selectedFuel.value.name}`,
        qty: `${lit.toFixed(2)} L`, liters: lit, amount: amt
    });
    fuelAmount.value = ''; fuelLiters.value = '';
};

// --- PRODUCT LOGIC ---
const products = ref({
    "CALTEX": [{ n:"TEXAMATIC 1L", p:290, s:25, addQty:1 }, { n:"HAVOLINE 20W-40 1L", p:240, s:30, addQty:1 }],
    "SHELL": [{ n:"2T 200ML", p:50, s:50, addQty:1 }, { n:"COOLANT 1L", p:278, s:8, addQty:1 }]
});
const currentBrand = ref('CALTEX');
const searchQuery = ref('');

const filteredProducts = computed(() => {
    let items = products.value[currentBrand.value] || [];
    if (searchQuery.value) {
        items = items.filter(p => p.n.toLowerCase().includes(searchQuery.value.toLowerCase()));
    }
    return items;
});

const addProductToCart = (prod) => {
    const q = prod.addQty || 1;
    cart.value.push({
        id: Date.now(), cat: 'Item',
        desc: `${currentBrand.value} ${prod.n}`,
        qty: `${q}`, liters: 0, amount: q * prod.p
    });
    prod.addQty = 1;
};

// --- CART TOTALS & DEDUCTIONS ---
const showDeductions = ref(false);
const deductions = reactive({ exp: 0, po: 0, md: 0, mo: 0, rn: 0, hb: 0 });

const grossSales = computed(() => cart.value.reduce((sum, item) => sum + item.amount, 0));
const totalLiters = computed(() => cart.value.reduce((sum, item) => sum + item.liters, 0));
const totalDeductions = computed(() => Object.values(deductions).reduce((sum, val) => sum + (parseFloat(val) || 0), 0));
const netRemittance = computed(() => grossSales.value - totalDeductions.value);

const removeFromCart = (id) => {
    cart.value = cart.value.filter(item => item.id !== id);
};

const clearCart = () => {
    if(confirm('Clear all transactions?')) {
        cart.value = [];
        Object.keys(deductions).forEach(k => deductions[k] = 0);
    }
};

const submitShift = async () => {
    if (!cart.value.length) {
        alert('Cannot submit an empty shift. Please add transactions first.');
        return;
    }

    try {
        isSubmitting.value = true;

        const payload = {
            date: today,
            schedule: shift.schedule,
            gasman: shift.gasman,
            gross_sales: grossSales.value,
            total_deductions: totalDeductions.value,
            net_remittance: netRemittance.value,
            fuel_sales: cart.value.filter(c => c.cat === 'Fuel').map(f => ({
                pump: f.desc.split(' — ')[0],
                fuel_type: f.desc.split(' — ')[1],
                liters: f.liters,
                amount: f.amount
            })),
            item_sales: cart.value.filter(c => c.cat === 'Item').map(i => ({
                product_name: i.desc,
                quantity: parseInt(i.qty),
                amount: i.amount
            })),
            deductions: deductions
        };

        const response = await axios.post('/api/shifts', payload);
        alert('Shift Submitted Successfully!');
        
        cart.value = [];
        Object.keys(deductions).forEach(k => deductions[k] = 0);
        
    } catch (error) {
        console.error('Failed to submit shift:', error);
        alert(error.response?.data?.message || 'Error saving shift to the database. Please try again.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
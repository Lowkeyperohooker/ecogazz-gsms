<template>
    <div class="flex flex-col h-full overflow-hidden bg-card rounded-xl border border-light shadow-sm">
        
        <div class="flex justify-between items-center p-[16px_20px] border-b border-light shrink-0 bg-[#fcfdfd]">
            <div>
                <h3 class="text-[1.1rem] font-extrabold text-dark flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-primary"></i> Pricing & Margins
                </h3>
                <p class="text-[0.7rem] font-bold text-gray mt-0.5 uppercase tracking-[0.5px]">Manage product costs, selling prices, and stock levels</p>
            </div>
            <div class="flex gap-2">
                <button @click="showAddModal = true" class="px-4 py-2 bg-white border-2 border-light text-dark rounded-lg font-bold text-[0.75rem] flex items-center gap-1.5 transition-all hover:border-primary hover:text-primary shadow-sm">
                    <i class="fa-solid fa-plus"></i> Add Item
                </button>
                <button @click="saveInventory" :disabled="isSaving" class="px-5 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-[0.75rem] flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-[0_4px_12px_rgba(61,187,145,0.3)] disabled:opacity-50">
                    <i class="fa-solid fa-spinner fa-spin" v-if="isSaving"></i>
                    <i class="fa-solid fa-save" v-else></i>
                    {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto relative no-scrollbar">
            <div v-if="isLoading" class="absolute inset-0 flex justify-center items-center bg-white/50 backdrop-blur-sm z-10">
                <i class="fa-solid fa-spinner fa-spin text-primary text-3xl"></i>
            </div>

            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-light z-20 shadow-sm text-[0.65rem] text-gray uppercase tracking-[1px]">
                    <tr>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[15%]">Brand</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[25%]">Product</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[12%] text-center">Cost</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[12%] text-center">Sell</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[12%] text-right">Margin</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[14%] text-center">Stock</th>
                        <th class="p-[12px_20px] font-extrabold border-b border-light w-[10%] text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-[0.8rem] font-medium">
                    <tr v-if="products.length === 0 && !isLoading">
                        <td colspan="7" class="p-8 text-center text-gray italic font-bold">No products found in inventory.</td>
                    </tr>
                    <tr v-for="product in products" :key="product.id" class="border-b border-light/50 hover:bg-[#f8faf9] transition-colors">
                        <td class="p-[12px_20px] font-bold text-dark">{{ product.brand }}</td>
                        <td class="p-[12px_20px] font-bold text-dark">{{ product.name }}</td>
                        <td class="p-[12px_20px]">
                            <input type="number" step="any" v-model="product.cost_price" class="w-full p-[6px_10px] border-2 border-light rounded-lg text-center font-mono font-bold text-dark transition-all focus:outline-none focus:border-warning focus:ring-2 focus:ring-warning/20">
                        </td>
                        <td class="p-[12px_20px]">
                            <input type="number" step="any" v-model="product.selling_price" class="w-full p-[6px_10px] border-2 border-primary/30 rounded-lg text-center font-mono font-extrabold text-primary transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 bg-primary-light/10">
                        </td>
                        <td class="p-[12px_20px] text-right font-mono font-extrabold">
                            <span :class="getMargin(product) >= 0 ? 'text-success' : 'text-danger'">
                                ₱{{ getMargin(product).toFixed(2) }}
                            </span>
                        </td>
                        <td class="p-[12px_20px]">
                            <div class="flex items-center justify-center gap-1">
                                <input type="number" v-model="product.stock_quantity" class="w-20 p-[6px_10px] border-2 border-light rounded-lg text-center font-mono font-bold text-dark transition-all focus:outline-none focus:border-blue focus:ring-2 focus:ring-blue/20" :class="{'border-danger/50 text-danger bg-danger/5': product.stock_quantity <= 5}">
                            </div>
                        </td>
                        <td class="p-[12px_20px] text-center">
                            <button @click="deleteProduct(product.id)" class="text-danger hover:bg-danger/10 p-1.5 rounded-lg transition-colors" title="Delete Product">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showAddModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-9000 flex justify-center items-center p-4">
            <div class="bg-card w-full max-w-100 rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col animate-[scaleIn_0.2s_ease-out]">
                <div class="p-[16px_20px] border-b border-light flex justify-between items-center bg-[#fcfdfd] rounded-t-[18px]">
                    <h3 class="text-[0.95rem] font-extrabold text-dark flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-primary"></i> Add New Item
                    </h3>
                    <button @click="showAddModal = false" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-danger hover:text-white hover:border-danger transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px]">Brand</label>
                        <input type="text" v-model="newProduct.brand" placeholder="e.g. CALTEX" class="w-full p-2.5 border-2 border-light rounded-lg text-[0.8rem] font-bold text-dark focus:outline-none focus:border-primary">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px]">Product Name</label>
                        <input type="text" v-model="newProduct.name" placeholder="e.g. TEXAMATIC 1L" class="w-full p-2.5 border-2 border-light rounded-lg text-[0.8rem] font-bold text-dark focus:outline-none focus:border-primary">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px]">Cost (₱)</label>
                            <input type="number" step="any" v-model="newProduct.cost_price" class="w-full p-2.5 border-2 border-light rounded-lg text-[0.8rem] font-bold font-mono text-dark focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px]">Sell (₱)</label>
                            <input type="number" step="any" v-model="newProduct.selling_price" class="w-full p-2.5 border-2 border-light rounded-lg text-[0.8rem] font-bold font-mono text-dark focus:outline-none focus:border-primary">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px]">Initial Stock</label>
                        <input type="number" v-model="newProduct.stock_quantity" class="w-full p-2.5 border-2 border-light rounded-lg text-[0.8rem] font-bold font-mono text-dark focus:outline-none focus:border-primary">
                    </div>
                </div>
                <div class="p-[16px_20px] border-t border-light flex justify-end gap-2 bg-light rounded-b-[18px]">
                    <button @click="showAddModal = false" class="px-4 py-2 bg-white border border-light rounded-lg text-[0.75rem] font-bold text-gray hover:text-dark transition-colors">Cancel</button>
                    <button @click="submitNewProduct" :disabled="isCreating" class="px-5 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg text-[0.75rem] font-bold flex items-center gap-1.5 hover:-translate-y-px transition-transform shadow-sm disabled:opacity-50">
                        <i class="fa-solid fa-spinner fa-spin" v-if="isCreating"></i>
                        <i class="fa-solid fa-check" v-else></i> Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const isLoading = ref(true);
const isSaving = ref(false);
const isCreating = ref(false);
const showAddModal = ref(false);

const products = ref([]);

const newProduct = ref({
    brand: '',
    name: '',
    cost_price: '',
    selling_price: '',
    stock_quantity: ''
});

const getMargin = (product) => {
    const cost = parseFloat(product.cost_price) || 0;
    const sell = parseFloat(product.selling_price) || 0;
    return sell - cost;
};

const fetchProducts = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/api/products');
        
        const data = response.data.data || response.data || [];
        products.value = data.map(p => ({
            ...p,
            cost_price: parseFloat(p.cost_price).toFixed(2),
            selling_price: parseFloat(p.selling_price).toFixed(2),
            stock_quantity: parseInt(p.stock_quantity)
        }));
    } catch (error) {
        console.error("Error fetching products:", error);
    } finally {
        isLoading.value = false;
    }
};

const saveInventory = async () => {
    isSaving.value = true;
    try {
        const payload = products.value.map(p => ({
            id: p.id,
            cost_price: parseFloat(p.cost_price),
            selling_price: parseFloat(p.selling_price),
            stock_quantity: parseInt(p.stock_quantity)
        }));

        await axios.post('/api/products/bulk-update', { products: payload });
        alert("Inventory updated successfully!");
        await fetchProducts();
    } catch (error) {
        console.error("Error saving inventory:", error);
        alert("Failed to save updates. Please try again.");
    } finally {
        isSaving.value = false;
    }
};

const submitNewProduct = async () => {
    if (!newProduct.value.brand || !newProduct.value.name || !newProduct.value.selling_price) {
        return alert("Please fill in at least the Brand, Name, and Selling Price.");
    }

    isCreating.value = true;
    try {
        await axios.post('/api/products', {
            brand: newProduct.value.brand,
            name: newProduct.value.name,
            cost_price: parseFloat(newProduct.value.cost_price) || 0,
            selling_price: parseFloat(newProduct.value.selling_price) || 0,
            stock_quantity: parseInt(newProduct.value.stock_quantity) || 0
        });

        alert("Product added successfully!");
        
        newProduct.value = { brand: '', name: '', cost_price: '', selling_price: '', stock_quantity: '' };
        showAddModal.value = false;
        
        await fetchProducts();
    } catch (error) {
        console.error("Error adding product:", error);
        alert("Failed to add product. Please check your inputs.");
    } finally {
        isCreating.value = false;
    }
};

// NEW: Delete Product Logic
const deleteProduct = async (id) => {
    if (!confirm("Are you sure you want to remove this product from inventory? This cannot be undone.")) {
        return;
    }

    try {
        await axios.delete(`/api/products/${id}`);
        // Remove it from the local array immediately for a snappy UI
        products.value = products.value.filter(p => p.id !== id);
    } catch (error) {
        console.error("Error deleting product:", error);
        alert("Failed to delete product. It might be tied to existing sales records.");
    }
};

onMounted(() => {
    fetchProducts();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
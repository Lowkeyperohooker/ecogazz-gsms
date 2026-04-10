<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full relative">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Staff Management</h3>
            
            <button @click="openAddModal" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-user-plus"></i> Add
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto no-scrollbar">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10 bg-light">
                    <tr>
                        <th class="text-left p-3 text-gray text-[0.65rem] font-bold uppercase tracking-wide border-b border-light">Name</th>
                        <th class="text-left p-3 text-gray text-[0.65rem] font-bold uppercase tracking-wide border-b border-light">Role</th>
                        <th class="p-3 text-gray text-[0.65rem] font-bold uppercase tracking-wide border-b border-light text-center">PIN</th>
                        <th class="text-center p-3 text-gray text-[0.65rem] font-bold uppercase tracking-wide border-b border-light">Status</th>
                        <th class="text-center p-3 text-gray text-[0.65rem] font-bold uppercase tracking-wide border-b border-light">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="employees.length === 0">
                        <td colspan="5" class="text-center p-8 text-gray italic text-xs">No staff found.</td>
                    </tr>
                    <tr v-for="emp in employees" :key="emp.id" class="border-b border-light hover:bg-[#f8faf9] transition-colors">
                        <td class="p-3 text-xs font-bold text-dark">{{ emp.name || emp.n }}</td>
                        <td class="p-3 text-[0.7rem] font-bold uppercase text-gray">{{ emp.role || emp.r }}</td>
                        <td class="p-3 text-xs text-gray tracking-[2px] text-center"><i class="fa-solid fa-circle text-[0.4rem] mr-0.5"></i><i class="fa-solid fa-circle text-[0.4rem] mr-0.5"></i><i class="fa-solid fa-circle text-[0.4rem] mr-0.5"></i><i class="fa-solid fa-circle text-[0.4rem]"></i></td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[0.52rem] font-bold uppercase tracking-wide bg-success/10 border border-success/20 text-success">
                                Active
                            </span>
                        </td>
                        <td class="p-3 text-center flex justify-center gap-1.5">
                            <button @click="openEditModal(emp)" class="px-3 py-1.5 bg-white border border-light text-dark rounded-md text-[0.65rem] font-bold hover:border-primary hover:text-primary transition-colors shadow-sm">
                                Edit
                            </button>
                            <button @click="deleteStaff(emp.id)" class="px-2 py-1 text-danger hover:bg-danger/10 rounded-md transition-colors" title="Delete">
                                <i class="fa-solid fa-trash text-[0.7rem]"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AddStaffModal 
            v-if="showModal" 
            :staff-data="selectedStaff"
            @close="showModal = false" 
            @saved="handleRecordSaved" 
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AddStaffModal from './AddStaffModal.vue';

const showModal = ref(false);
const employees = ref([]);
const selectedStaff = ref(null); // Will hold data if editing, null if adding

const fetchEmployees = async () => {
    try {
        const response = await axios.get('/api/users');
        employees.value = response.data.data || response.data || [];
    } catch (error) {
        console.error("Error fetching employees:", error);
    }
};

const openAddModal = () => {
    selectedStaff.value = null; // Tell modal we are adding
    showModal.value = true;
};

const openEditModal = (emp) => {
    selectedStaff.value = emp;  // Tell modal we are editing this user
    showModal.value = true;
};

const handleRecordSaved = () => {
    showModal.value = false;
    fetchEmployees(); // Always refresh the list from the server to ensure accuracy
};

const deleteStaff = async (id) => {
    if (!confirm("Are you sure you want to remove this staff member? This cannot be undone.")) return;

    try {
        await axios.delete(`/api/users/${id}`);
        // Remove from UI
        employees.value = employees.value.filter(s => s.id !== id);
    } catch (error) {
        console.error("Error deleting staff:", error);
        alert("Failed to delete staff member.");
    }
};

onMounted(() => {
    fetchEmployees();
});
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full relative">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Staff Management</h3>
            
            <button @click="showAddModal = true" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
                <i class="fa-solid fa-user-plus"></i> Add
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10 bg-light">
                    <tr>
                        <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">Name</th>
                        <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">Role</th>
                        <th class="text-left p-2 text-gray text-xs font-bold uppercase tracking-wide">PIN</th>
                        <th class="text-center p-2 text-gray text-xs font-bold uppercase tracking-wide">Status</th>
                        <th class="text-center p-2 text-gray text-xs font-bold uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="emp in employees" :key="emp.id" class="border-b border-light hover:bg-[#f8faf9] transition-colors">
                        <td class="p-2 text-xs font-bold">{{ emp.n || emp.name }}</td>
                        <td class="p-2 text-xs">{{ emp.r || emp.role }}</td>
                        <td class="p-2 text-xs text-gray tracking-[2px]">● ● ● ●</td>
                        <td class="p-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[0.52rem] font-bold uppercase tracking-wide bg-primary-light text-primary">
                                Active
                            </span>
                        </td>
                        <td class="p-2 text-center">
                            <button @click="editStaff(emp)" class="px-3 py-1 bg-card border-2 border-light text-dark rounded-md text-[0.58rem] font-bold hover:bg-light transition-colors">
                                Edit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <AddStaffModal 
            v-if="showAddModal" 
            @close="showAddModal = false" 
            @saved="handleRecordSaved" 
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AddStaffModal from './AddStaffModal.vue';

const showAddModal = ref(false);
const employees = ref([]);

// Fetch users from Laravel
const fetchEmployees = async () => {
    try {
        // Fetch users (You may want to create a specific endpoint for this, e.g., /api/users)
        const response = await axios.get('/api/users');
        employees.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error fetching employees:", error);
    }
};

const handleRecordSaved = (newRecord) => {
    showAddModal.value = false;
    // Add new user to the UI without reloading
    employees.value.push(newRecord);
};

const editStaff = (emp) => {
    alert(`Editing ${emp.name}... feature coming soon!`);
};

onMounted(() => {
    fetchEmployees();
});
</script>
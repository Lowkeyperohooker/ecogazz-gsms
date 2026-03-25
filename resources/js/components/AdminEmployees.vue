<template>
    <div class="flex flex-col bg-card rounded-xl shadow-sm border border-light overflow-hidden h-full">
        <div class="flex justify-between items-center p-[12px_16px] border-b border-light shrink-0">
            <h3 class="text-[0.85rem] font-bold">Staff Management</h3>
            <button @click="addStaff" class="px-3 py-1.5 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg font-bold text-xs flex items-center gap-1.5 transition-transform hover:-translate-y-px shadow-sm">
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
                        <td class="p-2 text-xs font-bold">{{ emp.n }}</td>
                        <td class="p-2 text-xs">{{ emp.r }}</td>
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
    </div>
</template>

<script setup>
import { ref } from 'vue';

const employees = ref([
    { id: 1, n: "DODONG", r: "Pump Attendant" },
    { id: 2, n: "KENNETH", r: "Pump Attendant" },
    { id: 3, n: "FRANCIS", r: "Pump Attendant" },
    { id: 4, n: "ROXAS", r: "Pump Attendant" },
    { id: 5, n: "BRYAN BACOY", r: "Manager" }
]);

const addStaff = () => {
    alert('Add staff feature coming soon!');
};

const editStaff = (emp) => {
    alert(`Editing ${emp.n}... feature coming soon!`);
};
</script>
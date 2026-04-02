<template>
    <div class="w-52.5 bg-card border-r border-light flex flex-col shrink-0 p-[14px_10px_10px] z-60 transition-all duration-250">
        <div class="text-center mb-4 pb-3 border-b border-light">
            <div class="w-9.5 h-9.5 bg-linear-to-br from-primary to-primary-hover rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-[0_4px_12px_rgba(61,187,145,0.25)]">
                <i class="fa-solid fa-gas-pump text-white text-[0.9rem]"></i>
            </div>
            <h2 class="text-[0.95rem] font-extrabold tracking-[-0.3px] text-dark">ECOGAZZ</h2>
            <span class="text-[0.58rem] text-gray block mt-px font-semibold tracking-[1px]">
                {{ role === 'admin' ? 'ADMIN CONSOLE' : 'STAFF TERMINAL' }}
            </span>
        </div>

        <div class="flex-1 overflow-y-auto flex flex-col gap-0.5">
            <button 
                v-for="item in currentMenu" 
                :key="item.id"
                @click="navigate(item.id)"
                :class="[
                    'flex items-center gap-2.5 p-[9px_12px] rounded-lg font-medium text-[0.74rem] text-left w-full transition-all duration-250 group overflow-hidden',
                    activePage === item.id 
                        ? 'bg-linear-to-br from-primary to-primary-hover text-white shadow-[0_4px_15px_rgba(61,187,145,0.3)]' 
                        : 'text-dark hover:bg-light hover:translate-x-0.5'
                ]"
            >
                <i :class="[
                    'fa-solid w-4.5 text-center transition-colors', 
                    item.icon,
                    activePage === item.id ? 'text-white' : 'text-gray'
                ]"></i>
                <span class="flex-1">{{ item.label }}</span>
                
                <span v-if="item.id === 'admin-reviews'" class="ml-auto bg-danger text-white text-[0.5rem] font-bold py-px px-1.5 rounded-[10px] min-w-4 text-center">
                    2
                </span>
            </button>
        </div>

        <div class="flex items-center gap-2 p-[12px_8px_4px] border-t border-light mt-1.5">
            <div class="w-8.5 h-8.5 bg-linear-to-br from-[#fce4b3] to-warning rounded-full flex items-center justify-center text-[#d19a22] text-[0.85rem] shrink-0 font-bold shadow-[0_2px_8px_rgba(245,166,35,0.2)] uppercase">
                {{ userName ? userName.charAt(0) : (role === 'admin' ? 'A' : 'S') }}
            </div>
            
            <div class="flex-1 overflow-hidden">
                <p class="text-[0.55rem] text-gray font-medium truncate uppercase tracking-[0.5px]">
                    {{ role === 'admin' ? 'Admin' : 'Gasman' }}
                </p>
                <h4 class="text-[0.72rem] font-bold text-dark truncate">
                    {{ userName || 'Staff' }}
                </h4>
            </div>
            
            <button @click="$emit('logout')" class="text-danger cursor-pointer p-1.5 bg-transparent border-none text-[0.85rem] transition-all rounded-lg hover:bg-danger/10 hover:scale-110">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    role: String,
    activePage: String,
    userName: String // <-- Added this to accept the login name from App.vue
});

const emit = defineEmits(['navigate', 'logout']);

const menus = {
    staff: [
        { id: 'staff-pos', label: 'GAS POS', icon: 'fa-cash-register' },
        { id: 'staff-pumps', label: 'Pump Readings', icon: 'fa-gauge-high' },
        { id: 'staff-history', label: 'History', icon: 'fa-clock-rotate-left' }
    ],
    admin: [
        { id: 'admin-dashboard', label: 'Dashboard', icon: 'fa-chart-pie' },
        { id: 'admin-reviews', label: 'Shifts', icon: 'fa-clock' },
        { id: 'admin-audit', label: 'Audit', icon: 'fa-file-lines' },
        { id: 'admin-pumps', label: 'Pumps', icon: 'fa-gauge-high' },
        { id: 'admin-inventory', label: 'Inventory', icon: 'fa-boxes-stacked' },
        { id: 'admin-purchasing', label: 'P.O.', icon: 'fa-file-invoice' },
        { id: 'admin-employees', label: 'Staff', icon: 'fa-users' }
    ]
};

const currentMenu = computed(() => menus[props.role] || []);

const navigate = (pageId) => {
    emit('navigate', pageId);
};
</script>
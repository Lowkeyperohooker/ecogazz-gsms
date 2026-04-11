<template>
    <div class="w-full h-full">
        
        <router-view v-if="$route.name === 'login'" @login="handleLogin"></router-view>

        <div v-else class="flex w-full h-screen bg-card overflow-hidden">
            
            <Sidebar 
                :role="role" 
                :activePage="$route.name" 
                :userName="userName" 
                @navigate="goToPage" 
                @logout="handleLogout"
            />

            <div class="flex-1 flex flex-col bg-bg overflow-hidden min-w-0">
                
                <div class="flex justify-between items-center px-5 pt-3 shrink-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold tracking-tight">{{ pageTitle }}</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="bg-dark text-white px-3 py-1 rounded-full text-xs font-semibold tabular-nums">
                            {{ currentTime }}
                        </span>
                        <span class="font-medium text-gray text-xs">{{ currentDate }}</span>
                    </div>
                </div>

                <div class="flex-1 overflow-hidden p-3 flex flex-col">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

// Only import the Sidebar. The router handles all the other components!
import Sidebar from './Sidebar.vue';

const router = useRouter();
const route = useRoute();

// Pull state from localStorage so it survives browser refreshes
const role = ref(localStorage.getItem('user_role') || '');
const userName = ref(localStorage.getItem('user_name') || ''); 
const currentTime = ref('');
const currentDate = ref('');

// Computed Page Title mapping (Uses the router name)
const pageTitle = computed(() => {
    const titles = {
        'staff-pos': 'GAS POS System',
        'staff-pumps': 'Pump Readings',
        'staff-history': 'Sales History',
        'admin-dashboard': 'Executive Dashboard',
        'admin-reviews': 'Shift Management',
        'admin-audit': 'Sales Audit',
        'admin-pumps': 'Pump Control',
        'admin-inventory': 'Pricing & Margins',
        'admin-purchasing': 'Purchase Orders',
        'admin-employees': 'Staff Management'
    };
    return titles[route.name] || 'ECOGAZZ System';
});

// Clock Logic
let timer;
const updateClock = () => {
    const d = new Date();
    currentDate.value = d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    currentTime.value = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

onMounted(() => {
    updateClock();
    timer = setInterval(updateClock, 1000);

    // Basic protection: If not logged in and not on the login page, redirect to login
    if (route.name !== 'login' && !localStorage.getItem('auth_token')) {
        router.push({ name: 'login' });
    }
});

onUnmounted(() => {
    clearInterval(timer);
});

// Handle Navigation from the Sidebar
const goToPage = (pageName) => {
    router.push({ name: pageName });
};

// Handle Login Success
const handleLogin = (payload) => {
    role.value = payload.role;
    userName.value = payload.name; 
    
    // Save to local storage for persistence across reloads
    localStorage.setItem('user_role', payload.role);
    localStorage.setItem('user_name', payload.name);
    
    // Redirect via Vue Router
    if (payload.role === 'admin') {
        router.push({ name: 'admin-dashboard' });
    } else {
        router.push({ name: 'staff-pos' });
    }
};

// Handle Logout
const handleLogout = async () => {
    try {
        await axios.post('/api/logout');
    } catch (error) {
        console.error("Error logging out:", error);
    } finally {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_role');
        localStorage.removeItem('user_name');
        delete axios.defaults.headers.common['Authorization'];
        
        role.value = '';
        userName.value = '';
        
        // Push user back to the login URL
        router.push({ name: 'login' });
    }
};
</script>
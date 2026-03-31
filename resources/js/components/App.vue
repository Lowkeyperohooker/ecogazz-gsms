<template>
    <div class="w-full h-full">
        <LoginScreen v-if="!isLoggedIn" @login="handleLogin" />

        <div v-else class="flex w-full h-screen bg-card overflow-hidden">
            
            <Sidebar 
                :role="role" 
                :activePage="activePage" 
                @navigate="activePage = $event" 
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
                    
                    <StaffPos v-if="activePage === 'staff-pos'" />
                    <StaffPumps v-if="activePage === 'staff-pumps'" />
                    <StaffHistory v-if="activePage === 'staff-history'" />

                    <AdminDashboard v-if="activePage === 'admin-dashboard'" />
                    <AdminReviews v-if="activePage === 'admin-reviews'" />
                    <AdminAudit v-if="activePage === 'admin-audit'" />
                    <AdminPumps v-if="activePage === 'admin-pumps'" />
                    <AdminInventory v-if="activePage === 'admin-inventory'" />
                    <AdminPurchasing v-if="activePage === 'admin-purchasing'" />
                    <AdminEmployees v-if="activePage === 'admin-employees'" />

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios'; // <-- Added Axios import

// Global Shell Components
import LoginScreen from './LoginScreen.vue';
import Sidebar from './Sidebar.vue';

// Staff Components
import StaffPos from './StaffPos.vue';
import StaffPumps from './StaffPumps.vue';
import StaffHistory from './StaffHistory.vue';

// Admin Components
import AdminDashboard from './AdminDashboard.vue';
import AdminReviews from './AdminReviews.vue';
import AdminAudit from './AdminAudit.vue';
import AdminPumps from './AdminPumps.vue';
import AdminInventory from './AdminInventory.vue';
import AdminPurchasing from './AdminPurchasing.vue';
import AdminEmployees from './AdminEmployees.vue';

// Application State
const isLoggedIn = ref(false);
const role = ref('');
const activePage = ref('');
const currentTime = ref('');
const currentDate = ref('');

// Computed Page Title mapping
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
    return titles[activePage.value] || 'Dashboard';
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
});

onUnmounted(() => {
    clearInterval(timer);
});

// Authentication Actions
const handleLogin = (selectedRole) => {
    role.value = selectedRole;
    isLoggedIn.value = true;
    activePage.value = selectedRole === 'admin' ? 'admin-dashboard' : 'staff-pos';
};

// <-- Updated Logout Logic using Axios -->
const handleLogout = async () => {
    try {
        // Tell Laravel to destroy the token in the database
        await axios.post('/api/logout');
    } catch (error) {
        console.error("Error logging out:", error);
    } finally {
        // Clear frontend state regardless of API success/failure
        localStorage.removeItem('auth_token');
        delete axios.defaults.headers.common['Authorization'];
        
        isLoggedIn.value = false;
        role.value = '';
        activePage.value = '';
    }
};
</script>
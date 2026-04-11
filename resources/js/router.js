import { createRouter, createWebHistory } from 'vue-router';

// 1. Core/Auth Components
import LoginScreen from '../pages/components/LoginScreen.vue';

// 2. Staff Components
import StaffPos from '../pages/components/StaffPos.vue';
import StaffHistory from '../pages/components/StaffHistory.vue';
import StaffPumps from '../pages/components/StaffPumps.vue';

// 3. Admin Components
import AdminDashboard from '../pages/components/AdminDashboard.vue';
import AdminEmployees from '../pages/components/AdminEmployees.vue';
import AdminAudit from '../pages/components/AdminAudit.vue';
import AdminPumps from '../pages/components/AdminPumps.vue';
import AdminInventory from '../pages/components/AdminInventory.vue';
import AdminPurchasing from '../pages/components/AdminPurchasing.vue';
import AdminReviews from '../pages/components/AdminReviews.vue';

const routes = [
    // Default redirect to login
    { path: '/', redirect: '/login' },
    
    // Login Route
    { path: '/login', component: LoginScreen, name: 'login' },

    // === STAFF ROUTES ===
    { path: '/staff/pos', component: StaffPos, name: 'staff-pos' },
    { path: '/staff/history', component: StaffHistory, name: 'staff-history' },
    { path: '/staff/pumps', component: StaffPumps, name: 'staff-pumps' },

    // === ADMIN ROUTES ===
    { path: '/admin/dashboard', component: AdminDashboard, name: 'admin-dashboard' },
    { path: '/admin/staff', component: AdminEmployees, name: 'admin-employees' },
    { path: '/admin/audit', component: AdminAudit, name: 'admin-audit' },
    { path: '/admin/pumps', component: AdminPumps, name: 'admin-pumps' },
    { path: '/admin/inventory', component: AdminInventory, name: 'admin-inventory' },
    { path: '/admin/purchasing', component: AdminPurchasing, name: 'admin-purchasing' },
    { path: '/admin/reviews', component: AdminReviews, name: 'admin-reviews' },
];

const router = createRouter({
    // createWebHistory gives you clean URLs without the '#' symbol
    history: createWebHistory(),
    routes,
});

export default router;
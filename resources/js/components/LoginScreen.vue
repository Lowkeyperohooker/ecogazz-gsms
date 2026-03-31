<template>
    <div class="fixed inset-0 bg-linear-to-br from-dark via-[#2d4a3e] to-dark flex justify-center items-center z-9000 p-5">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_40%,rgba(61,187,145,0.15)_0%,transparent_60%),radial-gradient(circle_at_70%_70%,rgba(91,124,250,0.1)_0%,transparent_50%)]"></div>
        
        <div class="bg-white/97 backdrop-blur-xl py-10 px-9 rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.2)] text-center w-full max-w-95 relative z-10 animate-[scaleIn_0.5s_ease-out]">
            <div class="w-17.5 h-17.5 bg-linear-to-br from-primary to-primary-hover rounded-[20px] flex items-center justify-center mx-auto mb-4 shadow-[0_8px_25px_rgba(61,187,145,0.3)]">
                <i class="fa-solid fa-gas-pump text-[1.8rem] text-white"></i>
            </div>
            
            <h1 class="text-[1.4rem] font-extrabold tracking-[-0.5px] text-dark mb-1">EcoGazz GSMS</h1>
            <div class="text-gray text-[0.78rem] font-medium mb-7">Kimaya Station Management</div>
            
            <input 
                type="password" 
                v-model="pin" 
                placeholder="● ● ● ●" 
                @keydown.enter="attemptLogin('staff')"
                class="w-full p-3.5 mb-4 border-2 border-[#e8ebe9] rounded-xl text-[0.95rem] text-center tracking-[4px] font-bold bg-[#fafbfa] transition-all focus:outline-none focus:border-primary focus:bg-white focus:ring-4 focus:ring-primary/15"
            >
            
            <div class="flex gap-2.5">
                <button @click="attemptLogin('admin')" :disabled="isLoggingIn" class="flex-1 p-3.5 rounded-xl text-[0.85rem] font-bold flex justify-center items-center gap-2 transition-all hover:-translate-y-0.5 shadow-md bg-dark text-white active:translate-y-0 disabled:opacity-70">
                    <i class="fa-solid fa-shield-halved"></i> Admin
                </button>
                <button @click="attemptLogin('staff')" :disabled="isLoggingIn" class="flex-1 p-3.5 rounded-xl text-[0.85rem] font-bold flex justify-center items-center gap-2 transition-all hover:-translate-y-0.5 shadow-md bg-linear-to-br from-primary to-primary-hover text-white active:translate-y-0 disabled:opacity-70">
                    <i class="fa-solid fa-user"></i> Staff
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['login']);
const pin = ref('');
const isLoggingIn = ref(false);

const attemptLogin = async (requestedRole) => {
    if (!pin.value || pin.value.length !== 4) {
        alert('Please enter a valid 4-digit PIN');
        return;
    }

    try {
        isLoggingIn.value = true;

        const response = await axios.post('/api/login', {
            pin: pin.value
        });

        const { token, role, user } = response.data;

        localStorage.setItem('auth_token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        if (requestedRole === 'admin' && role !== 'admin') {
            alert('Access Denied: You do not have Manager privileges.');
            localStorage.removeItem('auth_token');
            return;
        }

        emit('login', role);

    } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || 'Invalid PIN or server error.');
        pin.value = ''; 
    } finally {
        isLoggingIn.value = false;
    }
};
</script>

<style scoped>
@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.92); }
    to { opacity: 1; transform: scale(1); }
}
</style>
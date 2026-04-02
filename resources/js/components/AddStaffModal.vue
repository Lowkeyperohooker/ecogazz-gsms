<template>
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-8000 flex justify-center items-center p-4">
        <div class="bg-card w-full max-w-md rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col animate-[scaleIn_0.3s_ease-out]">
            
            <div class="p-4 border-b border-light flex justify-between items-center shrink-0">
                <h3 class="font-extrabold text-lg text-dark">Add New Staff</h3>
                <button @click="$emit('close')" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-light transition-colors">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="p-5 flex flex-col gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray uppercase tracking-wide mb-1.5">Full Name</label>
                    <input 
                        type="text" 
                        v-model="form.name" 
                        placeholder="Juan Dela Cruz" 
                        class="p-2.5 border-2 border-light rounded-lg text-sm font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray uppercase tracking-wide mb-1.5">Role</label>
                    <select 
                        v-model="form.role" 
                        class="w-full p-2.5 border border-light rounded-lg text-sm bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all"
                        required
                    >
                        <option value="" disabled>Select a role...</option>
                        <option value="admin">admin</option>
                        <option value="gasman">gasman</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-xs font-bold text-gray uppercase tracking-wide mb-1.5">4-Digit PIN Code</label>
                    <input 
                        type="password" 
                        v-model="form.pin" 
                        placeholder="••••" 
                        maxlength="4" 
                        class="p-2.5 border-2 border-light rounded-lg text-sm font-bold tracking-[6px] text-center text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div v-if="errorMessage" class="text-xs font-bold text-danger bg-[#fef0f0] p-2 rounded-md text-center border border-danger/20">
                    {{ errorMessage }}
                </div>
            </div>

            <div class="p-4 border-t border-light flex justify-end gap-2 shrink-0">
                <button @click="$emit('close')" class="px-4 py-2 bg-card border-2 border-light rounded-lg text-xs font-bold text-dark hover:bg-light transition-colors">
                    Cancel
                </button>
                <button 
                    @click="submitRecord" 
                    :disabled="isSubmitting" 
                    class="px-5 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg text-xs font-bold flex items-center gap-1.5 hover:-translate-y-px transition-transform shadow-sm disabled:opacity-60 disabled:hover:translate-y-0 disabled:cursor-not-allowed"
                >
                    <i class="fa-solid fa-spinner fa-spin" v-if="isSubmitting"></i>
                    <i class="fa-solid fa-save" v-else></i>
                    {{ isSubmitting ? 'Saving...' : 'Save Record' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close', 'saved']);

const isSubmitting = ref(false);
const errorMessage = ref('');

const form = reactive({
    name: '',
    role: 'Pump Attendant',
    pin: ''
});

const submitRecord = async () => {
    if (!form.name || !form.pin) {
        errorMessage.value = 'Please fill out all required fields.';
        return;
    }
    
    if (form.pin.length !== 4) {
        errorMessage.value = 'PIN must be exactly 4 digits.';
        return;
    }

    try {
        isSubmitting.value = true;
        errorMessage.value = '';

        const response = await axios.post('/api/users', form);
        emit('saved', response.data);
        
    } catch (error) {
        console.error('Submission failed:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to save record. Please try again.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>
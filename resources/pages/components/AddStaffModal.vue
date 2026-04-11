<template>
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-8000 flex justify-center items-center p-4">
        <div class="bg-card w-full max-w-md rounded-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.1)] flex flex-col animate-[scaleIn_0.2s_ease-out]">
            
            <div class="p-4 border-b border-light flex justify-between items-center shrink-0 bg-[#fcfdfd] rounded-t-[18px]">
                <h3 class="font-extrabold text-[1.05rem] text-dark flex items-center gap-2">
                    <i :class="isEditing ? 'fa-solid fa-user-pen text-primary' : 'fa-solid fa-user-plus text-primary'"></i> 
                    {{ isEditing ? 'Edit Staff Member' : 'Add New Staff' }}
                </h3>
                <button @click="$emit('close')" class="w-7 h-7 rounded-lg border-2 border-light text-dark flex items-center justify-center hover:bg-danger hover:border-danger hover:text-white transition-colors">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <div class="p-5 flex flex-col gap-4">
                <div class="flex flex-col">
                    <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px] mb-1.5">Full Name</label>
                    <input 
                        type="text" 
                        v-model="form.name" 
                        placeholder="Juan Dela Cruz" 
                        class="p-2.5 border-2 border-light rounded-lg text-sm font-bold text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div class="flex flex-col">
                    <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px] mb-1.5">Role</label>
                    <select 
                        v-model="form.role" 
                        class="w-full p-2.5 border-2 border-light rounded-lg text-sm bg-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all cursor-pointer font-bold text-dark"
                    >
                        <option value="" disabled selected>Select a role...</option>
                        <option value="admin">admin</option>
                        <option value="gasman">gasman</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-[0.65rem] font-bold text-gray uppercase tracking-[0.5px] mb-1.5 flex justify-between items-end">
                        Password
                        <span v-if="isEditing" class="text-[0.55rem] font-medium text-warning normal-case">(Leave blank to keep current)</span>
                    </label>
                    <input 
                        type="password" 
                        v-model="form.pin" 
                        placeholder="••••••••" 
                        class="p-2.5 border-2 border-light rounded-lg text-[1rem] font-bold tracking-widest text-center text-dark transition-all focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>

                <div v-if="errorMessage" class="text-[0.7rem] font-bold text-danger bg-[#fef0f0] p-3 rounded-lg text-center border border-danger/20 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ errorMessage }}
                </div>
            </div>

            <div class="p-4 border-t border-light flex justify-end gap-2 shrink-0 bg-light rounded-b-[18px]">
                <button @click="$emit('close')" class="px-5 py-2 bg-white border border-light rounded-lg text-[0.75rem] font-bold text-gray hover:text-dark transition-colors shadow-sm">
                    Cancel
                </button>
                <button 
                    @click="submitRecord" 
                    :disabled="isSubmitting" 
                    class="px-6 py-2 bg-linear-to-br from-primary to-primary-hover text-white rounded-lg text-[0.75rem] font-bold flex items-center gap-1.5 hover:-translate-y-px transition-transform shadow-[0_4px_12px_rgba(61,187,145,0.3)] disabled:opacity-60 disabled:hover:translate-y-0 disabled:cursor-not-allowed"
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
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    staffData: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'saved']);

const isSubmitting = ref(false);
const errorMessage = ref('');

const form = ref({
    name: '',
    role: '', 
    pin: ''
});

const isEditing = computed(() => props.staffData !== null);

onMounted(() => {
    if (props.staffData) {
        form.value = {
            name: props.staffData.name || props.staffData.n,
            role: props.staffData.role || props.staffData.r,
            pin: '' 
        };
    }
});

const submitRecord = async () => {
    errorMessage.value = '';

    if (!form.value.name || form.value.name.trim() === '') {
        errorMessage.value = 'Full Name is required.';
        return;
    }
    
    if (!form.value.role || form.value.role === '') {
        errorMessage.value = 'You must select a Role from the dropdown.';
        return;
    }

    // Checking minimum length instead of strict 4
    if (!isEditing.value && (!form.value.pin || form.value.pin.length < 4)) {
        errorMessage.value = 'A password (min 4 characters) is required for new staff.';
        return;
    }
    
    if (isEditing.value && form.value.pin.length > 0 && form.value.pin.length < 4) {
        errorMessage.value = 'If changing the password, it must be at least 4 characters.';
        return;
    }

    try {
        isSubmitting.value = true;
        errorMessage.value = '';

        let response;
        if (isEditing.value) {
            response = await axios.put(`/api/users/${props.staffData.id}`, form.value);
        } else {
            response = await axios.post('/api/users', form.value);
        }

        emit('saved', response.data);
        
    } catch (error) {
        console.error('Submission failed:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to save record. Please check your inputs.';
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
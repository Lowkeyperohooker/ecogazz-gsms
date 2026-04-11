import './bootstrap';
import { createApp } from 'vue';
import App from '../pages/components/App.vue'; 
import router from './router'; 
import axios from 'axios';

// === THE FIX: Restore the token if the user refreshes the page ===
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App);

app.use(router); 

app.mount('#app');
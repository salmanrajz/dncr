import {
    createApp
} from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios'; // Import axios
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';

// Set axios globally
const app = createApp(App);

// You can set the default base URL for Axios (optional)
axios.defaults.baseURL = 'http://localhost:8000';

// Attach axios to the global instance
app.config.globalProperties.$axios = axios;

// Use the router
app.use(router);

// Mount the Vue app
app.mount('#app');

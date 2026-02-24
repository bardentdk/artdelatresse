import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Permet de gérer les cookies de session Laravel automatiquement
window.axios.defaults.withCredentials = true;
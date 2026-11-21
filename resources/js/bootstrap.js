import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Request Interceptor: Otomatis sisipkan Bearer Token dari localStorage jika ada
window.axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('vonic_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

// Response Interceptor: Tangani 401 Unauthorized secara global
window.axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response && error.response.status === 401) {
        // Jangan redirect otomatis jika sedang dalam tahap preview / pengembangan
        const isLoginEndpoint = error.config && error.config.url && error.config.url.includes('/api/login');
        if (!isLoginEndpoint) {
            localStorage.removeItem('vonic_token');
            localStorage.removeItem('vonic_user');
            // Dinonaktifkan sementara agar tidak melempar user saat preview/testing tanpa login
            console.warn('Unauthorized (401) terdeteksi. Auto-redirect dinonaktifkan untuk tahap testing.');
            // window.location.href = '/login?expired=1';
        }
    }
    return Promise.reject(error);
});

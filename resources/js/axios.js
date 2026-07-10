import axios from 'axios';

// axios.defaults.withCredentials = true;
// axios.defaults.withXSRFToken = true;
// axios.defaults.baseURL = 'http://localhost:8000'; // URL вашего Laravel API


axios.defaults.baseURL = 'http://127.0.0.1:8000';

axios.interceptors.request.use(config => {

    const token = localStorage.getItem('user_token');

    config.headers['Accept'] = 'application/json';

    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }

    return config;

}, error => {
    return Promise.reject(error);
});

export default axios;


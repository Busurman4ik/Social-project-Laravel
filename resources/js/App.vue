<template>

     <div class="min-h-screen bg-slate-50 text-slate-800">
         <div class="sticky top-0 z-50 w-full bg-white/70 backdrop-blur-md border-b border-slate-200/50 shadow-sm transition-all duration-300">
             <nav class="max-w-[700px] mx-auto px-6 py-4 flex items-center justify-between">
                 <RouterLink v-if="!token" to="/user/login" class="mr-10">Login</RouterLink>
                 <RouterLink v-if="!token"  to="/user/registration" >Registration</RouterLink>
                 <RouterLink v-if="token" to="/user/personal" class="mr-3 float-left">Personal</RouterLink>
                 <div class="float-right flex">
                     <p v-if="user" class="mr-4 text-sm text-slate-500">{{ user.name }}</p>
                     <a v-if="token" href="#" @click.prevent="handleLogout" class="">Logout</a>
                 </div>
             </nav>
         </div>

        <main class="p-6 max-w-[700px] mx-auto">
            <RouterView />
        </main>

     </div>
</template>

<script setup>
import {onMounted,  ref, watch} from 'vue';
import {useRouter, useRoute} from "vue-router";
import axios from "./axios.js";

const router = useRouter();
const route = useRoute();
const user = ref(null);
const token = ref(null);


const getToken = () => {
    token.value = localStorage.getItem('user_token')
}

const getUserData = async () => {
    if (!token.value) {
        user.value = null;
        return;
    }

    try {
        const response = await axios.get('/api/personal');
        user.value = response.data.data || response.data;
    } catch (error) {
        if (error.response?.status === 401) {
            handleLocalLogout();
        }
    }
};

onMounted(() => {
    getToken();
    getUserData();
});

watch(
    () => route.path,
    () => {
        getToken();
        getUserData();
    },
    { immediate: true } // immediate: true выполнит проверку ОДИН раз сразу при загрузке сайта [1]
)

const handleLocalLogout = () => {
    localStorage.removeItem('user_token');
    token.value = null;
    user.value = null;
};


const handleLogout = async () => {
    try {
        await axios.post('/api/logout');
    } catch (error) {
        console.error('Ошибка при выходе на сервере', error);
    } finally {
        handleLocalLogout();
        await router.push({name: 'user.login'});
    }
};



</script>


<template>

     <div class="max-w-[700px] mx-auto">
        <nav class="p-6  text-center">
            <RouterLink v-if="!token" to="/user/login" class="mr-10">Login</RouterLink>
            <RouterLink v-if="!token"  to="/user/registration" >Registration</RouterLink>
            <RouterLink v-if="token" to="/user/personal" class="mr-3 float-left">Personal</RouterLink>
            <a v-if="token" href="#" @click.prevent="handleLogout" class="float-right">Logout</a>
        </nav>
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

const token = ref(null);


const getToken = () => {
    token.value = localStorage.getItem('user_token')
}

onMounted(() => {
    getToken();
});

watch(
    () => route.path,
    () => {
        getToken()
    },
    { immediate: true } // immediate: true выполнит проверку ОДИН раз сразу при загрузке сайта [1]
)

const handleLocalLogout = () => {
    localStorage.removeItem('user_token');
};


const handleLogout = async () => {
    try {
        await axios.post('/api/logout');

        handleLocalLogout();

        await router.push({name: 'user.login'});

    } catch (error) {
        handleLocalLogout();
        console.error('Ошибка при выходе', error);
    }
};



if (!token) {
    router.push({name: 'user.login'});
} else {
    router.push({name: 'user.personal'});
}


</script>


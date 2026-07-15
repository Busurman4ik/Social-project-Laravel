<template>
    <div class="w-96 mx-auto">
        <form  @submit.prevent="handleLogin" >
            <div>
                <input v-model="form.email" type="email" placeholder="email" autocomplete="username" class="w-96 p-1 mb-2 border border-inherit rounded-lg">
            </div>
            <div>
                <input v-model="form.password" type="password" placeholder="password" autocomplete="current-password" class="w-96 p-1 mb-2 border border-inherit rounded-lg">
            </div>
            <div >
                <button  type="submit" value="login" class="block  mx-auto w-full p-1 bg-sky-400 text-white rounded-lg cursor-pointer">Login</button>
            </div>
            <div>
                <p>{{ error }}</p>
            </div>
        </form>
    </div>

</template>

<script setup>
import axios from "axios";
import {ref, reactive} from 'vue';
import {useRouter} from "vue-router";

const router = useRouter();

const form = reactive({
    email: '',
    password: ''
});


const error = ref('');


const handleLogin = async () => {
    error.value = '';

    try {

        // Отправляем запрос на логин
        const response = await axios.post('/api/login', {
            email: form.email,
            password: form.password
        });

        localStorage.setItem('user_token', response.data.token);

        await router.push({name: 'user.personal'});
    } catch (e) {
        if (e.response && e.response.status === 422) {
            error.value = 'Неверный email или пароль.';
        } else {
            error.value = 'Произошла ошибка сервера. Попробуйте позже.';
        }
    }
};


</script>

<style scoped>

</style>

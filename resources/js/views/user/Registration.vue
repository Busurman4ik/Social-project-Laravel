


<template>
    <div class="w-96 mx-auto">
        <form @submit.prevent="handleRegister" class="w-96 mx-auto">
            <input v-model="form.name" type="text" placeholder="Name" class="w-96 p-1 mb-2 border border-inherit rounded-lg" required />
            <input v-model="form.email" type="email" placeholder="Email" class="w-96 p-1 mb-2 border border-inherit rounded-lg" required />
            <input v-model="form.password" type="password" placeholder="Password" class="w-96 p-1 mb-2 border border-inherit rounded-lg" required />
            <input v-model="form.password_confirmation" type="password" placeholder="Confirm Password" class="w-96 p-1 mb-2 border border-inherit rounded-lg" required />
            <button type="submit" class="block mx-auto w-full p-1 bg-sky-400 text-white rounded-lg cursor-pointer">Register</button>
        </form>
    </div>
    <div>
        <div v-if="errorMessage" class="w-96 mx-auto">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const errorMessage = ref('');

const handleRegister = async () => {
    errorMessage.value = '';
    try {

        // Запрос регистрации
        const response = await axios.post('/api/registration', form.value);

        const token = response.data.token;
        localStorage.setItem('user_token', token);

        // Редирект в личный кабинет
        await router.push({name: 'user.personal'});
    } catch (error) {
        errorMessage.value = error.response.data.message;
    }
};
</script>

<template>
    <div class="w-96 mx-auto">
        <h1 class="text-center text-xl mb-6">Users</h1>
        <div v-if="users">
            <div v-for="user in users" class="py-2 px-4 mb-4 border-b-1 border-gray-500">
                <p>{{ user.id }}</p>
                <p>{{ user.name }}</p>
                <p>{{ user.email }}</p>
            </div>
        </div>
    </div>

</template>

<script setup>

import axios from "axios";
import {onMounted, ref} from 'vue';

const users = ref([]);

const getUsers = async () => {
    const res = await axios.get('api/users');
    users.value = res.data.data;
}

onMounted(() => {
    getUsers();
});


</script>

<style scoped>

</style>

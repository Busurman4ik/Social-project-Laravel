import { createMemoryHistory, createRouter } from 'vue-router'

import Login from '../views/user/Login.vue'
import Personal from '../views/user/Personal.vue'
import Registration from '../views/user/Registration.vue'
import axios from "axios";

const routes = [
    { path: '/user/login', component: Login, name: 'user.login'},
    { path: '/user/personal', component: Personal, name: 'user.personal'},
    { path: '/user/registration', component: Registration, name: 'user.registration'}
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('user_token');

    if (!token) {
        if (to.name === 'user.login' || to.name === 'user.registration') {
            return next();
        } else {
            return next({
                name: 'user.login'
            });
        }
    }

    if (token && to.name === 'user.login' || to.name === 'user.registration') {
        return next({
            name: 'user.personal'
        });
    }

    next()
})




export default router

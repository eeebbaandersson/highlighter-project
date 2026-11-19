import { createRouter, createWebHashHistory } from 'vue-router'
import HomeView from "../src/views/HomeView.vue";
import TimelineView from "../src/views/TimelineView.vue";

const router = createRouter({
    history: createWebHashHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
        },
        {
            path: '/timeline',
            name: 'timeline',
            component: TimelineView
        }
    ],
})

export default router

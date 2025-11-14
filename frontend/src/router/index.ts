import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/HomePage.vue'
import LoginPage from '@/LoginPage.vue'
import CreateUserPage from '@/CreateUserPage.vue'
import PersonalizedPage from '@/PersonalizedPage.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomePage,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage,
    },
    {
      path: '/createUser',
      name: 'createUser',
      component: CreateUserPage,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: "/personalizedPages/:slug",
      name: "personalized-page",
      component: PersonalizedPage,
    }
  ],
})

export default router

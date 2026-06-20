import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/HomePage.vue'
import LoginPage from '@/LoginPage.vue'
import CreateUserPage from '@/CreateUserPage.vue'
import PersonalizedPage from '@/PersonalizedPage.vue'
import CreateSocialProjectPage from '@/CreateSocialProjectPage.vue'
import ProjectsByUser from '@/ProjectsByUser.vue'
import AboutPage from '@/AboutPage.vue'
import MapPage from '@/MapPage.vue'


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
      component: AboutPage,
    },
    {
      path: "/personalizedPages/:slug",
      name: "personalized-page",
      component: PersonalizedPage,
    },
    {
      path: "/createSocialProject",
      name: "createSocialProject",
      component: CreateSocialProjectPage,
    },
    {
      path: "/mySocialProjects",
      name: "mySocialProjects",
      component: ProjectsByUser,
    },
    {
      path: "/map",
      name: "map",
      component: MapPage,
    },
  ],
})

export default router

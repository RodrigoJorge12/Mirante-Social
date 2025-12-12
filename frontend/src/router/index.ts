import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/HomePage.vue'
import LoginPage from '@/LoginPage.vue'
import CreateUserPage from '@/CreateUserPage.vue'
import PersonalizedPage from '@/PersonalizedPage.vue'
import CreateSocialProjectPage from '@/CreateSocialProjectPage.vue'
import ProjectsByUser from '@/ProjectsByUser.vue'
import PreviewTemplate1 from '@/PreviewTemplate1.vue'
import PreviewTemplate2 from '@/PreviewTemplate2.vue'


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
      path: "/preview/template1",
      name: "previewTemplate1",
      component: PreviewTemplate1,
    },
    {
      path: "/preview/template2",
      name: "previewTemplate2",
      component: PreviewTemplate2,
    },
  ],
})

export default router

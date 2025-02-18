import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import GestionUsuarios from '../views/GestionUsuarios.vue'
import CrearEditarRutas from '../views/CrearEditarRutas.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: LoginView
    },
    {
      path: '/users',
      name: 'users',
      component: GestionUsuarios  
    },
    {
      path: '/crearEditarRutas',
      name: 'crearEditarRutas',
      component: CrearEditarRutas  // Agrega la vista aquí
    }
  ],
})

export default router

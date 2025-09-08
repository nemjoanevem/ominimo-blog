import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const HomeView = () => import('../views/HomeView.vue')
const LoginView = () => import('../views/LoginView.vue')

/** Routes with basic meta flags. */
const routes: RouteRecordRaw[] = [
  { path: '/', redirect: '/redirect' },

  { path: '/redirect', name: 'Redirect', component: { template: '<div />' }, meta: { public: true } },

  { path: '/login', name: 'Login', component: LoginView, meta: { public: true } },

  { path: '/home', name: 'Home', component: HomeView, meta: { requiresAuth: true } },

  { path: '/:pathMatch(.*)*', redirect: '/login' }
]

export const router = createRouter({
  history: createWebHistory(),
  routes
})

/** Global navigation guard to place user on login or home appropriately. */
router.beforeEach(async (to, from) => {
  const auth = useAuthStore()

  // Initialize once per app start
  if (from === undefined || from.name === undefined) {
    await auth.init()
  }

  // Special redirect route evaluates auth state
  if (to.name === 'Redirect') {
    return auth.isAuthenticated ? { name: 'Home' } : { name: 'Login' }
  }

  // Auth-guard
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'Login' }
  }

  // If already signed in, avoid going back to login
  if (to.name === 'Login' && auth.isAuthenticated) {
    return { name: 'Home' }
  }

  return true
})

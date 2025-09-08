import { defineStore } from 'pinia'
import { api, setAuthToken } from '../services/api'

type User = {
  id: number
  name: string
  email: string
  role?: string
}

type LoginPayload = { email: string; password: string }
type RegisterPayload = { name: string; email: string; password: string; password_confirmation: string }

const TOKEN_KEY = 'auth_token'

/** Auth store for handling token-based authentication. */
export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: (localStorage.getItem(TOKEN_KEY) || '') as string,
    user: null as User | null,
    loading: false as boolean,
    error: '' as string
  }),

  getters: {
    isAuthenticated: (s) => !!s.token
  },

  actions: {
    /** Initialize token header and fetch user if token exists. */
    async init() {
      if (this.token) {
        setAuthToken(this.token)
        try {
          const { data } = await api.get('/api/user')
          this.user = data
        } catch {
          // Token invalid → clear
          this.logout()
        }
      } else {
        setAuthToken(undefined)
      }
    },

    /** Sign in using Breeze API (returns plain text token). */
    async login(payload: LoginPayload) {
      this.loading = true
      this.error = ''
      try {
        const { data } = await api.post('/api/login', payload)
        // Breeze API returns: { token: "...", user: {...} } or plain string token (variant)
        const token = data.token ?? data
        this.token = token
        localStorage.setItem(TOKEN_KEY, token)
        setAuthToken(token)

        // Fetch current user
        const me = await api.get('/api/user')
        this.user = me.data
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Login failed.'
        throw e
      } finally {
        this.loading = false
      }
    },

    /** Register and sign in. */
    async register(payload: RegisterPayload) {
      this.loading = true
      this.error = ''
      try {
        const { data } = await api.post('/api/register', payload)
        const token = data.token ?? data
        this.token = token
        localStorage.setItem(TOKEN_KEY, token)
        setAuthToken(token)
        const me = await api.get('/api/user')
        this.user = me.data
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Registration failed.'
        throw e
      } finally {
        this.loading = false
      }
    },

    /** Request password reset link. */
    async forgot(email: string) {
      this.loading = true
      this.error = ''
      try {
        // Breeze API endpoint for password reset request
        await api.post('/api/forgot-password', { email })
      } catch (e: any) {
        this.error = e?.response?.data?.message ?? 'Request failed.'
        // Do not rethrow; UX: we still show generic success to avoid information leakage
      } finally {
        this.loading = false
      }
    },

    /** Sign out and clear local state. */
    async logout() {
      try {
        if (this.token) {
          await api.post('/api/logout')
        }
      } finally {
        this.token = ''
        this.user = null
        localStorage.removeItem(TOKEN_KEY)
        setAuthToken(undefined)
      }
    }
  }
})

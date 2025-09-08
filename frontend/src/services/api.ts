import axios from 'axios'
import { useUiStore } from '../stores/ui'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8080',
  withCredentials: false
})

/** Attach bearer token if present. */
export function setAuthToken(token?: string) {
  if (token) api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  else delete api.defaults.headers.common['Authorization']
}

// Attach interceptors once (guard against SSR or HMR double attach if needed)
api.interceptors.request.use((config) => {
  const ui = useUiStore()
  ui.start()
  return config
})

api.interceptors.response.use(
  (res) => {
    const ui = useUiStore()
    ui.done()
    return res
  },
  (err) => {
    const ui = useUiStore()
    ui.done()
    return Promise.reject(err)
  }
)

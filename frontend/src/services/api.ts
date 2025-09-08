import axios from 'axios'

/** Axios instance for API requests. */
export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8080',
  withCredentials: false // token-based (Breeze API), not cookie-based SPA
})

/** Attach bearer token if present. */
export function setAuthToken(token?: string) {
  if (token) api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  else delete api.defaults.headers.common['Authorization']
}

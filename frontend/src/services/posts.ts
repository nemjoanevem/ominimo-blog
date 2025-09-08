import { api } from './api'

export type Post = {
  id: number
  user_id: number
  title: string
  slug: string
  body: string
  description?: string
  user?: { id: number; name: string; email: string }
}

export type Paginated<T> = {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export async function getPosts(page = 1, perPage = 10) {
  const { data } = await api.get<Paginated<Post>>('/api/posts', { params: { page, per_page: perPage } })
  return data
}

export async function getPost(id: number) {
  const { data } = await api.get<Post>(`/api/posts/${id}`)
  return data
}

export async function createPost(payload: { title: string; body: string; slug?: string }) {
  const { data } = await api.post<Post>('/api/posts', payload)
  return data
}

export async function updatePost(id: number, payload: { title?: string; body?: string; slug?: string }) {
  const { data } = await api.put<Post>(`/api/posts/${id}`, payload)
  return data
}

export async function deletePost(id: number) {
  await api.delete(`/api/posts/${id}`)
}

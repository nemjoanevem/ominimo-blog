import { api } from './api'

export type Comment = {
  id: number
  user_id: number | null
  post_id: number
  body: string
  guest_name?: string | null
  guest_email?: string | null
  user?: { id: number; name: string; email: string }
}

export async function getComments(postId: number, page = 1, perPage = 10) {
  const { data } = await api.get(`/api/posts/${postId}/comments`, { params: { page, per_page: perPage } })
  return data
}

export async function addComment(postId: number, body: string, guest?: { name: string; email: string }) {
  const payload: any = { body }
  if (guest) {
    payload.guest_name = guest.name
    payload.guest_email = guest.email
  }
  const { data } = await api.post(`/api/posts/${postId}/comments`, payload)
  return data
}

export async function deleteComment(commentId: number) {
  await api.delete(`/api/comments/${commentId}`)
}

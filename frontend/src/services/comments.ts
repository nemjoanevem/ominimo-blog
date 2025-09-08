import { api } from './api'

export type Comment = {
  id: number
  user_id: number
  post_id: number
  body: string
  user?: { id: number; name: string; email: string }
}

export async function getComments(postId: number, page = 1, perPage = 10) {
  const { data } = await api.get(`/api/posts/${postId}/comments`, { params: { page, per_page: perPage } })
  return data
}

export async function addComment(postId: number, body: string) {
  const { data } = await api.post(`/api/posts/${postId}/comments`, { body })
  return data
}

export async function deleteComment(commentId: number) {
  await api.delete(`/api/comments/${commentId}`)
}

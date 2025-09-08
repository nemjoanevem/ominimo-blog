<template>
  <div>
    <NavBar />

    <main class="max-w-4xl mx-auto p-4">
      <div class="flex items-center justify-between mb-4">
        <button class="px-3 py-2 rounded-xl border" @click="goBack">← Back</button>

        <div v-if="auth.isAuthenticated && canEdit" class="flex gap-2">
          <router-link :to="{ name: 'PostEdit', params: { id }, query: route.query }"
            class="px-3 py-2 rounded-xl border">
            {{ $t('posts.edit') }}
          </router-link>
          <button class="px-3 py-2 rounded-xl border" @click="confirmOpen = true">
            {{ $t('posts.delete') }}
          </button>
        </div>
      </div>

      <div v-if="html" v-html="html" class="mb-8"></div>
      <div v-else class="opacity-60">{{ $t('common.loading') }}</div>

      <!-- Comments -->
      <section class="mt-8">
        <h2 class="text-lg font-semibold mb-3">{{ $t('comments.title') }}</h2>

        <div v-if="comments.length === 0" class="opacity-70 mb-4">
          {{ $t('comments.noComments') }}
        </div>

        <ul class="space-y-3">
          <li v-for="c in comments" :key="c.id" class="border rounded-xl p-3">
            <div class="flex items-center justify-between">
              <span class="font-medium">{{ c.user?.name }}</span>
              <button v-if="auth.user && (auth.user.id===c.user_id || auth.user.role==='admin')"
                      class="text-sm opacity-70 hover:underline"
                      @click="onDeleteComment(c.id)">
                {{ $t('comments.delete') }}
              </button>
            </div>
            <p class="mt-1 whitespace-pre-wrap">{{ c.body }}</p>
          </li>
        </ul>

        <div v-if="auth.isAuthenticated" class="mt-4">
          <form @submit.prevent="onAddComment" class="flex gap-2">
            <input v-model="newComment" type="text" :placeholder="$t('comments.placeholder') as string"
                   class="flex-1 border rounded-xl px-4 py-2 outline-none">
            <button class="px-3 py-2 rounded-xl bg-gray-900 text-white">
              {{ $t('comments.add') }}
            </button>
          </form>
        </div>
      </section>
    </main>

    <!-- confirm modal -->
    <ConfirmModal
      :open="confirmOpen"
      :title="$t('posts.delete')"
      :message="$t('posts.confirmDelete')"
      :confirmText="$t('common.save')"
      :cancelText="$t('common.cancel')"
      @cancel="confirmOpen=false"
      @confirm="onConfirmDelete"
    />

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { deletePost, getPost, type Post } from '../services/posts'
import { addComment, deleteComment, getComments, type Comment } from '../services/comments'
import { useAuthStore } from '../stores/auth'
import { loadPostHtml, loadPostSnapshot, savePostCache, buildPostHtml } from '../lib/postCache'
import ConfirmModal from '../components/ConfirmModal.vue'
import NavBar from '../components/NavBar.vue'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const id = Number(route.params.id)
const html = ref<string>('')               // cached or built HTML
const post = ref<Post | null>(null)        // only for canEdit & comments loading
const comments = ref<Comment[]>([])
const page = ref(1)
const lastPage = ref(1)
const newComment = ref('')
const confirmOpen = ref(false)

const canEdit = computed(() => {
  if (!auth.user || !post.value) return false
  return auth.user.role === 'admin' || auth.user.id === post.value.user_id
})

function goBack() {
  // preserve ?page from route.query (3. pont)
  router.push({ name: 'PostList', query: { page: route.query.page ?? 1 } })
}

async function onConfirmDelete() {
  confirmOpen.value = false
  await deletePost(id)
  router.push({ name: 'PostList' })
}

async function load() {
  // 1) Try cached snapshot + html
  const snap = loadPostSnapshot(id)
  const cachedHtml = loadPostHtml(id)
  if (snap && cachedHtml) {
    post.value = snap as any
    html.value = cachedHtml
    await loadComments()
    return
  }

  // 2) Fallback: fetch, then cache for later transitions
  const data = await getPost(id)
  post.value = {
    id: data.id, user_id: data.user_id, title: data.title, body: data.body, user: data.user
  } as any
  html.value = buildPostHtml(post.value as any)
  savePostCache(post.value as any)
  await loadComments()
}

async function loadComments() {
  if (!post.value) return
  const res = await getComments(post.value.id, page.value, 10)
  comments.value = res.data
  lastPage.value = res.last_page
}

async function onAddComment() {
  if (!post.value || !newComment.value.trim()) return
  await addComment(post.value.id, newComment.value.trim())
  newComment.value = ''
  await loadComments()
}

async function onDelete() {
  if (!confirm('Are you sure you want to delete this post?')) return
  await deletePost(id)
  router.push({ name: 'PostList' })
}

async function onDeleteComment(commentId: number) {
  await deleteComment(commentId)
  await loadComments()
}

onMounted(load)
</script>

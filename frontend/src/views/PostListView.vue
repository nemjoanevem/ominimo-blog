<template>
  <div>
    <NavBar />

    <main class="max-w-4xl mx-auto p-4">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">{{ $t('posts.title') }}</h1>
        <router-link v-if="auth.isAuthenticated" :to="{ name: 'PostCreate' }"
          class="px-3 py-2 rounded-xl bg-gray-900 text-white">
          {{ $t('posts.new') }}
        </router-link>
      </div>

      <div v-if="loading" class="opacity-60">{{ $t('common.loading') }}</div>

      <div v-else>
        <div v-if="posts.length === 0" class="opacity-70">
          {{ $t('posts.noPosts') }}
        </div>

        <ul class="space-y-3">
          <li
            v-for="p in posts"
            :key="p.id"
            class="border rounded-xl p-4 transition-colors"
            :class="auth.user && auth.user.id === p.user_id ? 'bg-gray-50' : 'bg-white'"
          >
            <div class="flex items-start justify-between gap-3">
              <router-link
                :to="{ name: 'PostDetail', params: { id: p.id }, query: { page } }"
                class="font-medium hover:underline"
                @click="cachePost(p)"
              >
                {{ p.title }}
              </router-link>

              <span
                v-if="auth.user && auth.user.id === p.user_id"
                class="text-xs px-2 py-0.5 rounded-full border bg-white"
              >
                {{ $t('posts.yours') }}
              </span>
            </div>

            <p class="text-sm opacity-70 mt-1">
              {{ p.user?.name }}
            </p>
            <p class="text-sm mt-2 opacity-80">{{ p.description }}</p>
          </li>
        </ul>



        <!-- Pagination -->
        <div class="flex gap-2 mt-6 justify-center">
          <button class="px-3 py-1 border rounded-lg"
                  :disabled="page<=1"
                  @click="goto(page-1)">&laquo;</button>
          <span class="px-3 py-1">{{ page }} / {{ lastPage }}</span>
          <button class="px-3 py-1 border rounded-lg"
                  :disabled="page>=lastPage"
                  @click="goto(page+1)">&raquo;</button>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getPosts, type Post } from '../services/posts'
import { useAuthStore } from '../stores/auth'
import { savePostCache, type PostSnapshot } from '../lib/postCache'
import { watch } from 'vue'
import NavBar from '../components/NavBar.vue'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()

const posts = ref<Post[]>([])
const loading = ref(true)
const page = ref(Number(route.query.page ?? 1))
const lastPage = ref(1)

async function load() {
  loading.value = true
  const res = await getPosts(page.value, 10)
  posts.value = res.data
  lastPage.value = res.last_page
  loading.value = false
}

function goto(p: number) {
  router.push({ name: 'PostList', query: { page: p } })
}

function cachePost(p: any) {
  const snap: PostSnapshot = {
    id: p.id, user_id: p.user_id, title: p.title, body: p.body, user: p.user, slug: p.slug
  }
  savePostCache(snap)
}

watch(
  () => route.query.page,
  async (val) => {
    page.value = Number(val ?? 1)
    await load()
  }
)

onMounted(() => {
  page.value = Number(route.query.page ?? 1)
  load()
})
</script>

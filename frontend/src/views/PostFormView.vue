<template>
  <div>
    <NavBar />

    <main class="max-w-3xl mx-auto p-4">
      <h1 class="text-2xl font-semibold mb-4">
        {{ isEdit ? $t('posts.update') : $t('posts.create') }}
      </h1>

      <form class="space-y-4" @submit.prevent="onSubmit">
        <div>
          <label class="block text-sm mb-1">{{ $t('posts.form.title') }}</label>
          <input v-model="form.title" type="text" required
                 class="w-full border rounded-xl px-4 py-2 outline-none">
        </div>

        <div>
          <label class="block text-sm mb-1">{{ $t('posts.form.slug') }}</label>
          <input v-model="form.slug" type="text"
                 class="w-full border rounded-xl px-4 py-2 outline-none">
        </div>

        <div>
          <label class="block text-sm mb-1">{{ $t('posts.form.body') }}</label>
          <textarea v-model="form.body" required rows="8"
                    class="w-full border rounded-xl px-4 py-2 outline-none"></textarea>
        </div>

        <div class="flex gap-2">
          <button class="px-3 py-2 rounded-xl bg-gray-900 text-white">
            {{ $t('posts.form.save') }}
          </button>
          <router-link
            :to="isEdit ? { name: 'PostDetail', params: { id } } : { name: 'PostList' }"
            class="px-3 py-2 rounded-xl border">
            {{ $t('posts.form.cancel') }}
          </router-link>
        </div>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
      </form>
    </main>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { createPost, getPost, updatePost } from '../services/posts'
import { savePostCache } from '../lib/postCache'
import NavBar from '../components/Navbar.vue'

const route = useRoute()
const router = useRouter()

const id = computed(() => Number(route.params.id))
const isEdit = computed(() => !!route.params.id)

const form = reactive<{ title: string; slug?: string; body: string }>({ title: '', body: '' })
const error = ref('')

onMounted(async () => {
  if (isEdit.value) {
    const data = await getPost(id.value)
    form.title = data.title
    form.slug = data.slug
    form.body = data.body
  }
})

async function onSubmit() {
  error.value = ''
  try {
    if (isEdit.value) {
      const updated = await updatePost(id.value, { title: form.title, slug: form.slug, body: form.body })
      // cache refresh before navigate
      savePostCache({
        id: updated.id,
        user_id: updated.user_id,
        title: updated.title,
        body: updated.body,
        user: updated.user
      })
      router.push({ name: 'PostDetail', params: { id: updated.id } })
    } else {
      const created = await createPost(form)
      savePostCache({
        id: created.id,
        user_id: created.user_id,
        title: created.title,
        body: created.body,
        user: created.user
      })
      router.push({ name: 'PostDetail', params: { id: created.id } })
    }
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Save failed.'
  }
}
</script>

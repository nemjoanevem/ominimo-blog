<template>
  <header class="w-full border-b bg-white">
    <div class="max-w-4xl mx-auto flex items-center justify-between p-4">
      <router-link to="/home" class="font-semibold">Blog</router-link>

      <div class="flex items-center gap-3">
        <router-link v-if="!auth.isAuthenticated" to="/login"
          class="px-3 py-1 rounded-lg border">
          {{ $t('nav.login') }}
        </router-link>
        <button v-else @click="onLogout"
          class="px-3 py-1 rounded-lg border">
          {{ $t('nav.logout') }}
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const onLogout = async () => {
  await auth.logout()
  router.push({ name: 'Login' })
}
</script>

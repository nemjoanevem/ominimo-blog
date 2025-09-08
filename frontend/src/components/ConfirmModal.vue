<template>
  <transition name="fade">
    <div v-if="open" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="text-lg font-semibold mb-2">{{ title }}</h3>
        <p class="text-sm opacity-80 mb-6">{{ message }}</p>
        <div class="flex justify-end gap-2">
          <button class="px-3 py-2 rounded-xl border" @click="$emit('cancel')">{{ cancelText }}</button>
          <button class="px-3 py-2 rounded-xl bg-gray-900 text-white" @click="$emit('confirm')">{{ confirmText }}</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
defineProps<{
  open: boolean
  title?: string
  message?: string
  confirmText?: string
  cancelText?: string
}>()
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s }
.fade-enter-from, .fade-leave-to { opacity: 0 }
</style>

<template>
  <div class="flex items-center justify-center w-full min-h-60" aria-label="Memuat konten">
    <div role="status" class="flex flex-col justify-center items-center gap-4">
      <div class="flex items-center justify-center gap-2">
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>

      <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ loadingText }}
      </p>

    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  loadingText: {
    type: String,
    default: 'Sedang memproses data'
  }
});
</script>

<style scoped>
.dot {
  display: block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #0d9488; /* teal-600 */
  animation: dot-pulse 1.4s infinite ease-in-out both;
}

.dark .dot {
  background-color: #2dd4bf; /* teal-400 */
}

.dot:nth-child(1) {
  animation-delay: -0.32s;
}

.dot:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes dot-pulse {
  0%, 80%, 100% {
    transform: scale(0.5);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

.ellipsis span {
  opacity: 0;
  animation: ellipsis-fade 1.4s infinite;
}

.ellipsis span:nth-child(1) {
  animation-delay: 0s;
}

.ellipsis span:nth-child(2) {
  animation-delay: 0.2s;
}

.ellipsis span:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes ellipsis-fade {
  0% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}
</style>
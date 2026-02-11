<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

      <div class="relative z-10 w-full max-w-sm text-center">
        <div class="elegant-dot-loader mx-auto">
          <span 
            v-for="i in 5" 
            :key="i" 
            class="dot" 
            :style="{ 
              'background-color': color,
              'animation-delay': `${i * 0.1}s`
            }"
          ></span>
        </div>
        
        <p v-if="message" class="mt-5 text-sm tracking-wider text-white/80">
          {{ message }}
        </p>
      </div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
defineProps({
  show: { type: Boolean, required: true },
  message: { type: String, default: 'Harap tunggu sebentar...' },
  color: { type: String, default: '#ffffff' },
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 250ms ease-in-out;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.elegant-dot-loader {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  width: fit-content;
}

.dot {
  display: block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  opacity: 0.2;
  animation: dot-elegance 1.2s infinite ease-in-out;
}

@keyframes dot-elegance {
  0%, 100% {
    transform: scale(0.6);
    opacity: 0.2;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
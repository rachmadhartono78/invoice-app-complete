<template>
    <!-- Fade transition untuk backdrop -->
    <transition name="modal-fade" appear>
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex justify-center items-center bg-black bg-opacity-50 p-2"
        @click.self="onBackdropClick"
      >
        <!-- Zoom transition untuk modal box -->
        <transition name="modal-zoom" appear>
          <div
            :class="[
              'relative w-full m-1 bg-white rounded-xl shadow dark:bg-gray-800 transition-all',
              modalSizeClass,
              isBouncing ? 'animate-bounce-once' : ''
            ]"
            :style="scrollable ? 'max-height: 90vh;' : ''"
          >
            <!-- Modal Header -->
            <div class="flex justify-between items-start mb-4 p-3 rounded-t-xl bg-primary-600">
              <h3 class="text-xl font-semibold text-white">
                {{ title }}
              </h3>
              <button
                v-if="props.showClose"
                type="button"
                class="text-white bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                @click="close"
              >
                <svg
                  aria-hidden="true"
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0
                    111.414 1.414L11.414 10l4.293 4.293a1 1 0
                    01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0
                    01-1.414-1.414L8.586 10 4.293 5.707a1 1 0
                    010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
  
            <!-- Modal Body -->
            <div :class="['p-4', scrollable ? 'overflow-y-auto' : '']" :style="scrollable ? 'max-height: calc(90vh - 120px);' : ''">
              <slot />
            </div>
  
            <!-- Modal Footer -->
            <div class="flex justify-end pt-4">
              <slot name="footer" />
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted, computed } from 'vue'
  
  const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    persistent: { type: Boolean, default: false },
    showClose: { type: Boolean, default: true },
    size: { type: String, default: 'md', validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl'].includes(value) },
    scrollable: { type: Boolean, default: false },
  })
  
  const emit = defineEmits(['close'])
  
  const isBouncing = ref(false)
  
  const modalSizeClass = computed(() => {
    const sizeClasses = {
      'sm': 'max-w-sm',
      'md': 'max-w-md',
      'lg': 'max-w-lg',
      'xl': 'max-w-xl',
      '2xl': 'max-w-2xl'
    }
    return sizeClasses[props.size] || 'max-w-md'
  })
  
  const close = () => {
    emit('close')
  }
  
  const onBackdropClick = () => {
    if (props.persistent) {
      isBouncing.value = true
      setTimeout(() => {
        isBouncing.value = false
      }, 400)
    } else {
      close()
    }
  }
  
  const onKeydown = (e) => {
    if (e.key === 'Escape') {
      if (!props.persistent) {
        close()
      } else {
        e.preventDefault()
      }
    }
  }
  
  onMounted(() => {
    document.addEventListener('keydown', onKeydown)
  })
  onUnmounted(() => {
    document.removeEventListener('keydown', onKeydown)
  })
  </script>
  
  <style scoped>
  /* Backdrop fade */
  .modal-fade-enter-active,
  .modal-fade-leave-active {
    transition: opacity 0.2s ease;
  }
  .modal-fade-enter-from,
  .modal-fade-leave-to {
    opacity: 0;
  }
  
  /* Modal zoom in/out */
  .modal-zoom-enter-active,
  .modal-zoom-leave-active {
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .modal-zoom-enter-from,
  .modal-zoom-leave-to {
    opacity: 0;
    transform: scale(0.95);
  }
  
  /* Bounce effect when persistent modal clicked */
  @keyframes bounce-once {
    0%   { transform: scale(1); }
    25%  { transform: scale(1.05); }
    50%  { transform: scale(0.95); }
    75%  { transform: scale(1.02); }
    100% { transform: scale(1); }
  }
  .animate-bounce-once {
    animation: bounce-once 0.4s ease;
  }
  </style>
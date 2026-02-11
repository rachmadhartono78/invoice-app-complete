<template>
  <div class="flex flex-col h-dvh w-full bg-gray-100 dark:bg-gray-900">
    <Header @toggleSidebar="toggleSidebar" />
    <div class="flex flex-1 overflow-hidden">
      <Sidebar :is-open="isSidebarOpen" :minify="minifySidebar" @update:minify="updateMinifySidebar($event)"
        @close="isSidebarOpen = false" />
      <div :class="[
        'flex flex-col flex-1 min-w-0']" class="overflow-hidden">
        <Breadcrumb />
        <main class="flex-1 p-4 pt-0 overflow-y-auto">
          <router-view :key="$route.fullPath" @showToast="handleShowToast" />
        </main>
      </div>
    </div>
    <!-- Floating draggable button -->
    <div
      v-if="false"
      ref="floatingButton"
      :style="{ left: buttonPosition.x + 'px', top: buttonPosition.y + 'px' }"
      class="fixed z-50 animate-bounce-slow hover:animate-none"
      @mousedown="startDrag"
      @touchstart.prevent="startDrag">
      <button
        data-tooltip-target="tooltip-floating"
        data-tooltip-placement="left"
        class="relative bg-green-500 hover:bg-green-700 text-white rounded-full shadow-2xl hover:shadow-xl px-1 py-1 cursor-move transition-all duration-300 hover:scale-110"
        style="box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.6), 0 10px 10px -5px rgba(0, 0, 0, 0.3);"
        @click="onFloatingButtonClick">
        <svg class="h-10 w-10 text-white" viewBox="0 0 1120 1120" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M252 434C252 372.144 302.144 322 364 322H770C831.856 322 882 372.144 882 434V614.459L804.595 585.816C802.551 585.06 800.94 583.449 800.184 581.405L763.003 480.924C760.597 474.424 751.403 474.424 748.997 480.924L711.816 581.405C711.06 583.449 709.449 585.06 707.405 585.816L606.924 622.997C600.424 625.403 600.424 634.597 606.924 637.003L707.405 674.184C709.449 674.94 711.06 676.551 711.816 678.595L740.459 756H629.927C629.648 756.476 629.337 756.945 628.993 757.404L578.197 825.082C572.597 832.543 561.403 832.543 555.803 825.082L505.007 757.404C504.663 756.945 504.352 756.476 504.073 756H364C302.144 756 252 705.856 252 644V434ZM633.501 471.462C632.299 468.212 627.701 468.212 626.499 471.462L619.252 491.046C618.874 492.068 618.068 492.874 617.046 493.252L597.462 500.499C594.212 501.701 594.212 506.299 597.462 507.501L617.046 514.748C618.068 515.126 618.874 515.932 619.252 516.954L626.499 536.538C627.701 539.788 632.299 539.788 633.501 536.538L640.748 516.954C641.126 515.932 641.932 515.126 642.954 514.748L662.538 507.501C665.788 506.299 665.788 501.701 662.538 500.499L642.954 493.252C641.932 492.874 641.126 492.068 640.748 491.046L633.501 471.462Z"
            fill="currentColor"></path>
          <path
            d="M771.545 755.99C832.175 755.17 881.17 706.175 881.99 645.545L804.595 674.184C802.551 674.94 800.94 676.551 800.184 678.595L771.545 755.99Z"
            fill="currentColor"></path>
        </svg>
        <!-- X icon to hide the button -->
        <span
          class="absolute top-1 right-2 translate-x-1/2 -translate-y-1/2 bg-gray-600 dark:bg-gray-400 text-white rounded-full p-0 cursor-pointer hover:scale-125 transition-transform"
          @click.stop="handleCloseFloatingButton" title="Hide">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </span>
      </button>
    </div>

    <div id="tooltip-floating" role="tooltip"
      class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700"
      style="background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15);">
      Layanan Koas
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useAuthStore } from '@/stores/auth'
import  { useNotificationStore}  from '@/stores/notificationStore' 

import Header from './Header.vue'
import Sidebar from './Sidebar.vue'
import Breadcrumb from './Breadcrumb.vue'

const emit = defineEmits(['showToast'])

const minifySidebar = ref(false)
const authStore = useAuthStore()
const isSidebarOpen = ref(false)
const showFloatingButton = ref(
  sessionStorage.getItem('showFloatingButton') !== null
    ? sessionStorage.getItem('showFloatingButton') === 'true'
    : true
)

// Floating button position state
const floatingButton = ref(null)
const buttonPosition = ref({
  x: typeof window !== 'undefined' ? Math.max(0, window.innerWidth - 70) : 20,
  y: typeof window !== 'undefined' ? Math.max(0, window.innerHeight - 100) : 100
})
const isDragging = ref(false)
const hasDragged = ref(false)
const dragStart = ref({ x: 0, y: 0 })
const startPosition = ref({ x: 0, y: 0 })

const isMobile = ref(window.innerWidth <= 768)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const handleResize = () => {
  isMobile.value = window.innerWidth <= 768

  // Adjust button position if it's out of viewport after resize
  if (buttonPosition.value.x > window.innerWidth - 50) {
    buttonPosition.value.x = Math.max(0, window.innerWidth - 70)
  }
  if (buttonPosition.value.y > window.innerHeight - 50) {
    buttonPosition.value.y = Math.max(0, window.innerHeight - 100)
  }
}

onMounted(() => {

  useNotificationStore().fetchNotifications()


  window.addEventListener('resize', handleResize)
  const stored = sessionStorage.getItem('showFloatingButton')
  showFloatingButton.value = stored !== null ? stored === 'true' : true

  // Load saved position or set default
  const savedPosition = sessionStorage.getItem('floatingButtonPosition')
  if (savedPosition) {
    const pos = JSON.parse(savedPosition)
    // Ensure saved position is within viewport
    buttonPosition.value = {
      x: Math.max(0, Math.min(window.innerWidth - 50, pos.x)),
      y: Math.max(0, Math.min(window.innerHeight - 50, pos.y))
    }
  } else {
    // Set default position (bottom-right corner with some padding)
    buttonPosition.value = {
      x: Math.max(0, window.innerWidth - 70),
      y: Math.max(0, window.innerHeight - 100)
    }
  }

  // Add drag event listeners
  document.addEventListener('mousemove', drag)
  document.addEventListener('mouseup', stopDrag)
  document.addEventListener('touchmove', drag, { passive: false })
  document.addEventListener('touchend', stopDrag)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)
  document.removeEventListener('mousemove', drag)
  document.removeEventListener('mouseup', stopDrag)
  document.removeEventListener('touchmove', drag, { passive: false })
  document.removeEventListener('touchend', stopDrag)
})

const handleShowToast = (message, type, timeout = 3000, title = '') => {
  emit('showToast', { message, type, timeout, title })
}

const updateMinifySidebar = (value) => {
  minifySidebar.value = value
}

// Floating button click handler
const onFloatingButtonClick = () => {
  // Don't open link if button was dragged
  if (isDragging.value || hasDragged.value) {
    hasDragged.value = false
    return
  }
  window.open('https://wa.me/6285190300705', '_blank')
}

const handleCloseFloatingButton = () => {
  showFloatingButton.value = false;
  sessionStorage.setItem('showFloatingButton', 'false');
}

// Drag functionality
const startDrag = (e) => {
  e.preventDefault()
  isDragging.value = true
  hasDragged.value = false

  const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX
  const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY

  dragStart.value = {
    x: clientX - buttonPosition.value.x,
    y: clientY - buttonPosition.value.y
  }

  startPosition.value = {
    x: buttonPosition.value.x,
    y: buttonPosition.value.y
  }
}

const drag = (e) => {
  if (!isDragging.value) return

  e.preventDefault()
  const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX
  const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY

  let newX = clientX - dragStart.value.x
  let newY = clientY - dragStart.value.y

  // Keep button within viewport
  const buttonWidth = 50
  const buttonHeight = 50
  newX = Math.max(0, Math.min(window.innerWidth - buttonWidth, newX))
  newY = Math.max(0, Math.min(window.innerHeight - buttonHeight, newY))

  // Check if button has moved more than 5 pixels (to differentiate between click and drag)
  const distance = Math.sqrt(
    Math.pow(newX - startPosition.value.x, 2) +
    Math.pow(newY - startPosition.value.y, 2)
  )

  if (distance > 5) {
    hasDragged.value = true
  }

  buttonPosition.value = { x: newX, y: newY }
}

const stopDrag = () => {
  if (isDragging.value) {
    isDragging.value = false
    // Save position to sessionStorage
    sessionStorage.setItem('floatingButtonPosition', JSON.stringify(buttonPosition.value))
  }
}
</script>
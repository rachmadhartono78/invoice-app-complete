<template>
  <div v-if="isMobile && isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-20" @click="$emit('close')"></div>
  <aside :class="[
    'fixed top-0 left-0 h-full w-3/4 z-40 bg-white border-r border-gray-200 dark:border-gray-700 transition-transform duration-300 bg-white dark:bg-gray-800' ,
    isOpen ? 'translate-x-0' : '-translate-x-full',
    'md:static md:translate-x-0 md:block',
    !isOpen ? 'hidden md:block' : 'block',
    minify ? 'md:w-16' : 'md:w-64'
  ]">
    <div class="flex flex-col justify-between h-[92%] bg-white dark:bg-gray-800">
      <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-800 min-h-full p-3">
        <div class="p-3 flex justify-between items-center" v-if="isMobile">
          <div @click="$router.push({ name: 'home' }); emit('close')" class="flex items-center justify-between mr-4">
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">ecos</span>
            <small class="self-end ml-1 dark:text-white">v.1.0</small>
          </div>
          <svg @click="emit('close')" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18 17.94 6M18 18 6.06 6" />
          </svg>

        </div>
        <ul class="space-y-1">
          <template v-for="menu in menus" :key="menu.id">
            <!-- Single Menu Item (No Children) -->
            <li v-if="!menu.children">
              <a @click.prevent="onClickMenu(menu.url)"
                :class="['flex items-center p-2 text-sm font-medium rounded-lg cursor-pointer',
                  isActive(menu.url) ? 'bg-gray-100 dark:bg-gray-700 text-primary-500' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700']">
                <span v-html="menu.icon"></span>
                <span class="flex-1 ml-3 whitespace-nowrap" v-if="!minify">{{ menu.name }}</span>
                <span v-show="menu.badge" v-if="!minify"
                  class="inline-flex justify-center items-center w-5 h-5 text-xs font-semibold rounded-full text-white bg-red-600 dark:bg-red-500">
                  {{ menu.badge }}
                </span>
              </a>
            </li>

            <!-- Menu with Dropdown (Has Children) -->
            <li v-else>
              <button @click="toggleDropdown(menu.name)"
                :class="['flex items-center p-2 w-full text-sm font-medium rounded-lg transition duration-75 group',
                  isParentActive(menu.children) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700']">
                <span v-html="menu.icon"></span>
                <span class="flex-1 ml-3 text-left whitespace-nowrap" v-if="!minify">{{ menu.name }}</span>
                <svg aria-hidden="true" class="w-6 h-6 transition-transform" v-if="!minify"
                  :class="{ 'rotate-180': activeDropdown === menu.name }" fill="currentColor" viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
                </svg>
              </button>

              <!-- Dropdown Items -->
              <ul :class="{ 'block': activeDropdown === menu.name, 'hidden': activeDropdown !== menu.name }"
                class="py-2 space-y-1">
                <li v-for="childMenu in menu.children" :key="childMenu.name">
                  <a @click.prevent="onClickMenu(childMenu.url)" :class="{
                    'flex items-center p-2 w-full font-medium rounded-lg text-sm transition duration-75 cursor-pointer': true,
                    'pl-11 text-left': !minify, 'pl-2 text-right': minify, 'bg-gray-100 dark:bg-gray-700 text-primary-500': isActive(childMenu.url), 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !isActive(childMenu.url)
                  }">
                    <span class="" v-if="minify"><strong>{{ childMenu.name.substring(0, 2).toUpperCase()
                    }}</strong></span>
                    <span class="flex-1 ml-3 whitespace-nowrap">{{ !minify ? childMenu.name : '' }}</span>
                    <span v-show="childMenu.badge"
                      class="inline-flex justify-center items-center w-5 h-5 text-xs font-semibold rounded-full text-white bg-red-600 dark:bg-red-500">
                      {{ childMenu.badge }}
                    </span>
                  </a>
                </li>
              </ul>
            </li>
          </template>
        </ul>

        <!-- Jika tidak ada menu -->
        <ul v-if="menus.length === 0">
          <li>
            <a
              class="text-xs flex items-center p-2 text-base rounded-lg text-gray-400 dark:text-white cursor-not-allowed">
              <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 14v3m-3-6V7a3 3 0 1 1 6 0v4m-8 0h10a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1Z" />
              </svg>
              <span class="ms-3" v-if="!minify"> Anda tidak memiliki otoritas</span>
            </a>
          </li>
        </ul>
      </div>
      <!-- Minify Button Always at Bottom -->
      <div v-if="!isMobile"
        class="flex items-end border-t border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-800">
        <button @click="updateMinify(!minify)" class="w-full flex justify-center">
          <svg v-if="!minify" class="w-6 h-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
          </svg>
          <svg v-else class="w-6 h-6 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4" />
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { onMounted, onUnmounted, ref } from 'vue';


const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const activeDropdown = ref(null);
const menus = ref([]);
const props = defineProps({ isOpen: Boolean, minify: Boolean })
const emit = defineEmits(['close', 'update:minify'])


onUnmounted(() => {
  window.removeEventListener('resize', updateIsMobile);
});

onMounted(() => {
  menus.value = authStore.getMenuInduk;
  updateIsMobile();
  window.addEventListener('resize', updateIsMobile);
});

let isMobile = ref(window.innerWidth <= 768);

const updateIsMobile = () => {
  isMobile.value = window.innerWidth <= 768;
  console.log(isMobile.value, 'isMobile');

  if (isMobile.value == true) {
    updateMinify(false)
  };


};


const isActive = (menuUrl) => {
  return route.path === menuUrl;
};

const isParentActive = (childs) => {
  return childs.some(child => child.url === route.path);
};

const toggleDropdown = (menuName) => {
  activeDropdown.value = activeDropdown.value === menuName ? null : menuName;
  updateMinify(false);
};

const onClickMenu = (url) => {
  router.push(url);
  emit("close");
};

const updateMinify = (value) => {
  emit("update:minify", value);
}
</script>

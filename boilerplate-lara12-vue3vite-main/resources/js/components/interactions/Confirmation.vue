<template>
    <Transition enter-active-class="transition-opacity duration-300 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isVisible" class="fixed inset-0 z-[60] bg-black bg-opacity-50"></div>
    </Transition>

    <Transition enter-active-class="transition-transform transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0 scale-75" enter-to-class="opacity-100 scale-100"
        leave-active-class="transition-transform transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-75">
        <div v-if="isVisible" class="fixed inset-0 z-[70] flex items-center justify-center">
            <div class="relative p-4 w-full max-w-md">
                <div class="relative bg-white rounded-xl shadow dark:bg-gray-700">
                    <button type="button"
                        class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        @click="close(false)">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>

                    <div class="p-4 md:p-5">
                        <slot name="icon"></slot>

                        <h3 v-if="options.title" class="text-lg font-bold text-gray-500 dark:text-gray-400">
                            {{ options.title }}
                        </h3>

                        <p class="mb-5 text-md font-normal text-gray-500 dark:text-gray-400">
                            {{ options.message }}
                        </p>
                        <div class="flex justify-center items-center gap-3">
                            <button type="button" :disabled="options.loading" class="btn-cancel" @click="close(false)">
                                {{ options.cancelText }}
                            </button>

                            <button type="button" :disabled="options.loading"
                                :class="['text-white', `bg-${options.confirmButtonColor}-600`, `hover:bg-${options.confirmButtonColor}-800`, `focus:ring-${options.confirmButtonColor}-300`, `dark:focus:ring-${options.confirmButtonColor}-800`, 'focus:ring-4', 'focus:outline-none', 'font-medium', 'rounded-lg', 'text-sm', 'inline-flex', 'items-center', 'px-5', 'py-2.5', 'text-center', 'ms-3']"
                                @click="close(true)">
                                <span v-if="options.loading">Loading ..</span>
                                <span v-else>{{ options.confirmText }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
<script setup lang="ts">
import { confirmation } from "@/composables/confirmation.ts";

const { isVisible, options, close } = confirmation;
</script>

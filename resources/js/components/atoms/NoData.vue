<template>
    <div class="flex flex-col items-center justify-center text-center p-8 min-h-[40vh]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-20 text-primary-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
            {{ title }}
        </h3>
        <p class="max-w-sm text-sm text-gray-500 dark:text-gray-400">
            {{ message }}
        </p>
        
        <div class="mt-6">
            <slot></slot>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps({
    title: {
        type: String,
        default: 'Tidak Ada Data',
    },
    message: {
        type: String,
        default: 'Data yang Anda cari tidak dapat ditemukan atau belum tersedia saat ini.',
    },
});
</script>
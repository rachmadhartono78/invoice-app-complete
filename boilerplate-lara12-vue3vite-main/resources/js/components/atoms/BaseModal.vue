<template>
    <Teleport to="body">
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[9999]">
            <div
                class="flex flex-col justify-between rounded-xl bg-white dark:bg-gray-800 w-full h-full md:w-[90%] md:h-[90%] max-w-[90%] max-h-[90%]">
                <div v-if="$slots.header">
                    <slot name="header"></slot>
                </div>
                <div v-else-if="props.title" class="p-6">
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ props.title }}</h1>
                </div>

                <div class="flex-1 overflow-auto p-2 md:p-4 transparent-scrollbar">
                    <slot></slot>
                </div>

                <div v-if="$slots.footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['close'])
</script>

<style>
.transparent-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.transparent-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(100, 100, 100, 0.4);
    border-radius: 4px;
}

.transparent-scrollbar::-webkit-scrollbar-track {
    background-color: transparent;
}
</style>

<template>
    <BaseModal title="Detail Notifikasi">
        <div class="w-full">
            <div v-if="!loading">
                <div class="mb-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="space-y-4">
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ modalData.title }}</h3>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ modalData.message }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 pt-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ modalData.created_at }}
                                <div v-if="modalData.url_action" class="ml-auto">
                                    <button type="button" @click="handleRedirect()"
                                        class="min-w-[120px] text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <loader v-else></loader>
        </div>
        <template #footer>
            <div class="flex items-center justify-center gap-3">
                <button type="button" @click="handleClose()"
                    class="min-w-[120px] text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800">Tutup</button>
            </div>
        </template>
    </BaseModal>
</template>
<script>
import BaseModal from '../atoms/BaseModal.vue';

export default {
    components: { BaseModal },
    props: {
        modalData: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            loading: false,
            data: null
        };
    },
    created() {
    },
    emits: ['close'],
    methods: {
        handleClose() {
            this.$emit('close');
        },
        handleRedirect() {
            if (this.modalData.url_action) {
                window.open(this.modalData.url_action, '_blank');
            }
        }
    }
};
</script>

<style scoped>
button {
    transition: background 0.3s;
}

button:hover:not(:disabled) {
    opacity: 0.8;
}
</style>
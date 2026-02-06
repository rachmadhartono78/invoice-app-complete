<template>
    <div>
        <div class="mb-4 col-span-full xl:mb-2">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Example Dialog</h1>
        </div>

        <!-- Button Trigger -->
        <button @click="dialogConfirmation = true" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Delete Item
        </button>

        <!-- Confirmation Modal -->
        <ConfirmationDialog :isVisible="dialogConfirmation" title="Delete Item"
            message="Are you sure you want to delete this item? This action cannot be undone."
            @onClose="handleOnClose($event)" />

        <hr class="my-4">
    </div>
</template>
<script>
import ConfirmationDialog from './ConfirmationDialog.vue';


export default {
    components: {
        ConfirmationDialog
    },
    data() {
        return {
            dialogConfirmation: false,
        }
    },
    mounted() {

    },
    methods: {
        handleOnClose(event) {
            alert('Confirmed: ' + event);
            this.dialogConfirmation = false;
        },
    },
};
</script>
<template>
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">
                Contact Us
            </h2>
            <p class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">
                Have questions about the boilerplate? Need technical support? Let us know.
            </p>
            <form @submit.prevent="submitForm" class="space-y-8">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Your email
                    </label>
                    <input
                        type="email"
                        id="email"
                        v-model="form.email"
                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
                        placeholder="name@example.com"
                        required
                    />
                </div>
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                        Subject
                    </label>
                    <input
                        type="text"
                        id="subject"
                        v-model="form.subject"
                        class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-sm-light"
                        placeholder="Let us know how we can help you"
                        required
                    />
                </div>
                <div class="sm:col-span-2">
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">
                        Your message
                    </label>
                    <textarea
                        id="message"
                        v-model="form.message"
                        rows="6"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Leave a comment..."
                        required
                    ></textarea>
                </div>
                <button
                    type="submit"
                    :disabled="loading"
                    class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-blue-700 sm:w-fit hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="!loading">Send message</span>
                    <span v-else>Sending...</span>
                </button>
            </form>

            <div v-if="submitted" class="mt-8 p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Success!</span> Your message has been sent. We'll get back to you soon.
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import publicAxios from '@/api/publicAxios';

const form = ref({
    email: '',
    subject: '',
    message: ''
});

const loading = ref(false);
const submitted = ref(false);

const submitForm = async () => {
    loading.value = true;
    submitted.value = false;

    try {
        // This would normally send to a contact API endpoint
        // For demo purposes, we just simulate the submission
        await new Promise(resolve => setTimeout(resolve, 1000));

        console.log('Contact form submitted:', form.value);
        submitted.value = true;

        // Reset form
        form.value = {
            email: '',
            subject: '',
            message: ''
        };
    } catch (error) {
        console.error('Error submitting form:', error);
    } finally {
        loading.value = false;
    }
};
</script>

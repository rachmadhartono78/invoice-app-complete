<template>
    <div>

        <ol class="relative border-s border-gray-200 dark:border-gray-700">
            <li class="mb-10 ms-6" v-for="(item, index) in items" :key="index">
                <span
                    :class="[
                        'absolute flex items-center justify-center rounded-full -start-3 ring-gray-200 dark:ring-gray-900',
                        index !== 0 ? 'w-6 h-6 ring-4 bg-gray-300 dark:bg-gray-600' : 'w-6 h-6 ring-4 ring-primary-300 dark:ring-primary-200 ring-opacity-30 dark:ring-opacity-10 bg-primary-600 dark:bg-primary-600'
                    ]">
                    <span v-if="item.icon" v-html="item.icon"></span>
                </span>
                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white cursor-pointer" @click="handleClick(item)">{{ item.title }}
                    <!-- <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 ms-3">Terbaru</span> -->
                </h3>
                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                    {{item.date}}</time>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">{{ item.description }}</p>
                <a :href="item.link" v-if="item.link && item.linkText" target="_blank"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                    <svg
                        class="w-3.5 h-3.5 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                        <path
                            d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                    </svg> {{ item.linkText }}</a>
            </li>
            <li class="mb-10 ms-6">
                <span
                    class="absolute flex items-center justify-center w-6 h-6 bg-gray-300 rounded-full -start-3 ring-4 ring-gray-200 dark:ring-gray-900 dark:bg-gray-600">
                    
                </span>
                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                    <!-- <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-blue-900 dark:text-blue-300 ms-3">Terbaru</span> -->
                </h3>

                <time class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                   </time>
                <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400"></p>
            </li>
        </ol>
    </div>
</template>
<script>
export default {
    props: {
        items: {
            type: Array,
            default: []
        },
    },
    data() {
        return {

        };
    },
    computed: {
       
    },
    methods: {
        handleClick(item) {
            if (item.url && item.url !== '#') {
            this.$router.push(item.url);
            }
        }
    },
}
</script>
<template>
    <div class="pb-4 block sm:flex items-center justify-between lg:mt-1.5">
        <div class="w-full mb-1">
            <div class="sm:flex">
                <div class="items-center mb-3 sm:flex sm:mb-0 " v-if="search">
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative mt-1 lg:w-64 xl:w-96">
                        <input type="text" name="name" id="table-search" v-model="searchQuery" @input="onSearch"
                            class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Cari">
                    </div>
                </div>
                <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                    <button type="button" v-show="showAddButton" @click="onAddClicked"
                        class="inline-flex items-center justify-center w-full md:w-max px-3 py-2 text-sm font-medium text-center text-white bg-primary-600 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-600 dark:focus:ring-primary-800">
                        <svg class="w-6 h-6 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Tambah
                    </button>
                    <a href="#" v-show="showExportButton" @click.prevent="onExportClicked"
                        class="inline-flex items-center justify-center w-full md:w-max px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                        <svg class="w-6 h-6 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Export
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="relative overflow-x-auto dark:shadow-lg sm:rounded-lg"
        :class="paginatedData.length <= 3 && paginatedData.length !== 0 ? 'min-h-[250px] ' : 'shadow'">
        <table v-if="columns.length > 0"
            class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b">
                <tr>
                    <th class="px-6 py-4" v-if="withCheckbox">
                        <input id="default-checkbox" type="checkbox" v-model="allRowChecked"
                            @click="onHeaderCheckboxChange($event)"
                            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th v-for="(column, index) in columns" :key="index" class="px-6 py-4 uppercase"
                        :class="column.prop == 'actions' ? 'text-center' : ''">
                        {{ column.name }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="!loading">
                    <template v-if="paginatedData.length > 0">
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-800 border-gray-200"
                            v-for="(row, rowIndex) in paginatedData" :key="rowIndex">
                            <td class="px-6 py-2" v-if="withCheckbox">
                                <input id="default-checkbox" type="checkbox" v-model="row.checked"
                                    @click="onRowCheckboxChange($event, rowIndex, row.id)"
                                    class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </td>
                            <td v-for="(column, colIndex) in columns" :key="colIndex" class="px-6 py-2">
                                <p v-if="column.prop == 'number'">{{ ((currentPage - 1) * perPage) + rowIndex + 1 }}</p>
                                <p v-else-if="column.prop !== 'actions'">
                                    <slot :name="`column-${column.prop}`" :row="row">
                                        <!-- {{ row[column.prop] }} -->
                                        {{ getNestedValue(row, column.prop) }}
                                    </slot>
                                </p>
                                <div v-else class="flex items-center justify-center">

                                    <button type="button" :data-dropdown-toggle="'dropdownDots' + row.id + $route.path.split('/').filter(Boolean).pop()"
                                        data-dropdown-placement="bottom-start"
                                        class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
                                        Aksi
                                        <svg class="-me-0.5 ms-1.5 h-4 w-4" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div :id="'dropdownDots' + row.id + $route.path.split('/').filter(Boolean).pop()"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-40 dark:bg-gray-700 dark:divide-gray-600">
                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <a href="" @click.prevent="onDetailClicked(row)"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Lihat</a>
                                            </li>
                                            <li v-if="row.actions?.canUpdate == true">
                                                <a href="" @click.prevent="onEditClicked(row)"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                            </li>
                                            <li v-if="row.actions?.canDelete == true">
                                                <a href="" @click.prevent="onDeleteClicked(row)"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Hapus</a>
                                            </li>
                                            <li v-for="action in row.additional_action">
                                                <a href="" @click.prevent="action.action(row)"
                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{
                                                        action.name }}</a>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="100%" class="text-center py-4 text-gray-500">No data available</td>
                        </tr>
                    </template>
                </template>
                <template v-else>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                        v-for="(row, indexSkeleton) in perPage" :key="indexSkeleton">
                        <td v-for="(column, colIndex) in (columns.length + (withCheckbox ? 1 : 0))" :key="colIndex"
                            class="px-6 py-4">
                            <div class="animate-pulse h-3 bg-gray-300 rounded-full dark:bg-gray-600 mb-2.5"></div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div v-else class="text-center py-4 text-gray-500">
            No columns defined.
        </div>
    </div>

    <nav class="mt-5 flex justify-center md:justify-end">
        <ul class="flex items-center -space-x-px h-8 text-md">
            <li>
                <button type="button" @click="firstPage" :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'cursor-not-allowed' : ''"
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Previous</span>
                    <svg class="w-[23px] " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" @click="prevPage" :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'cursor-not-allowed' : ''"
                    class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Previous</span>
                    <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M5 1 1 5l4 4" />
                    </svg>
                </button>
            </li>
            <li v-for="page in paginationRange" :key="page">
                <button type="button" @click="goToPage(page)"
                    :class="{ 'z-10 text-white border-1 border-primary-300 bg-primary-600 hover:bg-primary-700 hover:text-primary-200 dark:border-primary-700 dark:bg-primary-700 dark:text-white': page === currentPage, 'text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== currentPage }"
                    class="flex items-center justify-center px-3 h-8 leading-tight">
                    {{ page }}
                </button>
            </li>
            <li>
                <button type="button" @click="nextPage" :disabled="currentPage >= totalPages"
                    :class="currentPage >= totalPages ? 'cursor-not-allowed' : ''"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Next</span>
                    <svg class="w-2.5 h-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="m1 9 4-4-4-4" />
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" @click="lastPage" :disabled="currentPage >= totalPages"
                    :class="currentPage >= totalPages ? 'cursor-not-allowed' : ''"
                    class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <span class="sr-only">Next</span>
                    <svg class="w-[23px] " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="m7 16 4-4-4-4m6 8 4-4-4-4" />
                    </svg>
                </button>
            </li>
        </ul>
    </nav>
</template>

<script>
import { initTooltips, initDropdowns } from 'flowbite';
export default {
    props: {
        data: {
            type: Array,
            default: () => []
        },
        columns: {
            type: Array,
            default: () => []
        },
        serverSide: {
            type: Boolean,
            default: false
        },
        totalItems: {
            type: Number,
            default: 0
        },
        perPage: {
            type: Number,
            default: 10
        },
        loading: {
            type: Boolean,
            default: false
        },
        showAddButton: {
            type: Boolean,
            default: false
        },
        showExportButton: {
            type: Boolean,
            default: false
        },
        withCheckbox: {
            type: Boolean,
            default: false
        },
        search: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            searchQuery: '',
            currentPage: 1,
            allRowChecked: false,
            checkedData: []
        };
    },
    created() {
    },
    mounted() {
        initTooltips();
    },
    emits: ['checkbox-changed', 'page-change', 'onAdd', 'onExport', 'search', 'detail-clicked', 'edit-clicked', 'delete-clicked', 'validate-clicked'],
    computed: {
        filteredData() {
            if (!Array.isArray(this.data) || this.data.length === 0) return [];
            if (this.serverSide) return this.data;
            return this.data.filter(row =>
                Object.values(row).some(val => val?.toString().toLowerCase().includes(this.searchQuery.toLowerCase()))
            );
        },
        paginatedData() {
            if (!Array.isArray(this.filteredData) || this.filteredData.length === 0) return [];
            if (this.serverSide) return this.data;
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredData.slice(start, start + this.perPage);
        },
        totalPages() {
            if (!Array.isArray(this.filteredData) || this.filteredData.length === 0) return 1;
            return this.serverSide ? Math.ceil(this.totalItems / this.perPage) : Math.ceil(this.filteredData.length / this.perPage);
        },
        paginationRange() {
            const range = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);

            for (let i = start; i <= end; i++) {
                range.push(i);
            }

            // Ensure the range always has 5 items if possible
            while (range.length < 5 && range[0] > 1) {
                range.unshift(range[0] - 1);
            }
            while (range.length < 5 && range[range.length - 1] < this.totalPages) {
                range.push(range[range.length - 1] + 1);
            }

            return range;
        },
    },
    watch: {
        data: {
            handler() {
                this.initDropdownTable();
            },
            deep: true
        },
        currentPage: {
            handler() {
                this.initDropdownTable();
            },
            deep: true
        }
    },
    methods: {
        firstPage() {
            if (this.currentPage > 1) this.currentPage = 1;
            this.$emit('page-change', this.currentPage);
            this.initDropdownTable();
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
            this.$emit('page-change', this.currentPage);
            this.initDropdownTable();
        },
        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
            this.$emit('page-change', this.currentPage);
            this.initDropdownTable();
        },
        lastPage() {
            if (this.currentPage < this.totalPages) this.currentPage = this.totalPages;
            this.$emit('page-change', this.currentPage);
            this.initDropdownTable();
        },
        goToPage(page) {
            if (this.currentPage !== page) this.currentPage = page;
            this.$emit('page-change', this.currentPage);
            this.initDropdownTable();
        },
        onSearch() {
            this.currentPage = 1;
            this.$emit('search', this.searchQuery);
            this.initDropdownTable();
        },

        //===> ADD & EXPORT BUTTONS <===
        onAddClicked() {
            this.$emit('onAdd');
        },
        onExportClicked() {
            this.$emit('onExport');
        },
        //--------------------------



        //==========> TABLE ACTIONS <=========
        onDetailClicked(item) {
            this.$emit('detail-clicked', item);
        },
        onEditClicked(item) {
            this.$emit('edit-clicked', item);
        },
        onDeleteClicked(item) {
            this.$emit('delete-clicked', item);
        },
        onValidateClicked(item) {
            this.$emit('validate-clicked', item);
        },
        //==========> END TABLE ACTIONS <=========

        onHeaderCheckboxChange(event) {
            this.data.forEach(element => element.checked = event?.target?.checked);
            this.checkedData = this.data.filter(x => x.checked);
            this.$emit('checkbox-changed', this.checkedData);
            this.allRowChecked = event?.target?.checked;
        },
        onRowCheckboxChange(event, index, id) {
            const indexData = this.data.findIndex(item => item.id === id);
            this.data[indexData]['checked'] = event?.target?.checked;
            this.checkedData = this.data.filter(x => x.checked === true);
            console.log(this.checkedData);

            this.allRowChecked = this.checkedData.length == this.data.length;
            this.$emit('checkbox-changed', this.checkedData);
        },
        getNestedValue(obj, path) {
            if (!path || typeof path !== 'string') return '';
            return path.split('.').reduce((acc, part) => acc && acc[part], obj);
        },
        initDropdownTable() {
            this.$nextTick(async () => {
                await new Promise(resolve => setTimeout(resolve, 0));
                initDropdowns();
            });
        },
    },
    unmounted() {
        initDropdowns('destroy');
    }
};
</script>

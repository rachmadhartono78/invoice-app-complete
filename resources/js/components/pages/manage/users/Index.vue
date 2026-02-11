<template>

    <confirmation-dialog :isVisible="dialogConfirmation" title="Hapus data" :loading="loading"
        message="Apakah kamu yakin ingin menghapus data ini?" confirmText="Ya, hapus" confirmButtonColor="red"
        @onClose="handleOnClose($event)" />


    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Kelola Pengguna</h1>
                </div>
            </div>
            <table-molecules 
                :columns="columns" 
                :data="data" 
                :loading="loading" 
                :server-side="true"
                :total-items="pagination.total"
                :per-page="pagination.per_page"
                :search="true"
                :show-add-button="true"
                @detail-clicked="handleDetail($event)"
                @edit-clicked="handleEdit($event)" 
                @delete-clicked="confirmDelete($event)" 
                @onAdd="handleAdd()"
                @page-change="handlePageChange"
                @search="handleSearch">
                
                <!-- Custom slot for organizations column -->
                <template v-slot:column-organizations="{ row }">
                    <div class="max-w-xs">
                        <span v-if="row.organizations_names" 
                              class="text-sm text-gray-900 dark:text-white" 
                              :title="row.organizations_names">
                            {{ truncateText(row.organizations_names, 30) }}
                        </span>
                        <span v-else class="text-sm text-gray-500 dark:text-gray-400">-</span>
                    </div>
                </template>

                <!-- Custom slot for authorities column -->
                <template v-slot:column-authorities="{ row }">
                    <div class="max-w-xs">
                        <span v-if="row.authorities_names" 
                              class="text-sm text-gray-900 dark:text-white"
                              :title="row.authorities_names">
                            {{ truncateText(row.authorities_names, 30) }}
                        </span>
                        <span v-else class="text-sm text-gray-500 dark:text-gray-400">-</span>
                    </div>
                </template>

                <!-- Custom slot for phone column -->
                <template v-slot:column-phone="{ row }">
                    <span v-if="row.phone" class="text-sm text-gray-900 dark:text-white">{{ row.phone }}</span>
                    <span v-else class="text-sm text-gray-500 dark:text-gray-400">-</span>
                </template>

            </table-molecules>
        </div>
    </div>

</template>


<script>
import { initModals, Modal } from 'flowbite';
import TableMolecules from '../../../molecules/TableMolecules.vue';
import ConfirmationDialog from '../../../interactions/Confirmation.vue';

export default {
    components: {
        TableMolecules,
        ConfirmationDialog
    },
    data() {
        return {
            loading: false,
            columns: [
                {
                    name: 'No.',
                    prop: 'number',
                },
                {
                    name: 'Nama',
                    prop: 'name',
                },
                {
                    name: 'Email',
                    prop: 'email',
                },
                {
                    name: 'No. Hp',
                    prop: 'phone',
                },
                {
                    name: 'Organisasi',
                    prop: 'organizations',
                },
                {
                    name: 'Otoritas',
                    prop: 'authorities',
                },
                {
                    name: 'Aksi',
                    prop: 'actions',
                },
            ],
            data: [],
            pagination: {
                current_page: 1,
                per_page: 10,
                total: 0,
                last_page: 1,
                from: 0,
                to: 0,
            },
            searchQuery: '',
            dialogConfirmation: false,
            selectedRow: {},
        }
    },
    created() {
    },
    mounted() {
        this.doGetData();
    },
    beforeUnmount() {

    },
    watch: {
    },
    methods: {
        doGetData(page = 1, search = '') {
            this.loading = true;
            const params = {
                page: page,
                per_page: this.pagination.per_page,
            };
            
            if (search) {
                params.search = search;
            }
            
            this.axios.get('v1/master/user', { params })
                .then(response => {
                    this.data = response.data.map(user => {
                        // Add action permissions
                        user.actions = { canUpdate: true, canDelete: true };
                        return user;
                    });
                    
                    this.pagination = response.pagination;
                    console.log('Pagination:', this.pagination);
                    console.log('Data:', this.data);
                    
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    this.$emit('showToast', 'Gagal memuat data pengguna', 'error');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        handlePageChange(page) {
            this.doGetData(page, this.searchQuery);
        },

        handleSearch(searchQuery) {
            this.searchQuery = searchQuery;
            this.doGetData(1, searchQuery);
        },

        handleAdd() {
            this.$router.push({ name: 'create-users' });
        },

        async handleDetail(event) {
            this.$router.push({ name: 'detail-users', params: { id: event.id } });
        },

        handleEdit(event) {
            this.$router.push({ name: 'update-users', params: { id: event.id } });
        },

        handleOnClose(event) {
            if (event === true) {
                this.submitDelete();
            } else {
                this.dialogConfirmation = false;
            }
        },

        confirmDelete(row) {
            this.selectedRow = row;
            this.dialogConfirmation = true;
        },

        submitDelete() {
            const url = `v1/master/user/${this.selectedRow.id}`;
            this.loading = true;
            
            this.axios.delete(url)
                .then((res) => {
                    this.$emit('showToast', res.data?.message || 'Berhasil menghapus pengguna!', 'success');
                    this.dialogConfirmation = false;
                    this.doGetData(this.pagination.current_page, this.searchQuery);
                })
                .catch((err) => {
                    this.loading = false;
                    this.$emit('showToast', err.response?.data?.message || 'Gagal menghapus pengguna', 'error');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        truncateText(text, maxLength) {
            if (!text) return '';
            return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
        }
    }
};
</script>
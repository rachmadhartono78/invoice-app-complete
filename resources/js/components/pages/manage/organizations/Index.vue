<template>

    <confirmation-dialog :isVisible="dialogConfirmation" title="Hapus data" :loading="loading"
        message="Apakah kamu yakin ingin menghapus data ini?" confirmText="Ya, hapus" confirmButtonColor="red"
        @onClose="handleOnClose($event)" />


    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Kelola Organisasi</h1>
                    <button @click="handleAdd"
                        class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                        <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Tambah Organisasi
                    </button>
                </div>
            </div>
            <table-molecule :columns="columns" :data="data" :loading="loading" @detail-clicked="handleDetail($event)"
                @edit-clicked="handleEdit($event)" @delete-clicked="confirmDelete($event)" @onAdd="handleAdd()">
                <template v-slot:column-status="{ row }">
                    <span v-if="row.is_active"
                        class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-green-900 dark:text-green-300">Aktif</span>
                    <span v-else
                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-red-900 dark:text-red-300">Nonaktif</span>
                </template>
            </table-molecule>
        </div>
    </div>


    <!-- Detail modal -->
    <BaseLiteModal :show="modalRead" title="Detail" persistent @close="modalRead = false">
        <dl>
            <template v-for="(value, key) in columns" :key="key">
                <template v-if="value.prop !== 'number' && value.prop !== 'actions'">
                    <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">{{ value.name }}</dt>
                    <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">{{ selectedRow[value.prop] ??
                        '-'}}</dd>
                </template>
            </template>
        </dl>
        <!-- <template #footer>
            <button class="text-white bg-primary-600 hover:bg-primary-700 px-4 py-2 rounded" @click="modalRead = false">
                Tutup
            </button>
        </template> -->
    </BaseLiteModal>


</template>


<script>
import BaseLiteModal from '../../../atoms/BaseLiteModal.vue';
import { initModals, Modal } from 'flowbite';

export default {
    components: {
        BaseLiteModal
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
                    name: 'Kode',
                    prop: 'code',
                },
                {
                    name: 'Tipe',
                    prop: 'type',
                },
                {
                    name: 'Kota',
                    prop: 'city',
                },
                {
                    name: 'Longitude',
                    prop: 'longitude',
                },
                {
                    name: 'Latitude',
                    prop: 'latitude',
                },
                {
                    name: 'Status',
                    prop: 'status',
                },
                {
                    name: 'Aksi',
                    prop: 'actions',
                },
            ],
            data: [],
            count: 0,
            user: {
                id: '',
                name: '',
                email: '',
                is_active: true,
            },
            modalRead: false,
            dialogConfirmation: false,
            selectedRow: {},
        }
    },
    created() {
    },
    mounted() {
        this.doGetData();
        console.log(this.modalRead, 'modalRead');


    },
    beforeUnmount() {

    },
    watch: {
        modalRead(val) {
            console.log('modalRead:', val);
        }
    },
    methods: {
        doGetData() {
            this.loading = true;
            this.axios.get('v1/settings/organizations')
                .then(response => {
                    const data = response.data.data || response.data;
                    this.data = data.map(x => {
                        x.status = x.is_active == 1 ? 'Aktif' : 'Nonaktif';
                        x.longitude = x.longitude || '-';
                        x.latitude = x.latitude || '-';
                        return x;
                    });
                    this.count = response.data.count || data.length;
                }).catch(error => {
                    console.log(error);
                    this.$emit('showToast', 'Gagal memuat data organisasi', 'error');
                }).finally(() => this.loading = false);
        },
        handleAdd() {
            this.$router.push({name: 'create-organizations' });
        },
        handleDetail(event) {
            this.selectedRow = event;
            this.modalRead = true;
        },
        handleEdit(event) {
            this.modalRead = false;
            this.$router.push({name: 'update-organizations', params: { id: event.id }});
        },
        handleOnClose(event) {
            if (event === true) {
                console.log(event);

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
            let url = "v1/settings/organizations/" + this.selectedRow.id;
            this.loading = true;
            this.axios
                .delete(url)
                .then((res) => {
                    this.$emit('showToast', res.message || 'Berhasil!', 'success');
                    this.dialogConfirmation = false;
                    this.doGetData();
                })
                .catch((err) => {
                    this.loading = false;
                    this.$emit('showToast', err.message, 'error');
                })
                .finally(() => {
                    this.loading = false;

                });
        }
    }
};
</script>
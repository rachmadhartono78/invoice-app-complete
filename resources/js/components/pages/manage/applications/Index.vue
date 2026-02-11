<template>

    <confirmation-dialog :isVisible="dialogConfirmation" title="Hapus data" :loading="loading"
        message="Apakah kamu yakin ingin menghapus data ini?" confirmText="Ya, hapus" confirmButtonColor="red"
        @onClose="handleOnClose($event)" />


    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700 rounded-xl">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Kelola Aplikasi</h1>
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

    <!-- Main modal -->
    <BaseLiteModal :show="modalRead" title="Detail" persistent @close="modalRead = false">
        <dl>
            <template v-for="(value, key) in columns" :key="key">
                <template v-if="value.prop !== 'number' && value.prop !== 'actions'">
                    <dt class="mb-2 font-semibold leading-none text-gray-900 dark:text-white">{{ value.name }}
                    </dt>
                    <dd class="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">{{
                        selectedRow[value.prop] ?? '-' }}</dd>
                </template>
            </template>
        </dl>
    </BaseLiteModal>


</template>


<script>
import BaseLiteModal from '../../../atoms/BaseLiteModal.vue';


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
                    name: 'URL',
                    prop: 'url',
                },
                {
                    name: 'Deskripsi',
                    prop: 'description',
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
    },
    beforeUnmount() {

    },
    watch: {
    },
    methods: {
        doGetData() {
            this.loading = true;
            this.axios.get('v1/settings/applications')
                .then(response => {
                    const data = response.data;
                    this.data = data.map(x => {
                        x.status = x.is_active == 1 ? 'Active' : 'Inactive';
                        return x;
                    });
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        handleAdd() {
            this.$router.push({ name: 'index-applications' });
        },
        handleDetail(event) {
            this.selectedRow = event;
            this.modalRead = true;
        },
        handleEdit(event) {
            this.modalRead = false;
            this.$router.push({ name: 'update-applications', params: { id: event.id } });
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
            let url = "v1/settings/applications/" + this.selectedRow.id;
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
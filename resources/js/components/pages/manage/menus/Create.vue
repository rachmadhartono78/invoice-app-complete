<template>
    <div>

        <confirmation-dialog :isVisible="dialogConfirmation" title="Simpan data" :loading="loading"
            message="Apakah kamu yakin ingin menyimpan data ini?" @onClose="handleOnClose($event)" />

        <form @submit.prevent="confirmSubmit">

            <div class="relative sm:p-4 xs:p-0 w-full h-full md:h-auto">

                <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                    <div
                        class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ id ? 'Ubah' : 'Tambah' }} Menu
                        </h3>
                    </div>
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Menu
                                Induk
                            </label>
                            <template v-if="id && form.menu_induk">
                                <input type="text" name="menu_id" id="menu_id" :value="form?.menu_induk?.name" disabled
                                    class="cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Menu induk" required="">
                            </template>
                            <template v-else>
                                <autocomplete :items="menus" placeholder="Cari menu..." label-key="name"
                                    v-model="form.menu_id" />
                            </template>
                        </div>
                        <div>
                            <label for="text"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Aplikasi
                            </label>
                            <autocomplete :items="applications" placeholder="Cari aplikasi..." label-key="name" required
                                v-model="form.application_id" />
                        </div>
                    </div>
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama</label>
                            <input type="text" name="name" id="name" v-model="form.name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nama" required="">
                        </div>
                        <div>
                            <label for="url"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">URL</label>
                            <input type="text" name="url" id="url" v-model="form.url"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="URL Menu" required="">
                        </div>
                        <div>
                            <label for="order"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order</label>
                            <input type="number" name="order" id="order" v-model="form.order"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Order" required="">
                        </div>
                        <div>
                            <label for="url"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi</label>
                            <textarea v-model="form.description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"></textarea>

                        </div>
                        <div>
                            <div class="flex flex-col justify-center items-center" v-if="form.icon">
                                <label for="url"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Icon
                                    Preview</label>
                                <span v-html="form.icon"></span>
                            </div>
                        </div>
                        <div>
                            <label for="url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Icon
                                SVG</label>
                            <textarea v-model="form.icon" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                    <button type="button" @click.prevent="$router.go(-1)"
                        class="text-gray-600 dark:text-white hover:text-white flex items-center justify-center  w-full sm:w-auto  border border-gray-700 dark:border-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center  dark:hover:bg-gray-700 dark:focus:ring-primary-800">
                        <svg class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m14 8-4 4 4 4" />
                        </svg>
                        <span>Kembali</span>
                    </button>
                    <button type="submit" @click="handleTypeSubmit('createNew')" v-show="!id"
                        class="text-primary-600 dark:text-primary-300 hover:text-white flex items-center justify-center  space-x-2  w-full sm:w-auto  border border-primary-700 hover:border-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <svg class="mr-1 -ml-1 w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2"
                                d="M16.5 15v1.5m0 0V18m0-1.5H15m1.5 0H18M3 9V6a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v3M3 9v6a1 1 0 0 0 1 1h5M3 9h16m0 0v1M6 12h3m12 4.5a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                        </svg>
                        <span>Simpan & Buat baru</span>
                    </button>
                    <button type="submit" @click="handleTypeSubmit('create')"
                        class="text-white flex items-center justify-center  w-full sm:w-auto  bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <svg v-show="!id" class="mr-1 -ml-1 w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg v-show="id" class="mr-1 -ml-1 w-6 h-6" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="1"
                                d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                        <span>{{ id ? 'Simpan perubahan' : 'Simpan' }}</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
</template>

<script>

export default {
    data() {
        return {
            loading: false,
            form: {
                application_id: '',
                name: '',
                url: '',
                icon: '',
                description: '',
                order: null,
            },
            modalCreate: null,
            dialogConfirmation: false,
            typeSubmit: '',
            id: '',
            applications: [],
            menus: [],
        }
    },
    created() {
        this.id = this.$route.params.id;
        if (this.id) {
            this.doGetDataDetail(this.id);
        }
    },
    mounted() {
        this.retrieveApplication();
        this.retrieveMenu();
    },
    beforeUnmount() {

    },
    watch: {

    },
    methods: {
        retrieveApplication() {
            this.loading = true;
            this.axios.get('v1/settings/applications')
                .then(response => {
                    this.applications = response.data;
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        retrieveMenu() {
            this.loading = true;
            this.axios.get('v1/settings/menus')
                .then(response => {
                    this.menus = response.data;
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        doGetDataDetail() {
            this.loading = true;
            this.axios.get('v1/settings/menus/' + this.id)
                .then(response => {
                    const data = response.data;
                    this.form = data;
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        confirmSubmit(typeSubmit) {
            this.dialogConfirmation = true;
        },
        handleTypeSubmit(typeSubmit) {
            this.typeSubmit = typeSubmit;
        },
        resetForm() {
            this.form = {
                application_id: '',
                name: '',
                url: '',
                icon: '',
                description: '',
                order: null,
            };
        },
        handleOnClose(event) {
            if (event === true) {
                this.submit();
            } else {
                this.dialogConfirmation = false;
            }
        },
        handleDepartmentSelect(event) {

        },
        submit() {
            let url = "v1/settings/menus";
            this.loading = true;

            let body = JSON.parse(JSON.stringify(this.form));
            if (this.id) {
                url += '/' + this.id;
                body['_method'] = 'PUT';
            }
            this.axios
                .post(url, body)
                .then((res) => {
                    this.$emit('showToast', res.message || 'Berhasil!', 'success');
                    this.dialogConfirmation = false;
                    this.typeSubmit === 'createNew' ? this.resetForm() : this.$router.push({name: 'index-menus'});
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
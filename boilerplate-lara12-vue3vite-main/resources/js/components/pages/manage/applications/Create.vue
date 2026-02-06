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
                            {{ id ? 'Ubah' : 'Tambah' }} Pengguna
                        </h3>
                    </div>
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama</label>
                            <input type="text" name="name" id="name" v-model="user.name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nama" required="">
                        </div>
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" name="email" id="email" v-model="user.email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Email" required="">
                        </div>
                        <div>
                            <label for="no_induk"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Induk</label>
                            <input type="no_induk" name="no_induk" id="email" v-model="user.no_induk"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nomor Induk" required="">
                        </div>
                        <div>
                            <label for="text"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Departemen</label>
                            <autocomplete :items="departments" placeholder="Search department..." multiple
                                label-key="name" v-model="user.departments" @select="handleDepartmentSelect" />
                        </div>
                        <div class="col-span-2">
                            <label for="text"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Roles</label>
                            <ul
                                class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <li v-for="role in roles"
                                    class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                                    <div class="flex items-center ps-3">
                                        <input :id="`checkbox-${role.id}`" type="checkbox" :value="role.id" v-model="user.roles"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label :for="`checkbox-${role.id}`"
                                            class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ role.name }}</label>
                                    </div>
                                </li>
                            </ul>

                        </div>
                    </div>

                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                    <button type="button" @click.prevent="$router.push({name:'index-users'})"
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
import { serialize } from "object-to-formdata";


export default {
    data() {
        return {
            loading: false,
            user: {
                name: '',
                email: '',
                roles: [],
                departments: []
            },
            modalCreate: null,
            dialogConfirmation: false,
            typeSubmit: '',
            id: '',
            departments: [
                { id: 1, name: 'Department 1' },
                { id: 2, name: 'Department 2' },
                { id: 3, name: 'Department 3' },
                { id: 4, name: 'Department 4' },
                { id: 5, name: 'Department 5' },
                { id: 6, name: 'Department 6' },
                { id: 7, name: 'Department 7' },
                { id: 8, name: 'Department 8' },
                { id: 9, name: 'Department 9' },
                { id: 10, name: 'Department 10' },
                { id: 11, name: 'Department 11' }
            ],
            roles: [
                { id: 1, name: 'Mahasiswa'},
                { id: 2, name: 'DPK'},
                { id: 3, name: 'Dosen'},
                { id: 4, name: 'Staff'},
                { id: 5, name: 'Prodi'},
                { id: 6, name: 'Admin'},
            ]
        }
    },
    created() {
        this.id = this.$route.params.id;
        if (this.id) {
            this.doGetDataDetail(this.user.id);
        }
    },
    mounted() {

    },
    beforeUnmount() {

    },
    watch: {

    },
    methods: {
        doGetDataDetail() {
            this.loading = true;
            this.axios.get('/v1/configuration/global/' + this.id)
                .then(response => {
                    this.user = response.data;
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
            this.user = {
                id: '',
                condition: '',
                code: '',
                name: '',
                value: '',
                is_active: true,
                modifier: '',
            }
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
            let url = "v1/master/user";
            this.loading = true;
            const options = {
                indices: false,
                nullsAsUndefineds: false,
                booleansAsIntegers: false,
                allowEmptyArrays: false,
                noFilesWithArrayNotation: false,
                dotsForObjectNotation: false,
            };
            let formData = new FormData();
            formData = serialize(this.user, options);
            if (this.id) {
                url += '/' + this.id;
                formData.append('_method', 'PUT');
            }
            this.axios
                .post(url, formData)
                .then((res) => {
                    this.$emit('showToast', res.message || 'Berhasil!', 'success');
                    this.dialogConfirmation = false;
                    this.typeSubmit === 'createNew' ? this.resetForm() : this.$router.push({name:'index-users'});
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
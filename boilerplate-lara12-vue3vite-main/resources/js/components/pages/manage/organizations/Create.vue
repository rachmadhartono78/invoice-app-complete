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
                            {{ id ? 'Ubah' : 'Tambah' }} Organisasi
                        </h3>
                    </div>
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Organisasi</label>
                            <input type="text" name="name" id="name" v-model="form.name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nama Organisasi" required="">
                        </div>
                        <div>
                            <label for="code"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Organisasi</label>
                            <input type="text" name="code" id="code" v-model="form.code"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Kode Organisasi" required="">
                        </div>
                        <div>
                            <label for="type"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipe Organisasi</label>
                            <select name="type" id="type" v-model="form.type"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                required="">
                                <option value="">Pilih Tipe</option>
                                <option value="headquarters">Kantor Pusat</option>
                                <option value="branch">Cabang</option>
                                <option value="division">Divisi</option>
                                <option value="department">Departemen</option>
                                <option value="unit">Unit</option>
                            </select>
                        </div>
                        <div>
                            <label for="city"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota</label>
                            <input type="text" name="city" id="city" v-model="form.city"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Kota" required="">
                        </div>
                        <div>
                            <label for="longitude"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Longitude</label>
                            <input type="number" step="any" name="longitude" id="longitude" v-model="form.longitude"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="107.6191">
                        </div>
                        <div>
                            <label for="latitude"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Latitude</label>
                            <input type="number" step="any" name="latitude" id="latitude" v-model="form.latitude"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="-6.9175">
                        </div>
                        <div>
                            <label for="organization_id"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Organisasi Induk</label>
                            <autocomplete :items="organizations" placeholder="Cari organisasi induk..." 
                                label-key="name" value-key="id" v-model="form.organization_id" />
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input id="is_active" type="checkbox" v-model="form.is_active"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                            <div class="ml-2 text-sm">
                                <label for="is_active" class="font-medium text-gray-900 dark:text-gray-300">
                                    Status Aktif
                                </label>
                                <p class="text-xs font-normal text-gray-500 dark:text-gray-300">
                                    Centang jika organisasi ini aktif
                                </p>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label for="address"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                            <textarea name="address" id="address" rows="3" v-model="form.address"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Alamat lengkap organisasi"></textarea>
                        </div>
                    </div>

                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-5">
                    <button type="button" @click.prevent="$router.push({name: 'index-organizations'})"
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
            form: {
                name: '',
                code: '',
                type: '',
                city: '',
                address: '',
                longitude: null,
                latitude: null,
                organization_id: null,
                is_active: true
            },
            modalCreate: null,
            dialogConfirmation: false,
            typeSubmit: '',
            id: '',
            organizations: []
        }
    },
    created() {
        this.id = this.$route.params.id;
        this.loadOrganizations();
        if (this.id) {
            this.doGetDataDetail(this.id);
        }
    },
    mounted() {

    },
    beforeUnmount() {

    },
    watch: {

    },
    methods: {
        loadOrganizations() {
            this.loading = true;
            this.axios.get('v1/settings/organizations')
                .then(response => {
                    this.organizations = (response.data.data || response.data).filter(org => org.id !== this.id);
                }).catch(error => {
                    console.log(error);
                    this.$emit('showToast', 'Gagal memuat data organisasi', 'error');
                }).finally(() => this.loading = false);
        },
        doGetDataDetail(id) {
            this.loading = true;
            this.axios.get(`v1/settings/organizations/${id}`)
                .then(response => {
                    this.form = response.data.data || response.data;
                    // Ensure boolean fields are properly set
                    this.form.is_active = Boolean(this.form.is_active);
                }).catch(error => {
                    console.log(error);
                    this.$emit('showToast', 'Gagal memuat detail organisasi', 'error');
                }).finally(() => this.loading = false);
        },
        confirmSubmit(typeSubmit) {
            // Basic validation before showing confirmation
            if (!this.form.name || !this.form.code || !this.form.type || !this.form.city) {
                this.$emit('showToast', 'Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }
            
            this.dialogConfirmation = true;
        },
        handleTypeSubmit(typeSubmit) {
            this.typeSubmit = typeSubmit;
        },
        resetForm() {
            this.form = {
                name: '',
                code: '',
                type: '',
                city: '',
                address: '',
                longitude: null,
                latitude: null,
                organization_id: null,
                is_active: true
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
            // Method kept for compatibility if needed
        },
        submit() {
            let url = "v1/settings/organizations";
            this.loading = true;
            
            // Prepare form data
            const submitData = { ...this.form };
            
            // Convert boolean to integer for backend
            submitData.is_active = this.form.is_active ? 1 : 0;
            
            // Convert empty strings to null for longitude and latitude
            if (submitData.longitude === '') submitData.longitude = null;
            if (submitData.latitude === '') submitData.latitude = null;
            if (submitData.organization_id === '') submitData.organization_id = null;
            
            const options = {
                indices: false,
                nullsAsUndefineds: false,
                booleansAsIntegers: false,
                allowEmptyArrays: false,
                noFilesWithArrayNotation: false,
                dotsForObjectNotation: false,
            };
            
            let formData = new FormData();
            formData = serialize(submitData, options);
            
            if (this.id) {
                url += '/' + this.id;
                formData.append('_method', 'PUT');
            }
            
            this.axios
                .post(url, formData)
                .then((res) => {
                    this.$emit('showToast', res.data?.message || 'Berhasil!', 'success');
                    this.dialogConfirmation = false;
                    this.typeSubmit === 'createNew' ? this.resetForm() : this.$router.push({name: 'index-organizations'});
                })
                .catch((err) => {
                    this.loading = false;
                    console.error('Error submitting organization:', err);
                    this.$emit('showToast', err.response?.data?.message || err.message, 'error');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
};
</script>
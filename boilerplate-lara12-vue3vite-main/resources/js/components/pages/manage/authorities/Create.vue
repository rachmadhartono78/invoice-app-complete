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
                            {{ id ? 'Ubah' : 'Tambah' }} Otoritas
                        </h3>
                    </div>
                    <div class="grid gap-4 mb-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                otoritas</label>
                            <input type="text" name="name" id="name" v-model="form.name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Nama otoritas" required="">
                        </div>
                        <div>
                            <label for="text"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Aplikasi
                            </label>
                            <autocomplete :items="applications" placeholder="Cari aplikasi..." label-key="name"
                                v-model="form.application_id" />
                        </div>
                        <div>
                            <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                                otoritas</label>
                            <input type="text" name="code" id="code" v-model="form.code" :disabled="id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Kode otoritas" required="">
                        </div>
                    </div>
                </div>

                <h3 class="font-semibold text-gray-900 dark:text-white mt-5 mb-3">
                    Menu & Aksi
                </h3>
                
                <!-- Menu Actions Table -->
                <div v-if="form.menus.length > 0" class="relative overflow-x-auto shadow-md sm:rounded-lg mb-6">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 w-1/4">
                                    Menu
                                </th>
                                <th scope="col" class="px-6 py-3 text-center" v-for="action in actions" :key="action.id">
                                    {{ action.name }}
                                </th>
                                <th scope="col" class="px-6 py-3 w-20 text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(menu, menuIndex) in form.menus" :key="menuIndex"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">{{ getMenuName(menu.id) }}</span>
                                        <span class="text-xs text-gray-500">{{ getMenuUrl(menu.id) }}</span>
                                    </div>
                                </td>
                                <td v-for="action in actions" :key="action.id" class="px-6 py-4 text-center">
                                    <input 
                                        :id="`checkbox-${menuIndex}-${action.id}`" 
                                        type="checkbox"
                                        :checked="isActionChecked(menuIndex, action.id)"
                                        @change="handleActionToggle(menuIndex, action.id, $event)"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" @click="removeMenus(menuIndex)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        title="Hapus Menu">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Individual Menu Cards (Alternative display for smaller screens) -->
                <div class="block md:hidden">
                    <div v-for="(menu, menuIndex) in form.menus" :key="menuIndex"
                        class="relative p-4 bg-white my-4 rounded-lg shadow dark:bg-gray-800 sm:p-5">
                        <div class="absolute right-0 top-2">
                            <button type="button" @click="removeMenus(menuIndex)"
                                class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm p-1.5 text-center inline-flex items-center me-2 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="sr-only">Delete Icon</span>
                            </button>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ getMenuName(menu.id) }}</h4>
                            <p class="text-sm text-gray-500">{{ getMenuUrl(menu.id) }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div v-for="action in actions" :key="action.id" class="flex items-center">
                                <input 
                                    :id="`mobile-checkbox-${menuIndex}-${action.id}`" 
                                    type="checkbox"
                                    :checked="isActionChecked(menuIndex, action.id)"
                                    @change="handleActionToggle(menuIndex, action.id, $event)"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label :for="`mobile-checkbox-${menuIndex}-${action.id}`" 
                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ action.name }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Menu Section -->
                <div class="relative p-4 bg-white my-4 rounded-lg shadow dark:bg-gray-800 sm:p-5">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Tambah Menu Baru</h4>
                    <div class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label for="newMenuSelect" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Pilih Menu
                            </label>
                            <autocomplete 
                                :items="availableMenus" 
                                placeholder="Cari menu..." 
                                label-key="name" 
                                value-key="id"
                                v-model="selectedNewMenu" 
                                @input="handleMenuSelect"
                            />
                        </div>
                        <button 
                            type="button" 
                            @click="addSelectedMenu"
                            :disabled="!selectedNewMenu"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                            Tambah
                        </button>
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
import { serialize } from "object-to-formdata";


export default {
    data() {
        return {
            loading: false,
            form: {
                application_id: '',
                name: '',
                code: '',
                menus: []
            },
            modalCreate: null,
            dialogConfirmation: false,
            typeSubmit: '',
            id: '',
            applications: [],
            menus: [],
            actions: [],
            selectedNewMenu: null,
        }
    },
    computed: {
        availableMenus() {
            const selectedMenuIds = this.form.menus.map(menu => menu.id);
            return this.menus.filter(menu => !selectedMenuIds.includes(menu.id));
        }
    },
    created() {
        this.id = this.$route.params.id;
        this.retrieveApplication();
        this.retrieveMenu();
        this.retrieveActions().then(() => {
            if (this.id) {
                this.doGetDataDetail(this.id);
            }
        });
    },
    mounted() {

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
                    this.menus = response.data.map(menu => {
                        menu.name = `${menu.name} - ${menu.url}`;
                        return menu;
                    });
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        retrieveActions() {
            console.log('get actions');

            this.loading = true;
            return this.axios.get('v1/settings/actions')
                .then(response => {
                    this.actions = response.data;
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        doGetDataDetail() {
            console.log('get data detail');

            this.loading = true;
            this.axios.get('v1/settings/authorities/' + this.id)
                .then(response => {
                    this.form = response.data;
                    // Ensure each menu has proper actions structure
                    this.form.menus.forEach(menu => {
                        if (!menu.actions || !menu.actions.length) {
                            menu.actions = this.createDefaultActions();
                        } else {
                            // Ensure all actions are present
                            menu.actions = this.normalizeMenuActions(menu.actions);
                        }
                    });
                }).catch(error => {
                    console.log(error);
                }).finally(() => this.loading = false);
        },
        
        createDefaultActions() {
            return this.actions.map(action => ({
                action: action,
                action_id: action.id,
                value: 0
            }));
        },

        normalizeMenuActions(existingActions) {
            // Create a map of existing actions
            const existingMap = {};
            existingActions.forEach(act => {
                const actionId = act.action?.id || act.action_id;
                existingMap[actionId] = act;
            });

            // Ensure all available actions are present
            return this.actions.map(action => {
                if (existingMap[action.id]) {
                    return existingMap[action.id];
                } else {
                    return {
                        action: action,
                        action_id: action.id,
                        value: 0
                    };
                }
            });
        },

        getMenuName(menuId) {
            const menu = this.menus.find(m => m.id === menuId);
            return menu ? menu.name.split(' - ')[0] : 'Menu tidak ditemukan';
        },

        getMenuUrl(menuId) {
            const menu = this.menus.find(m => m.id === menuId);
            return menu ? menu.url : '';
        },

        isActionChecked(menuIndex, actionId) {
            const menu = this.form.menus[menuIndex];
            if (!menu || !menu.actions) return false;
            
            const action = menu.actions.find(act => {
                const actId = act.action?.id || act.action_id;
                return actId === actionId;
            });
            
            return action ? action.value == 1 : false;
        },

        handleActionToggle(menuIndex, actionId, event) {
            const menu = this.form.menus[menuIndex];
            if (!menu || !menu.actions) return;
            
            const actionIndex = menu.actions.findIndex(act => {
                const actId = act.action?.id || act.action_id;
                return actId === actionId;
            });
            
            if (actionIndex !== -1) {
                menu.actions[actionIndex].value = event.target.checked ? 1 : 0;
            }
        },

        handleMenuSelect(selectedMenu) {
            this.selectedNewMenu = selectedMenu;
            console.log('Selected menu:', selectedMenu);
        },

        addSelectedMenu() {
            if (!this.selectedNewMenu) return;
            
            // Handle both object and ID formats from autocomplete
            const menuId = typeof this.selectedNewMenu === 'object' ? this.selectedNewMenu.id : this.selectedNewMenu;
            const menuObj = this.menus.find(m => m.id === menuId);
            
            if (!menuObj) {
                this.$emit('showToast', 'Menu tidak ditemukan', 'error');
                return;
            }
            
            const newMenu = {
                id: menuObj.id,
                name: menuObj.name,
                url: menuObj.url,
                actions: this.createDefaultActions()
            };
            
            this.form.menus.push(newMenu);
            this.selectedNewMenu = null;
        },
        confirmSubmit(typeSubmit) {
            // Basic validation before showing confirmation
            if (!this.form.name || !this.form.code || !this.form.application_id) {
                this.$emit('showToast', 'Harap lengkapi semua field yang wajib diisi', 'error');
                return;
            }
            
            // Check if we have any valid menus
            const validMenus = this.form.menus.filter(menu => menu.id && menu.id !== '');
            if (validMenus.length === 0) {
                this.$emit('showToast', 'Harap tambahkan minimal satu menu', 'error');
                return;
            }
            
            this.dialogConfirmation = true;
        },
        removeMenus(index) {
            this.form.menus.splice(index, 1);
        },
        handleTypeSubmit(typeSubmit) {
            this.typeSubmit = typeSubmit;
        },
        resetForm() {
            this.form = {
                application_id: '',
                name: '',
                code: '',
                menus: []
            };
            this.selectedNewMenu = null;
        },
        handleOnClose(event) {
            if (event === true) {
                this.submit();
            } else {
                this.dialogConfirmation = false;
            }
        },
        handleDepartmentSelect(event) {
            // Method kept for compatibility
        },
        submit() {
            let url = "v1/settings/authorities";
            this.loading = true;
            
            // Filter out menus without valid IDs and prepare form data with proper structure
            const validMenus = this.form.menus.filter(menu => menu.id && menu.id !== '');
            
            const submitData = {
                ...this.form,
                menus: validMenus.map(menu => ({
                    id: menu.id,
                    actions: menu.actions.map(action => ({
                        action_id: action.action?.id || action.action_id,
                        value: action.value || 0
                    })).filter(action => action.action_id) // Filter out actions without valid action_id
                }))
            };
            
            // Debug logging
            console.log('Submit data:', submitData);
            console.log('Valid menus count:', validMenus.length);
            
            const options = {
                indices: true,
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
                    this.typeSubmit === 'createNew' ? this.resetForm() : this.$router.push({ name: 'index-authorities' });
                })
                .catch((err) => {
                    this.loading = false;
                    console.error('Error submitting authority:', err);
                    console.error('Error response:', err.response?.data);
                    this.$emit('showToast', err.response?.data?.message || err.message, 'error');
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
};
</script>
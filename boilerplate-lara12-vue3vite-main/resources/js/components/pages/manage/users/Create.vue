<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-3">
                    <button @click="$router.go(-1)"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ id ? 'Edit' : 'Tambah' }} Pengguna
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ id ? 'Perbarui informasi pengguna' : 'Buat akun pengguna baru' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Confirmation Dialog -->
            <confirmation-dialog :isVisible="dialogConfirmation" title="Konfirmasi Simpan" :loading="loading"
                message="Apakah Anda yakin ingin menyimpan perubahan ini?" confirm-text="Ya, Simpan" cancel-text="Batal"
                @onClose="handleOnClose($event)" />

            <!-- Main Content -->
            <div v-if="loadingFetchUser" class="flex justify-center items-center py-12">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto"></div>
                    <p class="text-gray-500 dark:text-gray-400 mt-4">Memuat data pengguna...</p>
                </div>
            </div>

            <div v-else>
                <form @submit.prevent="confirmSubmit" class="space-y-6">
                    <!-- Personal Information Card -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informasi Personal
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Data dasar pengguna dan kontak utama
                            </p>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="lg:col-span-1">
                                    <label for="name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" v-model="user.name" :class="[
                                        'block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors',
                                        formErrors.name
                                            ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-600'
                                            : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
                                    ]" placeholder="Masukkan nama lengkap" required />
                                    <p v-if="formErrors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.name[0] }}
                                    </p>
                                </div>

                                <!-- Registration Number -->
                                <div class="lg:col-span-1">
                                    <label for="registration_number"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nomor Induk
                                    </label>
                                    <input type="text" id="registration_number" v-model="user.registration_number"
                                        :class="[
                                            'block w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors',
                                            formErrors.registration_number
                                                ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-600'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
                                        ]" placeholder="Contoh: EMP-2024-001" />
                                    <p v-if="formErrors.registration_number"
                                        class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.registration_number[0] }}
                                    </p>
                                </div>

                                <!-- Primary Email -->
                                <div class="lg:col-span-1">
                                    <label for="email"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Utama <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" v-model="user.email" :class="[
                                            'block w-full pl-10 pr-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors',
                                            formErrors.email
                                                ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-600'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
                                        ]" placeholder="contoh@domain.com" required />
                                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </div>
                                    <p v-if="formErrors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.email[0] }}
                                    </p>
                                </div>

                                <!-- Primary Phone -->
                                <div class="lg:col-span-1">
                                    <label for="phone"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        No HP/WhatsApp Utama <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone" v-model="user.phone" :class="[
                                            'block w-full pl-10 pr-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors',
                                            formErrors.phone
                                                ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-600'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
                                        ]" placeholder="08123456789" required />
                                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <p v-if="formErrors.phone" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formErrors.phone[0] }}
                                    </p>
                                </div>

                                <!-- Organizations -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Organisasi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="space-y-2">
                                        <autocomplete :items="organizations" placeholder="Pilih organisasi..." multiple
                                            label-key="name" v-model="user.organizations"
                                            :class="formErrors.organizations ? 'border-red-300' : ''" />
                                        <p v-if="formErrors.organizations"
                                            class="text-sm text-red-600 dark:text-red-400">
                                            {{ formErrors.organizations[0] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Pilih satu atau lebih organisasi untuk pengguna ini
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Identifiers Section -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                                Identifikasi Tambahan
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Email, nomor telepon, dan username alternatif (opsional)
                            </p>
                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Additional Emails -->
                            <div>
                                <identifier-input v-model="emailIdentifiers" type="email" label="Email Tambahan"
                                    placeholder="email@example.com" input-type="email"
                                    :error="formErrors.identifiers_email" />

                                <!-- Deleted Emails (if editing) -->
                                <div v-if="deletedEmailIdentifiers.length > 0" class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Email yang dihapus (dapat dipulihkan):
                                    </p>
                                    <div class="space-y-2">
                                        <div v-for="identifier in deletedEmailIdentifiers" :key="identifier.id"
                                            class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 line-through">
                                                {{ identifier.value }}
                                            </span>
                                            <button type="button" @click="restoreIdentifier(identifier, 'email')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Pulihkan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Phones -->
                            <div>
                                <identifier-input v-model="phoneIdentifiers" type="phone"
                                    label="No HP/WhatsApp Tambahan" placeholder="08123456789" input-type="tel"
                                    :error="formErrors.identifiers_phone" />

                                <!-- Deleted Phones (if editing) -->
                                <div v-if="deletedPhoneIdentifiers.length > 0" class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        No HP yang dihapus (dapat dipulihkan):
                                    </p>
                                    <div class="space-y-2">
                                        <div v-for="identifier in deletedPhoneIdentifiers" :key="identifier.id"
                                            class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 line-through">
                                                {{ identifier.value }}
                                            </span>
                                            <button type="button" @click="restoreIdentifier(identifier, 'phone')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Pulihkan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Usernames -->
                            <div>
                                <identifier-input v-model="usernameIdentifiers" type="username" label="Username"
                                    placeholder="username" input-type="text" :error="formErrors.identifiers_username" />

                                <!-- Deleted Usernames (if editing) -->
                                <div v-if="deletedUsernameIdentifiers.length > 0" class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                        Username yang dihapus (dapat dipulihkan):
                                    </p>
                                    <div class="space-y-2">
                                        <div v-for="identifier in deletedUsernameIdentifiers" :key="identifier.id"
                                            class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                            <span class="text-sm text-gray-600 dark:text-gray-300 line-through">
                                                {{ identifier.value }}
                                            </span>
                                            <button type="button" @click="restoreIdentifier(identifier, 'username')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                Pulihkan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Authorities Section -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-4">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Otoritas & Hak Akses
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Tentukan otoritas dan organisasi yang dapat diakses pengguna
                            </p>
                        </div>

                        <div class="p-6">
                            <!-- Existing Authorities -->
                            <div v-if="user.authorities && user.authorities.length > 0" class="space-y-4 mb-6">
                                <div v-for="(authority, index) in user.authorities" :key="index"
                                    class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- Authority Selection -->
                                        <div class="lg:col-span-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Nama Otoritas <span class="text-red-500">*</span>
                                            </label>
                                            <autocomplete :items="authorities" placeholder="Pilih otoritas..."
                                                label-key="name" v-model="authority.authority"
                                                :class="formErrors[`authorities.${index}.authority`] ? 'border-red-300' : ''" />
                                            <p v-if="formErrors[`authorities.${index}.authority`]"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ formErrors[`authorities.${index}.authority`][0] }}
                                            </p>
                                        </div>

                                        <!-- Organizations for this Authority -->
                                        <div class="lg:col-span-1">
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Organisasi untuk Otoritas Ini <span class="text-red-500">*</span>
                                            </label>
                                            <autocomplete :items="organizations" placeholder="Pilih organisasi..."
                                                multiple label-key="name" v-model="authority.organizations"
                                                :class="formErrors[`authorities.${index}.organizations`] ? 'border-red-300' : ''" />
                                            <p v-if="formErrors[`authorities.${index}.organizations`]"
                                                class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                {{ formErrors[`authorities.${index}.organizations`][0] }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Remove Authority Button -->
                                    <div class="flex justify-end mt-4">
                                        <button type="button" @click="removeAuthority(index)"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Otoritas
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Authority Button -->
                            <div
                                class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-primary-400 dark:hover:border-primary-500 transition-colors">
                                <button type="button" @click="addAuthority"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300 dark:hover:bg-primary-800 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Tambah Otoritas
                                </button>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Klik untuk menambah otoritas baru untuk pengguna ini
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6">
                        <button type="button" @click="$router.go(-1)"
                            class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-md transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                            </svg>
                            Kembali
                        </button>

                        <button v-if="!id" type="button" @click="handleTypeSubmit('createNew')" :disabled="loading"
                            class="inline-flex justify-center items-center px-4 py-2 border border-primary-600 dark:border-primary-500 shadow-sm bg-white dark:bg-gray-700 text-base font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span v-if="loading">Menyimpan...</span>
                            <span v-else>Simpan & Buat Baru</span>
                        </button>

                        <button type="button" @click="handleTypeSubmit('create')" :disabled="loading"
                            class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm bg-primary-600 dark:bg-primary-600 text-base font-medium text-white hover:bg-primary-700 dark:hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="!loading" class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path v-if="!id" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span v-if="loading">Menyimpan...</span>
                            <span v-else>{{ id ? 'Simpan Perubahan' : 'Simpan' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { serialize } from "object-to-formdata";
import IdentifierInput from "@/components/molecules/IdentifierInput.vue";
import ConfirmationDialog from "../../../shared/dialog/ConfirmationDialog.vue";

export default {
    components: {
        IdentifierInput,
        ConfirmationDialog
    },
    data() {
        return {
            loading: false,
            loadingFetchUser: false,
            user: {
                name: '',
                email: '',
                phone: '',
                registration_number: '',
                organizations: [],
                authorities: [],
            },
            emailIdentifiers: [],
            phoneIdentifiers: [],
            usernameIdentifiers: [],
            deletedEmailIdentifiers: [],
            deletedPhoneIdentifiers: [],
            deletedUsernameIdentifiers: [],
            organizations: [],
            authorities: [],
            dialogConfirmation: false,
            typeSubmit: '',
            id: '',
            formErrors: {},
        }
    },
    computed: {
        allIdentifiers() {
            return [
                ...this.emailIdentifiers,
                ...this.phoneIdentifiers,
                ...this.usernameIdentifiers
            ];
        },
        isFormValid() {
            return this.user.name &&
                this.user.email &&
                this.user.phone &&
                this.user.organizations.length > 0;
        }
    },
    created() {
        this.id = this.$route.params.id;
    },
    mounted() {
        this.fetchMasterData();
        if (this.id) {
            this.retrieveDetail(this.id);
        }
    },
    methods: {
        async fetchMasterData() {
            try {
                this.loading = true;
                const [orgsRes, authsRes] = await Promise.all([
                    this.axios.get('v1/settings/organizations'),
                    this.axios.get('v1/settings/authorities')
                ]);

                this.organizations = orgsRes.data;
                this.authorities = authsRes.data;
            } catch (error) {
                console.error('Error fetching master data:', error);
                this.$emit('showToast', 'Gagal memuat data master', 'error');
            } finally {
                this.loading = false;
            }
        },

        async retrieveDetail(id) {
            this.loadingFetchUser = true;
            try {
                const response = await this.axios.get(`v1/master/user/${id}`);
                const data = response.data;

                // Map authorities
                const authorities = data.authorities.map(authority => ({
                    authority: authority.id,
                    organizations: authority.organizations.map(org => org.id),
                }));

                // Set user basic data
                this.user = {
                    id: data.id,
                    name: data.name,
                    email: data.email,
                    phone: data.phone,
                    registration_number: data.registration_number,
                    organizations: data.organizations.map(org => org.id),
                    authorities: authorities,
                };

                // Map identifiers if they exist
                if (data.identifiers && data.identifiers.length > 0) {
                    this.mapIdentifiers(data.identifiers, data.email, data.phone);
                }
            } catch (error) {
                console.error('Error fetching user:', error);
                this.$emit('showToast', 'Gagal memuat data pengguna', 'error');
            } finally {
                this.loadingFetchUser = false;
            }
        },

        mapIdentifiers(identifiers, primaryEmail, primaryPhone) {
            // Active identifiers (not deleted)
            this.emailIdentifiers = identifiers
                .filter(i => i.type === 'email' && i.value !== primaryEmail && !i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));

            this.phoneIdentifiers = identifiers
                .filter(i => i.type === 'phone' && i.value !== primaryPhone && !i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));

            this.usernameIdentifiers = identifiers
                .filter(i => i.type === 'username' && !i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, verified_at: i.verified_at }));

            // Deleted identifiers (soft deleted)
            this.deletedEmailIdentifiers = identifiers
                .filter(i => i.type === 'email' && i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, deleted_at: i.deleted_at }));

            this.deletedPhoneIdentifiers = identifiers
                .filter(i => i.type === 'phone' && i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, deleted_at: i.deleted_at }));

            this.deletedUsernameIdentifiers = identifiers
                .filter(i => i.type === 'username' && i.deleted_at)
                .map(i => ({ id: i.id, type: i.type, value: i.value, deleted_at: i.deleted_at }));
        },

        addAuthority() {
            this.user.authorities.push({
                authority: null,
                organizations: this.user?.organizations ?? [],
            });
        },

        removeAuthority(index) {
            this.user.authorities.splice(index, 1);
        },

        handleTypeSubmit(typeSubmit) {
            console.log('handleTypeSubmit called with:', typeSubmit);
            console.log('Form validation status:', this.isFormValid);
            console.log('User data:', this.user);
            this.typeSubmit = typeSubmit;
            this.confirmSubmit();
        },

        confirmSubmit() {
            console.log('confirmSubmit called');
            // Clear previous errors
            this.formErrors = {};

            // Basic validation
            if (!this.isFormValid) {
                console.log('Form validation failed');
                this.$emit('showToast', 'Mohon lengkapi semua field yang wajib diisi', 'error');
                this.validateForm();
                return;
            }

            console.log('Form validation passed, showing dialog');
            this.dialogConfirmation = true;
        },

        validateForm() {
            const errors = {};

            if (!this.user.name) {
                errors.name = ['Nama wajib diisi'];
            }

            if (!this.user.email) {
                errors.email = ['Email wajib diisi'];
            } else if (!this.isValidEmail(this.user.email)) {
                errors.email = ['Format email tidak valid'];
            }

            if (!this.user.phone) {
                errors.phone = ['Nomor telepon wajib diisi'];
            }

            if (!this.user.organizations || this.user.organizations.length === 0) {
                errors.organizations = ['Minimal satu organisasi harus dipilih'];
            }

            // Validate authorities
            this.user.authorities.forEach((authority, index) => {
                if (!authority.authority) {
                    errors[`authorities.${index}.authority`] = ['Otoritas wajib dipilih'];
                }
                if (!authority.organizations || authority.organizations.length === 0) {
                    errors[`authorities.${index}.organizations`] = ['Minimal satu organisasi untuk otoritas ini'];
                }
            });

            this.formErrors = errors;
            return Object.keys(errors).length === 0;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        handleOnClose(event) {
            if (event === true) {
                this.submit();
            } else {
                this.dialogConfirmation = false;
            }
        },

        async restoreIdentifier(identifier) {
            if (!this.id) return;

            try {
                await this.axios.patch(`v1/master/user/${this.id}/identifiers/${identifier.id}/restore`);
                this.$emit('showToast', 'Identifier berhasil dipulihkan', 'success');
                // Refresh user data to get updated state
                this.retrieveDetail(this.id);
            } catch (error) {
                const message = error.response?.data?.message || 'Gagal memulihkan identifier';
                this.$emit('showToast', message, 'error');
            }
        },

        resetForm() {
            this.user = {
                name: '',
                email: '',
                phone: '',
                registration_number: '',
                organizations: [],
                authorities: [],
            };
            this.emailIdentifiers = [];
            this.phoneIdentifiers = [];
            this.usernameIdentifiers = [];
            this.deletedEmailIdentifiers = [];
            this.deletedPhoneIdentifiers = [];
            this.deletedUsernameIdentifiers = [];
            this.formErrors = {};
        },

        async submit() {
            if (!this.validateForm()) {
                this.dialogConfirmation = false;
                return;
            }

            let url = "v1/master/user";
            this.loading = true;

            try {
                // Prepare payload - filter out empty values
                const filteredIdentifiers = this.allIdentifiers.filter(i => i.value && i.value.trim() !== '');

                const payload = {
                    ...this.user,
                };

                // Convert organizations to IDs if they are objects
                if (payload.organizations && Array.isArray(payload.organizations)) {
                    payload.organizations = payload.organizations.map(org => {
                        return typeof org === 'object' ? org.id : org;
                    });
                }

                // Convert authority organizations to IDs if they are objects
                if (payload.authorities && Array.isArray(payload.authorities)) {
                    payload.authorities = payload.authorities.map(authority => ({
                        ...authority,
                        organizations: authority.organizations ? authority.organizations.map(org => {
                            return typeof org === 'object' ? org.id : org;
                        }) : []
                    }));
                }

                // Only add identifiers if there are any OR if updating (to signal deletion)
                if (filteredIdentifiers.length > 0) {
                    payload.identifiers = filteredIdentifiers;
                } else if (this.id) {
                    // For update: empty array signals to delete all identifiers
                    payload.identifiers = [];
                }

                const options = {
                    indices: true,
                    nullsAsUndefineds: false,
                    booleansAsIntegers: false,
                    allowEmptyArrays: false,
                    noFilesWithArrayNotation: false,
                    dotsForObjectNotation: false,
                };

                let formData = serialize(payload, options);

                // Special handling for empty identifiers array on update
                if (this.id && filteredIdentifiers.length === 0) {
                    formData.append('identifiers', JSON.stringify([]));
                }

                if (this.id) {
                    url += '/' + this.id;
                    formData.append('_method', 'PUT');
                }

                const response = await this.axios.post(url, formData);

                this.$emit('showToast', response.data?.message || 'Data berhasil disimpan!', 'success');
                this.dialogConfirmation = false;

                if (this.typeSubmit === 'createNew') {
                    this.resetForm();
                } else {
                    this.$router.go(-1);
                }
            } catch (error) {
                console.error('Submit error:', error);

                // Handle validation errors
                if (error.response?.status === 422) {
                    this.formErrors = error.response.data.errors || {};
                    this.$emit('showToast', 'Terdapat kesalahan validasi. Mohon periksa kembali data Anda.', 'error');
                } else {
                    const message = error.response?.data?.message || error.message || 'Terjadi kesalahan saat menyimpan data';
                    this.$emit('showToast', message, 'error');
                }

                this.dialogConfirmation = false;
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
/* Loading animation improvements */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.space-y-6>*+* {
    margin-top: 1.5rem;
}

/* Form validation states */
.border-red-300 {
    border-color: #fca5a5;
}

/* Smooth transitions */
.transition-colors {
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

/* Focus states improvements */
input:focus,
select:focus,
textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button hover states */
button:hover {
    transform: translateY(-1px);
    transition: transform 0.1s ease-in-out;
}

button:active {
    transform: translateY(0);
}

/* Card animations */
.bg-white,
.dark\\:bg-gray-800 {
    animation: fadeIn 0.3s ease-out;
}
</style>

<template>
    <div>
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-green-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
            <div class="py-12 px-4 mx-auto max-w-screen-xl text-center lg:py-20">
                <div class="mb-6 inline-block">
                    <span class="text-4xl font-bold text-green-700">🍽️</span>
                </div>
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                    PT Edo Mandiri Pratama
                </h1>
                <p class="mb-2 text-xl font-semibold text-green-700 dark:text-green-400">
                    Nutrisi Berkualitas untuk Kehidupan Lebih Sehat
                </p>
                <p class="mb-8 text-lg font-normal text-gray-600 lg:text-xl sm:px-16 lg:px-48 dark:text-gray-400">
                    Menyediakan layanan catering bergizi dan terpercaya untuk kebutuhan dapur makan Anda. 
                    Komitmen kami adalah kesehatan dan kepuasan setiap pelanggan dengan standar kebersihan dan kualitas terbaik.
                </p>
                <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                    <router-link
                        to="/app/auth"
                        class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900"
                    >
                        Pesan Sekarang
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </router-link>
                    <a
                        href="#layanan"
                        class="inline-flex justify-center items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                    >
                        Lihat Layanan
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="layanan" class="bg-gray-50 dark:bg-gray-800">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
                <div class="max-w-screen-md mb-8 lg:mb-16">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                        Layanan Kami
                    </h2>
                    <p class="text-gray-600 sm:text-xl dark:text-gray-400">
                        Solusi catering profesional dengan fokus pada nutrisi, kualitas, dan kepuasan pelanggan.
                    </p>
                </div>
                <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">🥗</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Menu Bergizi Seimbang</h3>
                        <p class="text-gray-600 dark:text-gray-400">Menu harian yang dirancang oleh ahli gizi profesional dengan protein, karbohidrat, dan vitamin seimbang untuk kesehatan optimal.</p>
                    </div>
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">🏢</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Catering Korporat</h3>
                        <p class="text-gray-600 dark:text-gray-400">Layanan catering untuk perusahaan, kantor, dan acara dengan sistem delivery tepat waktu dan penuh tanggung jawab.</p>
                    </div>
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">👨‍🍳</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Chef Berpengalaman</h3>
                        <p class="text-gray-600 dark:text-gray-400">Tim chef profesional dengan pengalaman bertahun-tahun dalam mengolah masakan berkualitas dan bergizi tinggi.</p>
                    </div>
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">🍕</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Beragam Pilihan Menu</h3>
                        <p class="text-gray-600 dark:text-gray-400">Tersedia berbagai pilihan menu tradisional dan modern disesuaikan dengan preferensi dan kebutuhan diet pelanggan.</p>
                    </div>
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">🌡️</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Standar Kebersihan Tinggi</h3>
                        <p class="text-gray-600 dark:text-gray-400">Proses produksi dengan standar HACCP dan SOP ketat untuk menjamin kebersihan dan keamanan pangan terbaik.</p>
                    </div>
                    <div>
                        <div class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-green-100 lg:h-12 lg:w-12 dark:bg-green-900">
                            <span class="text-2xl">📦</span>
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">Kemasan Ramah Lingkungan</h3>
                        <p class="text-gray-600 dark:text-gray-400">Menggunakan kemasan yang aman, higienis, dan dapat didaur ulang untuk menjaga lingkungan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-white dark:bg-gray-900">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
                <div class="mx-auto max-w-screen-sm text-center">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold leading-tight text-gray-900 dark:text-white">
                        Pesan Catering Berkualitas Sekarang
                    </h2>
                    <p class="mb-6 font-light text-gray-600 dark:text-gray-400 md:text-lg">
                        Dapatkan menu bergizi langsung ke lokasi Anda. Hubungi kami untuk penawaran khusus dan konsultasi menu gratis.
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <router-link
                            to="/app/auth"
                            class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                        >
                            Pesan Sekarang
                        </router-link>
                        <a
                            href="tel:+62-xxx-xxxx-xxxx"
                            class="text-green-700 border border-green-700 hover:bg-green-50 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:border-green-600 dark:text-green-400 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-green-800"
                        >
                            📞 Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
// Home page component
</script>

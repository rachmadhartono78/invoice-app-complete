<template>
    <div>
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-green-500 via-green-400 to-emerald-500 dark:from-gray-900 dark:to-gray-800">
            <div class="py-12 px-4 mx-auto max-w-screen-xl text-center lg:py-24">
                <div class="mb-6 inline-block">
                    <span class="text-5xl font-bold">🏭</span>
                </div>
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl dark:text-white">
                    PT EDO MANDIRI PRATAMA
                </h1>
                <p class="mb-2 text-2xl font-bold text-yellow-100 dark:text-yellow-200">
                    🛠️ Penyedia Peralatan Program Makan Bergizi Gratis 🛠️
                </p>
                <p class="mb-6 text-lg font-semibold text-white max-w-4xl mx-auto leading-relaxed">
                    Mitra strategis dalam penyediaan infrastruktur dan peralatan pendukung Program Nasional Makan Bergizi Gratis (MBG). 
                    Kami menghadirkan solusi peralatan dapur industri, distribusi logistik makanan, dan set alat makan standar food-grade.
                </p>
                <div class="mb-8 bg-white/20 backdrop-blur-md rounded-lg p-6 max-w-3xl mx-auto border-2 border-white/30">
                    <p class="text-white text-base leading-relaxed">
                        Mendukung kesuksesan program nasional dengan <span class="font-bold text-yellow-100">Kualitas, Ketahanan, dan Keamanan</span>.
                        Peralatan kami dirancang untuk operasional skala besar, memastikan setiap sajian makanan sampai ke penerima dalam kondisi <span class="font-bold text-yellow-100">hangat, higienis, dan layak konsumsi</span>.
                    </p>
                </div>
                <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 gap-4">
                    <router-link
                        to="/app/auth"
                        class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-center text-green-700 rounded-lg bg-white hover:bg-yellow-50 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900 shadow-lg transform hover:scale-105 transition"
                    >
                        📋 Katalog Produk
                        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </router-link>
                    <a
                        href="#produk"
                        class="inline-flex justify-center items-center py-3 px-6 text-base font-medium text-center text-white rounded-lg border-2 border-white hover:bg-white/20 focus:ring-4 focus:ring-white dark:text-white dark:border-white dark:hover:bg-white/10 dark:focus:ring-white shadow-lg transform hover:scale-105 transition"
                    >
                        🔍 Lihat Solusi Kami
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="produk" class="bg-gradient-to-b from-white to-gray-100 dark:from-gray-900 dark:to-gray-800">
            <div class="py-12 px-4 mx-auto max-w-screen-xl sm:py-20 lg:px-6">
                <div class="max-w-3xl mx-auto mb-12 lg:mb-20 text-center">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                        ⚙️ Solusi Peralatan & Infrastruktur MBG
                    </h2>
                    <p class="text-gray-700 sm:text-xl dark:text-gray-300 leading-relaxed">
                        Kami menyediakan ekosistem peralatan lengkap untuk menunjang operasional Program Makan Bergizi Gratis, mulai dari persiapan (dapur), distribusi, hingga penyajian.
                    </p>
                </div>
                <div class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">🍳</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Peralatan Dapur Industri</h3>
                        <p class="text-gray-700 dark:text-gray-400">Penyediaan kompor high-pressure, oven kapasitas besar, steamer, dan peralatan masak stainless steel heavy-duty untuk dapur umum (Central Kitchen).</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">🚚</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Armada & Box Distribusi</h3>
                        <p class="text-gray-700 dark:text-gray-400">Solusi logistik dengan Insulated Food Box (Thermobox) yang menjaga suhu makanan tetap aman dan higienis selama pengiriman ke sekolah/lokasi.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">🍽️</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Set Alat Makan Siswa</h3>
                        <p class="text-gray-700 dark:text-gray-400">Penyediaan nampan makan (food tray) stainless steel bersekat, peralatan makan (sendok/garpu) food grade yang aman, awet, dan mudah dibersihkan.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">🛡️</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Standar Food Grade & SNI</h3>
                        <p class="text-gray-700 dark:text-gray-400">Seluruh peralatan yang kami sediakan telah memenuhi standar keamanan pangan (Food Grade) dan sertifikasi SNI untuk kesehatan pengguna.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">📦</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Packaging Ramah Lingkungan</h3>
                        <p class="text-gray-700 dark:text-gray-400">Opsi kemasan dan wadah penyajian yang reusable (dapat dipakai ulang) untuk mengurangi sampah, mendukung program keberlanjutan lingkungan.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg hover:shadow-xl transition border-l-4 border-green-500">
                        <div class="flex justify-center items-center mb-4 w-12 h-12 rounded-full bg-green-100 lg:h-14 lg:w-14 dark:bg-green-900">
                            <span class="text-3xl">🤝</span>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Layanan Purna Jual</h3>
                        <p class="text-gray-700 dark:text-gray-400">Kami tidak hanya menjual, tetapi juga menyediakan layanan garansi, maintenance, dan ketersediaan suku cadang untuk peralatan dapur Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-green-600 to-emerald-700 dark:from-green-900 dark:to-emerald-900 py-12 sm:py-20">
            <div class="px-4 mx-auto max-w-screen-xl">
                <div class="mx-auto max-w-3xl text-center">
                    <h2 class="mb-4 text-3xl md:text-4xl tracking-tight font-extrabold leading-tight text-white">
                        ✨ Dukung Program MBG dengan Infrastruktur Terbaik
                    </h2>
                    <p class="mb-8 font-light text-green-50 md:text-lg leading-relaxed">
                        Diskusikan kebutuhan peralatan dapur pusat (Central Kitchen) dan distribusi program Makan Bergizi Gratis di wilayah Anda. Tim kami siap memberikan penawaran terbaik dan spesifikasi teknis yang sesuai.
                    </p>
                    <div class="grid md:grid-cols-2 gap-4 mb-8 max-w-2xl mx-auto">
                        <div class="bg-white/20 backdrop-blur-md rounded-lg p-5 border border-white/30 hover:bg-white/30 transition">
                            <p class="text-white text-sm mb-2 font-semibold">👤 AEN / ICA</p>
                            <a href="tel:087787620888" class="text-white font-bold text-lg hover:text-green-100 transition">
                                📞 087787620888
                            </a>
                        </div>
                        <div class="bg-white/20 backdrop-blur-md rounded-lg p-5 border border-white/30 hover:bg-white/30 transition">
                            <p class="text-white text-sm mb-2 font-semibold">👤 BAGUS</p>
                            <a href="tel:087878381090" class="text-white font-bold text-lg hover:text-green-100 transition">
                                📞 087878381090
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-center gap-4 flex-wrap">
                        <router-link
                            to="/app/auth"
                            class="text-green-700 bg-white hover:bg-green-50 focus:ring-4 focus:ring-green-300 font-bold rounded-lg text-sm md:text-base px-6 md:px-8 py-3 md:py-4 dark:bg-green-50 dark:text-green-800 dark:hover:bg-green-100 focus:outline-none shadow-lg transform hover:scale-105 transition"
                        >
                            📋 Ajukan Penawaran
                        </router-link>
                        <a
                            href="tel:087878381090"
                            class="text-white bg-green-800 hover:bg-green-900 focus:ring-4 focus:ring-green-200 font-bold rounded-lg text-sm md:text-base px-6 md:px-8 py-3 md:py-4 dark:bg-green-700 dark:hover:bg-green-800 focus:outline-none shadow-lg transform hover:scale-105 transition"
                        >
                            ☎️ Hubungi Sekarang
                        </a>
                    </div>
                </div>
                <div class="mt-12 pt-8 border-t border-white/30 text-center text-green-50 text-sm">
                    <p class="mb-2 font-semibold">📍 Lokasi: Jl. Garuda Raya No.C114, Jurang Manggu Tim., Kec. Pd. Aren, Kota Tangerang Selatan, Banten 15222</p>
                    <p>⏰ Buka 24 Jam | 🌍 Tangerang Selatan, Banten</p>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
// Home page component
</script>

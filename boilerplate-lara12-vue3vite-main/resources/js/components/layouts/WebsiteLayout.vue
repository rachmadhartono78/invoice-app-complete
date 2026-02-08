<template>
    <div class="min-h-screen flex flex-col bg-white font-sans text-slate-600 antialiased selection:bg-emerald-100 selection:text-emerald-900">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 w-full bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <router-link to="/" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-teal-800 rounded-lg flex items-center justify-center text-white shadow-lg group-hover:shadow-emerald-500/30 transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-bold text-slate-900 leading-tight tracking-tight">PT EDO MANDIRI PRATAMA</span>
                                <span class="text-[10px] font-medium text-slate-500 tracking-wider uppercase">Procurement & Supply Chain</span>
                            </div>
                        </router-link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <router-link to="/" class="text-sm font-medium text-slate-600 hover:text-emerald-700 transition-colors" active-class="text-emerald-700 font-bold">Home</router-link>
                        <a href="#about" class="text-sm font-medium text-slate-600 hover:text-emerald-700 transition-colors">Tentang Kami</a>
                        <a href="#services" class="text-sm font-medium text-slate-600 hover:text-emerald-700 transition-colors">Layanan</a>
                        <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-emerald-700 transition-colors">Kontak</a>
                    </div>

                    <!-- CTA Button -->
                    <div class="hidden md:flex items-center gap-4">
                        <router-link to="/app/auth" class="px-5 py-2.5 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all duration-200 border border-emerald-200">
                            Login Portal
                        </router-link>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-slate-700 focus:outline-none p-2 rounded-md">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden bg-white border-b border-slate-100">
                <div class="px-4 pt-2 pb-6 space-y-2">
                    <router-link to="/" class="block px-3 py-2 rounded-md text-base font-medium text-slate-900 hover:bg-slate-50 hover:text-emerald-700" @click="mobileMenuOpen = false">Home</router-link>
                    <a href="#about" class="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-emerald-700" @click="mobileMenuOpen = false">Tentang Kami</a>
                    <a href="#services" class="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-emerald-700" @click="mobileMenuOpen = false">Layanan</a>
                    <a href="#contact" class="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-emerald-700" @click="mobileMenuOpen = false">Kontak</a>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <router-link to="/app/auth" class="block w-full text-center px-4 py-3 rounded-lg font-medium bg-emerald-700 text-white hover:bg-emerald-800 transition-colors" @click="mobileMenuOpen = false">
                            Login Portal
                        </router-link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow">
            <router-view />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-50 border-t border-slate-200 pt-16 pb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-8 h-8 bg-emerald-700 rounded flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-xl font-bold text-slate-900">PT EDO MANDIRI PRATAMA</span>
                        </div>
                        <p class="text-slate-500 mb-6 max-w-sm leading-relaxed">
                            Mitra strategis dalam pengadaan peralatan pendukung program makan bergizi dan solusi operasional perusahaan yang efisien, transparan, dan akuntabel.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Navigasi</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-slate-500 hover:text-emerald-700 transition-colors">Beranda</a></li>
                            <li><a href="#about" class="text-slate-500 hover:text-emerald-700 transition-colors">Tentang Kami</a></li>
                            <li><a href="#services" class="text-slate-500 hover:text-emerald-700 transition-colors">Layanan</a></li>
                            <li><a href="#contact" class="text-slate-500 hover:text-emerald-700 transition-colors">Hubungi Kami</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-slate-500 hover:text-emerald-700 transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="text-slate-500 hover:text-emerald-700 transition-colors">Terms of Service</a></li>
                            <li><a href="#" class="text-slate-500 hover:text-emerald-700 transition-colors">Sitemap</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-slate-400 text-sm">
                        &copy; {{ new Date().getFullYear() }} PT EDO MANDIRI PRATAMA. All Rights Reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>

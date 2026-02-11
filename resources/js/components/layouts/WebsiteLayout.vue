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

        <!-- Floating WhatsApp Button -->
        <a href="https://wa.me/6287787620888" target="_blank" rel="noopener noreferrer" class="fixed bottom-6 right-6 z-50 group">
            <div class="relative flex items-center justify-center p-4 bg-[#25D366] rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <span class="absolute right-full mr-3 bg-white px-3 py-1 rounded-lg shadow-md text-xs font-bold text-slate-700 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                    Chat WhatsApp
                </span>
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
        </a>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>

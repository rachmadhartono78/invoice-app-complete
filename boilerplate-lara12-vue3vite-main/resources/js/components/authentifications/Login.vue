<template>
    <div :style="isScrollable ? 'overflow-y: auto; max-height: 100vh;' : ''">
        <section class="bg-white dark:bg-gray-900 min-h-screen flex flex-col">
            <div class="flex items-stretch justify-center mx-auto w-full h-screen">

                <!-- Chat Bubble Features Section -->
                <!-- <div
                    class="hidden md:flex flex-col justify-center items-center w-1/2 h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full"></div>
                        <div class="absolute top-40 right-20 w-16 h-16 bg-purple-200 rounded-full"></div>
                        <div class="absolute bottom-32 left-20 w-12 h-12 bg-pink-200 rounded-full"></div>
                        <div class="absolute bottom-20 right-10 w-24 h-24 bg-green-200 rounded-full"></div>
                    </div>

                    <div class="w-full max-w-lg mx-auto p-6 relative z-10" @mouseenter="pauseAutoSlide"
                        @mouseleave="resumeAutoSlide">
                        <div
                            class="h-150 overflow-y-auto -space-y-2 bg-transparent dark:bg-gray-800/50 rounded-lg p-4 backdrop-blur-sm relative">
                            <transition name="slide-left" appear>
                                <div v-show="chatStep >= 1" class="flex items-start space-x-2 mb-6">
                                    <div
                                        class="bg-gray-200 dark:bg-gray-600 rounded-lg rounded-tl-none px-4 py-3 shadow-sm max-w-xs">
                                        <p class="text-sm text-gray-800 dark:text-white font-medium">{{ currentQuestion
                                        }}</p>
                                    </div>
                                </div>
                            </transition>

                            <transition name="slide-right" appear>
                                <div v-show="chatStep >= 3"
                                    class="flex items-start space-x-2 justify-end z-20 relative">
                                    <div class="bg-primary-600 rounded-lg rounded-tr-none px-4 py-3 shadow-sm max-w-xs">
                                        <p class="text-sm text-white">{{ currentAnswer }}</p>
                                    </div>
                                </div>
                            </transition>

                            <transition name="slide-right" appear>
                                <div v-if="currentFeature.image" v-show="chatStep >= 3"
                                    class="flex justify-end mr-10 -space-y-3 z-10">
                                    <div class="rounded-lg overflow-hidden shadow-lg bg-white min-w-[350px]">
                                        <img :src="currentFeature.image" :alt="currentFeature.title"
                                            class="w-full h-auto object-contains">
                                    </div>
                                </div>
                            </transition>

                            <transition name="slide-right" appear>
                                <div v-show="chatStep === 2" class="flex items-start space-x-2 justify-end">
                                    <div class="bg-primary-600 rounded-lg rounded-tr-none px-4 py-3 shadow-sm">
                                        <div class="flex space-x-1 items-center">
                                            <div class="w-2 h-2 bg-white rounded-full animate-typing"></div>
                                            <div class="w-2 h-2 bg-white rounded-full animate-typing"
                                                style="animation-delay: 0.2s;"></div>
                                            <div class="w-2 h-2 bg-white rounded-full animate-typing"
                                                style="animation-delay: 0.4s;"></div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>

                    </div>
                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-30">
                        <div class="flex items-center justify-center space-x-2 rounded-full px-4 py-2">
                            <button v-for="(slide, index) in carouselSlides" :key="index" @click="goToSlide(index)"
                                class="transition-all duration-300 focus:outline-none" :class="[
                                    'rounded-full',
                                    carouselIndex === index
                                        ? 'w-2 h-2 bg-gray-300'
                                        : 'w-2 h-2 bg-gray-200'
                                ]" :aria-label="`Go to slide ${index + 1}`"></button>
                        </div>
                    </div>
                </div> -->

                
                <!-- Login Section -->
                <div class="flex flex-col items-center justify-center w-full md:w-1/2 h-screen relative p-6">
                    <div
                        class="w-full rounded-3xl sm:max-w-md xl:p-0 bg-white/70 backdrop-blur-xl dark:bg-gray-800/70 shadow-2xl border border-white/20 dark:border-gray-700/30">
                        <div class="p-8 space-y-6">
                            <!-- LOGIN TEMPLATE ======================================= -->
                                <div class="text-center mb-8">
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                        Welcome Back
                                    </h1>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Sign in to continue to your account
                                    </p>
                                </div>

                                <div class="flex flex-col items-center space-y-3">
                                    <!-- Hidden Google Button -->
                                    <div ref="googleBtnContainer" style="display: none;"></div>

                                    <button type="button"
                                        class="w-full flex items-center justify-center py-3 px-4 rounded-xl bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 hover:bg-white/80 dark:hover:bg-gray-700/80 cursor-pointer transition-all duration-200 hover:shadow-lg"
                                        @click="triggerGoogleLogin">
                                        <svg class="w-4 h-4 mr-3" viewBox="0 0 24 24" fill="currentColor"
                                            aria-hidden="true">
                                            <path fill="#4285F4"
                                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                            <path fill="#34A853"
                                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                            <path fill="#FBBC05"
                                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                            <path fill="#EA4335"
                                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                        </svg>

                                        <span class="font-medium text-base text-gray-700 dark:text-gray-300">Continue with Google</span>
                                    </button>

                                    <button type="button"
                                        class="w-full flex items-center justify-center py-3 px-4 rounded-xl bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 hover:bg-white/80 dark:hover:bg-gray-700/80 cursor-pointer transition-all duration-200 hover:shadow-lg"
                                        @click="triggerYahooLogin">
                                        <svg class="w-4 h-4 mr-3" viewBox="0 0 512 512" fill="currentColor"
                                            aria-hidden="true">
                                            <path fill="#7F40BD"
                                                d="M504.783 77.091H303.494a7 7 0 0 0-7.187 7.915l3.925 40.693a7 7 0 0 0 6.792 6.516l42.05 2.314-80.736 112.023-87.045-175.014 63.563-2.43a7 7 0 0 0 6.945-7.121l.464-36.392a7 7 0 0 0-7.219-7.313H7.22A7.22 7.22 0 0 0 0 32.799v40.558a7.22 7.22 0 0 0 7.115 7.22l66.917.964 127.257 224.488-.568 140.205-88.48 3.631a7 7 0 0 0-6.923 7.077l-.716 37.506a7 7 0 0 0 7.22 7.357h276.762a7 7 0 0 0 7.213-7.595l-1.933-37.132a7 7 0 0 0-6.835-6.834l-76.124-3.992-11.082-136.57 120.924-157.553 55.64-2.53a7 7 0 0 0 6.587-5.139l12.909-43.018a7 7 0 0 0-6.832-9.019z" />
                                        </svg>
                                        <span class="font-medium text-base text-gray-700 dark:text-gray-300">Continue with Yahoo</span>
                                    </button>

                                </div>

                                <div class="relative flex items-center my-6">
                                    <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                                    <span class="px-4 text-sm text-gray-500 dark:text-gray-400 bg-transparent">or</span>
                                    <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                                </div>

                                <div>
                                    <form ref="form" @submit.prevent="login" class="space-y-4">
                                        <div>
                                            <label for="email"
                                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                            <input type="email" name="email" id="email" v-model="email"
                                                :rules="emailRules"
                                                class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                                placeholder="you@example.com" required="">
                                        </div>
                                        <div>
                                            <label for="password"
                                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                            <input type="password" name="password" id="password" placeholder="••••••••"
                                                v-model="password" :rules="passwordRules"
                                                class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                                required="">
                                        </div>
                                        <btn-primary type="submit" rounded="rounded-xl" className="w-full py-3 mt-6 shadow-lg hover:shadow-xl transition-all duration-200"
                                            :loading="loading" :disabled="loading">Sign In</btn-primary>
                                    </form>
                                </div>

                                <div class="text-center mt-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Don't have an account?
                                        <router-link to="/app/register"
                                            class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                            Sign up
                                        </router-link>
                                    </p>
                                </div>

                                <div class="text-center mt-4">
                                    <router-link to="/" 
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                        </svg>
                                        Back to Home
                                    </router-link>
                                </div>
                            <!-- END LOGIN TEMPLATE **************************** -->

                        </div>
                    </div>

                    <div class="absolute bottom-6 left-0 w-full flex justify-center" v-if="false">
                        <button @click="scrollToAbout"
                            class="px-2 py-1 rounded-xl text-xs bg-white/30 backdrop-blur-md text-gray-200 font-semibold hover:bg-white/50 transition-colors duration-150 border border-gray-200">
                            Lebih lanjut..
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Tentang Aplikasi di bawah -->
        <section class="w-full py-12 px-4 bg-gradient-to-b from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold mb-4 text-green-700 dark:text-green-400">🍽️ Tentang PT EDO MANDIRI PRATAMA</h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6 text-lg leading-relaxed">PT EDO MANDIRI PRATAMA adalah perusahaan procurement terdepan yang berkomitmen menyediakan <strong>kebutuhan makan bergizi gratis</strong> dan <strong>peralatan makan profesional</strong> berkualitas tinggi. Kami menerapkan prinsip <strong>Efisiensi, Transparansi, dan Akuntabilitas</strong> dalam setiap proses pengadaan untuk mendukung kesehatan dan operasional perusahaan Anda secara optimal.</p>
                <div class="flex flex-wrap justify-center gap-6 mt-8">
                    <div class="flex flex-col items-center w-48 p-4 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold text-green-700 dark:text-green-400">Efisiensi Maksimal</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Proses pengadaan yang optimal dengan biaya efektif</p>
                    </div>
                    <div class="flex flex-col items-center w-48 p-4 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-bold text-green-700 dark:text-green-400">Transparansi Penuh</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Setiap transaksi terdokumentasi dan dapat dipertanggungjawabkan</p>
                    </div>
                    <div class="flex flex-col items-center w-48 p-4 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400 mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="font-bold text-green-700 dark:text-green-400">Delivery On-Time</span>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Pengiriman tepat waktu dengan penanganan profesional</p>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        📞 Hubungi kami: <span class="font-semibold text-green-700 dark:text-green-400">087878381090 (BAGUS) | 087787620888 (AEN/ICA)</span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Buka 24 Jam | Tangerang Selatan, Banten</p>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import { useAuthStore } from '@/stores/auth';
import { getDeviceName } from '@/utils/deviceHelper';
import { set } from 'date-fns';

export default {
    data: () => ({
        isScrollable: false,
        loading: false,
        app_name: import.meta.env.VITE_APP_NAME,
        app_url: import.meta.env.VITE_APP_URL,
        app_env: import.meta.env.VITE_APP_ENV,
        email: '',
        password: '',
        emailRules: [
            v => !!v || 'E-mail is required',
            v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        ],
        device: window.navigator.userAgent,
        device_name: getDeviceName(),
        passwordRules: [v => !!v || 'Password is required'],
        // Chat Features Carousel
        carouselSlides: [
            {
                icon: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                title: 'Login Mudah',
                question: 'Apakah proses login ribet dan memakan waktu?',
                answer: 'Tidak sama sekali! Login super mudah dengan Google, Yahoo, akun UII, atau WhatsApp OTP! 🚀',
                color: 'bg-blue-600',
                image: null,
                imageTitle: 'One-Click Login',
                imageDesc: 'Akses instant tanpa ribet password kompleks'
            },
            {
                icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                title: 'Notifikasi WhatsApp',
                question: 'Bagaimana jika saya melewatkan sesuatu?',
                answer: 'Tenang! Sistem otomatis kirim reminder via WhatsApp untuk semua deadline penting. No more missed deadlines! 📱',
                color: 'bg-green-600',
                image: '/koas//images/bubble.png',
                imageTitle: 'Smart WhatsApp Notifications',
                imageDesc: 'Reminder otomatis langsung ke HP Anda'
            },
            {
                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                title: 'Pantau Rotasi Realtime',
                question: 'Bisakah memantau progres rotasi dan kompetensi secara realtime?',
                answer: 'Absolutely! Dashboard realtime menampilkan progres asesmen, jadwal rotasi, dan pencapaian target. Semua termonitor! 📊',
                color: 'bg-purple-600',
                image: '/koas/images/card.png',
                imageTitle: 'Real-time Rotation Tracking',
                imageDesc: 'Monitor progres kompetensi dan jadwal rotasi live'
            },
            {
                icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                title: 'Assessment Mudah',
                question: 'Apakah pengisian assessment dan evaluasi rumit?',
                answer: 'Super simpel! Form assessment digital yang user-friendly',
                color: 'bg-orange-600',
                image: '/koas/images/assessment.png',
                imageTitle: 'Smart Assessment Forms',
                imageDesc: 'Form evaluasi digital yang mudah dan efisien'
            }
        ],
        carouselIndex: 0,
        chatStep: 0, // 0: nothing, 1: question appears, 2: typing, 3: answer appears
        isHovered: false, // Track hover state for pausing auto-slide
        carouselTimer: null, // Auto-slide timer
        chatTimer1: null, // Question timer
        chatTimer2: null, // Typing timer  
        chatTimer3: null, // Answer timer
    }),
    computed: {
        currentFeature() {
            return this.carouselSlides[this.carouselIndex];
        },
        currentQuestion() {
            return this.currentFeature?.question || '';
        },
        currentAnswer() {
            return this.currentFeature?.answer || '';
        }
    },
    created() {
        useAuthStore().clearUser();
        this.email = this.app_env === 'production' ? '' : 'superadmin@example.com';
        this.password = this.app_env === 'production' ? '' : 'password';
    },
    mounted() {
        const gClientId = import.meta.env.VITE_GOOGLE_CLIENT_ID;
        if (!window.google || !window.google.accounts || !window.google.accounts.id) {
            const checkGoogle = setInterval(() => {
                if (window.google && window.google.accounts && window.google.accounts.id) {
                    clearInterval(checkGoogle);
                    this.initGoogleSignIn(gClientId);
                }
            }, 100);
        } else {
            this.initGoogleSignIn(gClientId);
        }

        // Start chat animation
        this.startChatAnimation();

        // Auto-advance carousel with better timing
        this.startAutoSlide();
    },
    beforeUnmount() {
        this.clearAutoSlide();
        // Clear chat animation timers
        if (this.chatTimer1) clearTimeout(this.chatTimer1);
        if (this.chatTimer2) clearTimeout(this.chatTimer2);
        if (this.chatTimer3) clearTimeout(this.chatTimer3);
    },
    methods: {
        initGoogleSignIn(gClientId) {
            if (window.google && window.google.accounts && window.google.accounts.id) {
                window.google.accounts.id.initialize({
                    client_id: gClientId,
                    callback: this.googleCallback,
                    auto_select: false,
                });

                // Render tombol Google di container tersembunyi
                window.google.accounts.id.renderButton(
                    this.$refs.googleBtnContainer,
                    {
                        type: 'standard',
                        theme: 'outline',
                        size: 'large',
                        text: 'continue_with',
                        shape: 'rectangular',
                        logo_alignment: 'left',
                        width: 300,
                    }
                );

                window.google.accounts.id.prompt(notification => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        console.log('Google Sign-In prompt was not displayed or skipped');
                    }
                });
            } else {
                console.warn('Google Sign-In SDK belum tersedia.');
            }
        },
        triggerGoogleLogin() {
            // Find the Google button inside the hidden container and trigger click
            const btn = this.$refs.googleBtnContainer.querySelector('div[role="button"]');
            if (btn) btn.click();
        },
        triggerYahooLogin() {
            if (this.app_env === 'production') {
                window.location.href = 'https://sila.fk.uii.ac.id/koas/yahoo/redirect';
            } else {
                window.location.href = 'http://localhost:8000/koas/yahoo/redirect';
            }

        },
        scrollToAbout() {
            this.isScrollable = true;
            setTimeout(() => {
                const aboutSection = document.querySelector('section.w-full.py-8');
                if (aboutSection) {
                    aboutSection.scrollIntoView({ behavior: 'smooth' });

                }
            }, 500);
        },
        startAutoSlide() {
            this.clearAutoSlide();
            this.carouselTimer = setInterval(() => {
                if (!this.isHovered) {
                    this.nextFeature();
                }
            }, 7000); // 7 seconds per feature for better pacing
        },
        clearAutoSlide() {
            if (this.carouselTimer) {
                clearInterval(this.carouselTimer);
                this.carouselTimer = null;
            }
        },
        pauseAutoSlide() {
            this.isHovered = true;
        },
        resumeAutoSlide() {
            this.isHovered = false;
        },
        startChatAnimation() {
            this.chatStep = 0;
            // Clear any existing animation timers
            if (this.chatTimer1) clearTimeout(this.chatTimer1);
            if (this.chatTimer2) clearTimeout(this.chatTimer2);
            if (this.chatTimer3) clearTimeout(this.chatTimer3);

            this.chatTimer1 = setTimeout(() => {
                this.chatStep = 1; // Show question
            }, 300);
            this.chatTimer2 = setTimeout(() => {
                this.chatStep = 2; // Show typing
            }, 1500);
            this.chatTimer3 = setTimeout(() => {
                this.chatStep = 3; // Show answer
            }, 3000);
        },
        nextFeature() {
            this.carouselIndex = (this.carouselIndex + 1) % this.carouselSlides.length;
            this.startChatAnimation();
        },
        goToSlide(index) {
            this.carouselIndex = index;
            this.startChatAnimation();
            // Restart auto-slide timer after manual navigation
            this.startAutoSlide();
        },
        getTimeStamp() {
            const now = new Date();
            return now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        nextSlide() {
            this.nextFeature();
        },
        previousSlide() {
            this.carouselIndex = this.carouselIndex === 0 ? this.carouselSlides.length - 1 : this.carouselIndex - 1;
            this.startChatAnimation();
        },
        async googleCallback(googleRes) {
            this.loading = true;
            const body = {
                credential: googleRes.credential,
                device: this.device || window.navigator.userAgent,
                device_name: this.device_name,

            };
            this.axios.get(`${this.app_url}/sanctum/csrf-cookie`).then(() => {
                this.axios
                    .post('google/callback', body)
                    .then(response => this.afterLogin(response.data))
                    .catch(err => console.error(err))
                    .finally(() => (this.loading = false));
            });
        },
        login() {
            this.loading = true;
            const body = {
                email: this.email,
                password: this.password,
                device: this.device,
                device_name: this.device_name,
            };
            this.axios.get(`${this.app_url}/sanctum/csrf-cookie`, {
                credentials: 'include', withCredentials: true
            }).then(res => {
                console.log('csrf cookie set');
                this.axios
                    .post('auth/login', body)
                    .then((response) => {
                        this.afterLogin(response?.data);
                    })
                    .catch((err) => { console.log(err); })
                    .finally(() => (this.loading = false));
            })
                .catch(error => console.error('CSRF Error:', error));;
        },
        afterLogin(data) {
            // Handle authorities if not loaded yet (for faster login)
            const authorityIds = data.authorities?.map((authority) => authority.pivot.authority_id) || [];
            data['authority_ids'] = authorityIds;
            useAuthStore().setUser(data);
            
            // Load full app data (authorities, menus, etc.) in background
            if (!data.authorities || data.authorities.length === 0) {
                this.axios.get('auth/load-app-data')
                    .then((response) => {
                        if (response?.data?.result) {
                            const fullData = response.data.data;
                            const fullAuthorityIds = fullData.authorities?.map((authority) => authority.pivot.authority_id) || [];
                            fullData['authority_ids'] = fullAuthorityIds;
                            useAuthStore().setUser(fullData);
                        }
                    })
                    .catch((err) => console.log('Failed to load app data:', err));
            }
            
            this.$emit("showToast", { message: `Welcome back ${data.name}!`, type: "success", timeout: 3000 });
            // Check if there's a redirect destination from query parameters
            const redirectTo = this.$route.query.destination;
            if (redirectTo) {
                const url = (this.app_url + redirectTo).replace(/\/koas\/koas/g, '/koas')
                window.location.href = url;
                console.log(`Redirecting to: ${this.app_url}${redirectTo}`);

                //  this.$router.push(redirectTo);
            } else {
                this.$router.push({ name: 'home' });
            }
        },
    },
}
</script>

<style scoped>
/* Chat bubble slide animations */
.slide-left-enter-active {
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-left-enter-from {
    opacity: 0;
    transform: translateX(-50px) scale(0.8);
}

.slide-left-enter-to {
    opacity: 1;
    transform: translateX(0) scale(1);
}

.slide-right-enter-active {
    transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.slide-right-enter-from {
    opacity: 0;
    transform: translateX(50px) scale(0.8);
}

.slide-right-enter-to {
    opacity: 1;
    transform: translateX(0) scale(1);
}

/* Scale-up transition for icons */
.scale-up-enter-active,
.scale-up-leave-active {
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.scale-up-enter-from {
    opacity: 0;
    transform: scale(0.5) rotate(-180deg);
}

.scale-up-leave-to {
    opacity: 0;
    transform: scale(0.5) rotate(180deg);
}

/* Fade-up transition for content */
.fade-up-enter-active,
.fade-up-leave-active {
    transition: all 0.6s ease-out;
}

.fade-up-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

.fade-up-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}

/* Fade transition for typing indicator */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Enhanced bounce animation for typing dots */
@keyframes bounce-soft {

    0%,
    80%,
    100% {
        transform: translateY(0);
    }

    40% {
        transform: translateY(-8px);
    }
}

.animate-bounce {
    animation: bounce-soft 1s ease-in-out infinite;
}

/* Improved typing indicator animation */
@keyframes typing {

    0%,
    60%,
    100% {
        transform: translateY(0);
        opacity: 0.4;
    }

    30% {
        transform: translateY(-6px);
        opacity: 1;
    }
}

.animate-typing {
    animation: typing 1.4s ease-in-out infinite;
}

/* Fade scale transition for typing indicator */
.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.fade-scale-enter-from {
    opacity: 0;
    transform: translateX(-50%) scale(0.7) translateY(10px);
}

.fade-scale-leave-to {
    opacity: 0;
    transform: translateX(-50%) scale(0.7) translateY(-10px);
}

/* Floating progress dots styling */
.bottom-2\.5 {
    bottom: 10px;
}

/* Enhanced hover effects for floating dots */
.bg-white\/80:hover {
    background-color: rgba(255, 255, 255, 0.95);
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.dark .bg-gray-800\/80:hover {
    background-color: rgba(31, 41, 55, 0.95);
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

/* Active dot glow effect */
.bg-primary-600.scale-125 {
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

/* Chat bubble hover effects */
.bg-gray-200:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.bg-primary-600:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    transition: all 0.2s ease;
}

/* Smooth transitions for button interactions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Hover effects for navigation buttons */
button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Chat container scroll styling */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(59, 130, 246, 0.5);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.7);
}

/* Backdrop blur effect */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Animated gradient background */
@keyframes gradient-shift {

    0%,
    100% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }
}

.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradient-shift 10s ease infinite;
}

/* Image hover effects */
img:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

/* Preview card animation */
@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.border-blue-200 {
    animation: slideUp 0.5s ease-out;
}

/* Image loading effect */
img {
    transition: opacity 0.3s ease;
}

img[src*="unsplash"] {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}
</style>
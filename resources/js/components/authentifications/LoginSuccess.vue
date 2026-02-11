<template>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-100 to-blue-300">
        <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg border border-blue-200">
            <div class="flex flex-col items-center space-y-4">
                <svg v-if="token" class="w-16 h-16 text-green-500 mb-2" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4" />
                </svg>
                <h2 v-if="token" class="text-2xl font-bold text-green-600">Login Berhasil!</h2>
                <p v-if="token" class="text-gray-700 text-center">
                    Anda berhasil masuk. Anda akan diarahkan ke beranda.
                </p>
                <div v-if="token" class="flex flex-col items-center mt-2 space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Mengalihkan ke beranda dalam</span>
                        <span class="font-semibold text-blue-600 text-lg">{{ countdown }}</span>
                        <span class="text-sm text-gray-500">detik...</span>
                    </div>
                    <small class="text-xs text-gray-500">
                        Tidak diarahkan secara otomatis? 
                        <span @click="$router.push({name:'home'})" class="text-blue-600 cursor-pointer hover:underline font-medium">
                            Klik di sini untuk ke Beranda
                        </span>
                    </small>
                </div>
                <div v-else class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-red-400 mb-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6m0-6l6 6" />
                    </svg>
                    <p class="text-red-500 font-semibold">Token tidak ditemukan di URL.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth';


export default {
    name: 'LoginSuccess',
    data() {
        return {
            token: null,
            countdown: 3,
            countdownInterval: null,
        };
    },
    created() {
        const token = this.$route.query.token;
        if (token) {
            this.token = token;
            this.fetchUser(token);
        }
    },
    beforeDestroy() {
        if (this.countdownInterval) clearInterval(this.countdownInterval);
    },
    methods: {
        async fetchUser(token) {
            try {
            // Manual fetch for sanctum/csrf-cookie
            await fetch('/sanctum/csrf-cookie', {
                credentials: 'include'
            });
            
            // Axios for /me endpoint
            const res = await this.axios.get('/me', {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            const data = res.user;
            console.log(data);
            const authorityIds = data.authorities?.map((a) => a.pivot.authority_id) || [];
            data['authority_ids'] = authorityIds;
            useAuthStore().setUser(data);
            this.startCountdown();
            } catch (err) {
            console.error('Gagal ambil data user:', err);
            this.$router.push({ name: 'auth' });
            }
        },
        startCountdown() {
            this.countdownInterval = setInterval(() => {
                if (this.countdown > 1) {
                    this.countdown--;
                } else {
                    clearInterval(this.countdownInterval);
                    this.$router.push({ name: 'home' });
                }
            }, 1000);
        }
    }
};
</script>

<style scoped>
/* No extra styles needed, Tailwind + Flowbite covers it */
</style>
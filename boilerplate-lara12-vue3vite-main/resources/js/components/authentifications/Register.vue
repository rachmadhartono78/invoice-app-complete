<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center">
        <section class="w-full">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
                <div
                    class="w-full rounded-3xl sm:max-w-md xl:p-0 bg-white/70 backdrop-blur-xl dark:bg-gray-800/70 shadow-2xl border border-white/20 dark:border-gray-700/30">
                    <div class="p-8 space-y-6">
                        <div class="text-center">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                Create Account
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Sign up to get started
                            </p>
                        </div>

                        <form @submit.prevent="register" class="space-y-4">
                            <div>
                                <label for="name"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                                <input type="text" name="name" id="name" v-model="form.name"
                                    class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                    placeholder="Enter your full name" required>
                                <p v-if="errors.name" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                                    errors.name[0] }}</p>
                            </div>

                            <div>
                                <label for="email"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                                <input type="email" name="email" id="email" v-model="form.email"
                                    class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                    placeholder="you@example.com" required>
                                <p v-if="errors.email" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                                    errors.email[0] }}</p>
                            </div>

                            <div>
                                <label for="password"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                <input type="password" name="password" id="password" placeholder="••••••••"
                                    v-model="form.password"
                                    class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                    required>
                                <p v-if="errors.password" class="mt-1 text-xs text-red-600 dark:text-red-400">{{
                                    errors.password[0] }}</p>
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    placeholder="••••••••" v-model="form.password_confirmation"
                                    class="bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent block w-full p-3 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200"
                                    required>
                            </div>

                            <btn-primary type="submit" rounded="rounded-xl" className="w-full py-3 mt-6 shadow-lg hover:shadow-xl transition-all duration-200" :loading="loading"
                                :disabled="loading">
                                Sign Up
                            </btn-primary>

                            <div class="text-center mt-6">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Already have an account?
                                    <router-link to="/app/auth"
                                        class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                        Sign in
                                    </router-link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            errors: {},
            loading: false,
            app_url: import.meta.env.VITE_APP_URL,
        }
    },
    methods: {
        async register() {
            this.loading = true;
            this.errors = {};

            try {
                await this.axios.get(`${this.app_url}/sanctum/csrf-cookie`);

                const response = await this.axios.post('auth/register', this.form);

                this.$emit("showToast", {
                    message: response.message || 'Registration successful! Please check your email for verification.',
                    type: "success",
                    timeout: 5000
                });

                // Redirect to verify email page with email parameter
                this.$router.push({
                    name: 'verify-email',
                    query: { email: this.form.email }
                });

            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                }

                this.$emit("showToast", {
                    message: error.response?.message || 'An error occurred during registration',
                    type: "error",
                    timeout: 3000
                });
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
.backdrop-blur-xl {
    backdrop-filter: blur(16px);
}
</style>

<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center">
        <section class="w-full">
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto lg:py-0">
                <div
                    class="w-full rounded-3xl sm:max-w-md xl:p-0 bg-white/70 backdrop-blur-xl dark:bg-gray-800/70 shadow-2xl border border-white/20 dark:border-gray-700/30">
                    <div class="p-8 space-y-6">
                        <div v-if="!verified">
                            <div class="text-center mb-6">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                    Verify Email
                                </h1>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Enter the 6-digit code sent to
                                </p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ email }}
                                </p>
                            </div>

                            <form class="max-w-sm mx-auto mt-6" @submit.prevent="verifyOtp">
                                <div class="flex gap-2 mb-6 justify-center">
                                    <input v-for="(code, index) in codes" :key="index" type="text" maxlength="1"
                                        class="w-12 h-12 text-center text-lg font-bold text-gray-900 dark:text-white bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
                                        v-model="codes[index]" @input="onInput(index)"
                                        @keydown.backspace="onBackspace(index, $event)" @paste="onPaste($event, index)"
                                        ref="otpInputs" autocomplete="one-time-code" inputmode="numeric"
                                        pattern="[0-9]*" required />
                                </div>

                                <div class="mt-4 flex justify-center">
                                    <button type="button" :disabled="countdown > 0" @click="resendOtp"
                                        class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 disabled:text-gray-400 disabled:cursor-not-allowed transition-colors">
                                        <span v-if="countdown === 0">Resend Code</span>
                                        <span v-else>Resend in {{ countdown }}s</span>
                                    </button>
                                </div>

                                <btn-primary type="submit" rounded="rounded-xl" className="w-full mt-6 py-3 shadow-lg hover:shadow-xl transition-all duration-200"
                                    :disabled="codes.join('').length !== 6 || loading" :loading="loading">
                                    {{ loading ? 'Verifying...' : 'Verify Email' }}
                                </btn-primary>
                            </form>
                        </div>

                        <div v-else class="text-center py-4">
                            <div class="mb-6">
                                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                Email Verified!
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">
                                Your email has been successfully verified. You can now sign in to your account.
                            </p>
                            <router-link to="/app/auth">
                                <btn-primary rounded="rounded-xl" className="w-full py-3 shadow-lg hover:shadow-xl transition-all duration-200">
                                    Continue to Sign In
                                </btn-primary>
                            </router-link>
                        </div>

                        <div class="text-center mt-6" v-if="!verified">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Already have an account?
                                <router-link to="/app/auth"
                                    class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                                    Sign in
                                </router-link>
                            </p>
                        </div>
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
            email: '',
            codes: ['', '', '', '', '', ''],
            otpInputs: [],
            countdown: 0,
            timerId: null,
            loading: false,
            verified: false,
            app_url: import.meta.env.VITE_APP_URL,
        }
    },
    mounted() {
        // Get email from query parameter
        this.email = this.$route.query.email || '';

        // Get token from query parameter (if clicking link from email)
        const token = this.$route.query.token;

        if (token && this.email) {
            // Auto-fill OTP from URL
            const tokenString = String(token);
            if (tokenString.length === 6) {
                for (let i = 0; i < 6; i++) {
                    this.codes[i] = tokenString[i] || '';
                }
                // Auto-verify
                this.$nextTick(() => {
                    this.verifyOtp();
                });
            }
        }

        // Focus first input
        this.$nextTick(() => {
            this.otpInputs = this.$refs.otpInputs;
            if (this.otpInputs && this.otpInputs[0]) {
                this.otpInputs[0].focus();
            }
        });
    },
    beforeUnmount() {
        if (this.timerId) clearInterval(this.timerId);
    },
    methods: {
        onInput(index) {
            // Only allow numbers
            this.codes[index] = this.codes[index].replace(/[^0-9]/g, '');

            if (this.codes[index].length === 1 && index < this.codes.length - 1) {
                this.otpInputs[index + 1].focus();
            }
        },
        onBackspace(index, event) {
            if (event.key === 'Backspace' && this.codes[index] === '' && index > 0) {
                this.otpInputs[index - 1].focus();
            }
        },
        onPaste(event, index) {
            event.preventDefault();
            const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');

            if (pastedData.length === 6) {
                for (let i = 0; i < 6; i++) {
                    this.codes[i] = pastedData[i] || '';
                }
                this.$nextTick(() => {
                    if (this.otpInputs && this.otpInputs[5]) {
                        this.otpInputs[5].focus();
                    }
                });
            } else if (pastedData.length > 0) {
                for (let i = index; i < Math.min(index + pastedData.length, 6); i++) {
                    this.codes[i] = pastedData[i - index] || '';
                }
                const nextIndex = Math.min(index + pastedData.length, 5);
                this.$nextTick(() => {
                    if (this.otpInputs && this.otpInputs[nextIndex]) {
                        this.otpInputs[nextIndex].focus();
                    }
                });
            }
        },
        startCountdown() {
            this.countdown = 60;
            if (this.timerId) clearInterval(this.timerId);
            this.timerId = setInterval(() => {
                if (this.countdown > 0) {
                    this.countdown--;
                } else {
                    clearInterval(this.timerId);
                    this.timerId = null;
                }
            }, 1000);
        },
        async verifyOtp() {
            const otpCode = this.codes.join('');
            if (otpCode.length !== 6) {
                this.$emit("showToast", {
                    message: "Please enter the complete 6-digit code",
                    type: "error",
                    timeout: 3000
                });
                return;
            }

            this.loading = true;

            try {
                await this.axios.get(`${this.app_url}/sanctum/csrf-cookie`);

                const response = await this.axios.post('auth/verify-email', {
                    email: this.email,
                    otp_code: otpCode
                });

                this.$emit("showToast", {
                    message: response.message || 'Email verified successfully!',
                    type: "success",
                    timeout: 3000
                });

                this.verified = true;

            } catch (error) {
                console.error(error);
                this.codes = ['', '', '', '', '', ''];
                if (this.otpInputs && this.otpInputs[0]) {
                    this.otpInputs[0].focus();
                }

                this.$emit("showToast", {
                    message: error.response?.data?.message || 'Invalid verification code',
                    type: "error",
                    timeout: 3000
                });
            } finally {
                this.loading = false;
            }
        },
        async resendOtp() {
            if (this.countdown > 0) return;

            this.loading = true;
            this.codes = ['', '', '', '', '', ''];

            try {
                await this.axios.get(`${this.app_url}/sanctum/csrf-cookie`);

                const response = await this.axios.post('auth/send-verification-email', {
                    email: this.email
                });

                this.$emit("showToast", {
                    message: response.message || 'Verification code resent successfully',
                    type: "success",
                    timeout: 3000
                });

                this.startCountdown();

            } catch (error) {
                this.$emit("showToast", {
                    message: error.response?.message || 'Failed to resend code',
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

import { defineStore } from 'pinia';

type Theme = 'light' | 'dark';

interface ThemeState {
    theme: Theme;
}

export const useThemeStore = defineStore('theme', {
    state: (): ThemeState => ({
        theme: (localStorage.getItem('theme') as Theme) || 'light',
    }),
    actions: {
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },
        initializeTheme() {
            const userPreference = localStorage.getItem('theme') as Theme | null;

            // Always default to light theme if no user preference
            this.theme = userPreference || 'light';
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        },
    },
});

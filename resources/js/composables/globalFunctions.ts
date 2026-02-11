import { App } from 'vue';
import router from '../router/router';

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $functions: {
            logout: () => Promise<void>;
            formatTanggalIndonesia: (date: string | Date, withTime?: boolean, withDay?: boolean) => string;
            dayLeft: (start: string | Date, end: string | Date) => number;
        };
        $logout: () => Promise<void>;
        $formatTanggalIndonesia: (date: string | Date, withTime?: boolean, withDay?: boolean) => string;
        $dayLeft: (start: string | Date, end: string | Date) => number;
    }
}

export default {
    install: (app: App) => {

        const logout = async (): Promise<void> => {
            console.log('Logout');
            router.push({ name: 'auth' });
        };

        const formatTanggalIndonesia = (
            date: string | Date,
            withTime: boolean = false,
            withDay: boolean = true
        ): string => {
            const options: Intl.DateTimeFormatOptions = {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric',
            };

            let formattedDate = new Intl.DateTimeFormat('id-ID', options).format(new Date(date));

            if (withTime) {
                const d = new Date(date);
                const hours = d.getHours().toString().padStart(2, '0');
                const minutes = d.getMinutes().toString().padStart(2, '0');
                const time = `${hours}:${minutes}`;

                formattedDate += ` ${time}`;
            }

            if (!withDay) {
                formattedDate = formattedDate.replace(/^[a-zA-Z]+,?\s*/, ''); // remove weekday if exists
            }

            return formattedDate;
        };

        const dayLeft = (start: string | Date, end: string | Date): number => {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const today = new Date();

            const effectiveStart = startDate < today ? today : startDate;

            return Math.ceil((endDate.getTime() - effectiveStart.getTime()) / (1000 * 60 * 60 * 24));
        };

        app.config.globalProperties.$functions = {
            logout,
            formatTanggalIndonesia,
            dayLeft,
        };

        app.config.globalProperties.$logout = logout;
        app.config.globalProperties.$formatTanggalIndonesia = formatTanggalIndonesia;
        app.config.globalProperties.$dayLeft = dayLeft;

    },
};

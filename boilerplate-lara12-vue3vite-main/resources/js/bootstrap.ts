import dashboardAxios from './api/dashboardAxios';

declare global {
    interface Window {
        axios: typeof dashboardAxios;
    }
}

// Use dashboardAxios as the global axios instance (with authentication)
window.axios = dashboardAxios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

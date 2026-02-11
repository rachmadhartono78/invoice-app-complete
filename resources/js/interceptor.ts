/**
 * Legacy interceptor.ts - maintained for backward compatibility
 * New code should use:
 * - @/api/dashboardAxios for authenticated requests
 * - @/api/publicAxios for public requests
 */
import dashboardAxios from './api/dashboardAxios';

export default dashboardAxios;

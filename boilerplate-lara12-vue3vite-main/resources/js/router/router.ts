import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import settingRoutes from './settings';
import invoicesRoutes from './invoices';


import {
    initFlowbite,
    initAccordions,
    initCarousels,
    initCollapses,
    initDials,
    initDismisses,
    initDrawers,
    initDropdowns,
    initModals,
    initPopovers,
    initTabs,
    initTooltips,
} from "flowbite";

const routes: RouteRecordRaw[] = [
    // Public Website Routes (no authentication required)
    {
        path: "/",
        component: () => import("@/components/layouts/WebsiteLayout.vue"),
        children: [
            {
                path: "",
                name: "website-home",
                component: () => import("@/components/pages/website/Home.vue"),
            },
            {
                path: "about",
                name: "website-about",
                component: () => import("@/components/pages/website/About.vue"),
            },
            {
                path: "features",
                name: "website-features",
                component: () => import("@/components/pages/website/Features.vue"),
            },
            {
                path: "contact",
                name: "website-contact",
                component: () => import("@/components/pages/website/Contact.vue"),
            },
        ]
    },

    // Dashboard Routes (authentication required)
    {
        path: "/app",
        children: [
            {
                path: "auth",
                name: "auth",
                component: () => import("@/components/authentifications/Login.vue"),
            },
            {
                path: "register",
                name: "register",
                component: () => import("@/components/authentifications/Register.vue"),
            },
            {
                path: "verify-email",
                name: "verify-email",
                component: () => import("@/components/authentifications/VerifyEmail.vue"),
            },
            {
                path: "login-success",
                name: "login-success",
                component: () => import("@/components/authentifications/LoginSuccess.vue"),
            },
            {
                path: "",
                component: () => import("@/components/layouts/BaseLayout.vue"),
                meta: {
                    requiresAuth: true
                },
                children: [
                    {
                        path: "",
                        name: "home",
                        component: () => import("@/components/main/Home.vue"),
                    },

                    {
                        path: "profile",
                        name: 'profile',
                        component: () => import("@/components/accounts/Account.vue"),
                    },
                    {
                        path: "notification",
                        name: 'notification',
                        component: () => import("@/components/accounts/Notification.vue"),
                    },

                    // Settings routes (RBAC configuration)
                    ...settingRoutes,
                    ...invoicesRoutes,
                ],
            },
            {
                path: "/maintenance",
                name: "maintenance",
                component: () =>
                    import("@/components/z_errors/Maintenance.vue"),
            },
            {
                path: "/not-found",
                name: "not-found",
                component: () => import("@/components/z_errors/NotFound404.vue"),
            },
            {
                path: "/:pathMatch(.*)*",
                component: () => import("@/components/z_errors/NotFound404.vue"),
            },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, _from, next) => {
    ``
    const authStore = useAuthStore();
    const { isAuthenticated } = authStore;

    // Public website routes: all routes NOT under /app
    const isPublicWebsite = !to.path.startsWith('/app');

    // Auth routes: login, register, verify-email and OAuth callback pages
    const isAuthRoute = to.path === '/app/auth' || to.path === '/app/register' || to.path === '/app/verify-email' || to.path === '/app/login-success';

    // Allow access to public website and auth pages
    if (isPublicWebsite || isAuthRoute) {
        return next();
    }

    // Protected dashboard routes: require authentication
    if (to.meta.requiresAuth === true && !isAuthenticated) {
        return next('/app/auth?destination=' + to.fullPath);
    }

    // Optional: Uncomment to enable menu-based authorization
    const getMenus = authStore.getMenus;
    const isAuthorized = getMenus.some(menu => to.path.startsWith(menu.full_path));
    console.log(`Navigating to ${to.fullPath}, Authorized: ${isAuthorized}`);

    // if (!isAuthorized && to.meta.requiresAuth === true && !['home', 'profile', 'notification'].includes(to.name as string)) {
    //     return next('/not-found');

    // }

    next();
});

router.afterEach(() => {
    setTimeout(() => {
        initFlowbite();
        initAccordions();
        initCarousels();
        initCollapses();
        initDials();
        initDismisses();
        initDrawers();
        initDropdowns();
        initModals();
        initPopovers();
        initTabs();
        initTooltips();
    }, 100);
});


export default router;

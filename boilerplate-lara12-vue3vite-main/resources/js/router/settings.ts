import { RouteRecordRaw } from 'vue-router';

const settingsRoutes: RouteRecordRaw[] = [
    {
        path: "settings",
        children: [
            {
                path: "manage",
                meta: {
                    breadcrumb: "Manage",
                },
                children: [
                    {
                        path: "dashboard",
                        name: "settings-dashboard",
                        meta: {
                            breadcrumb: "Dashboard",
                        },
                        component: () =>
                            import("@/components/pages/settings/Dashboard.vue"),
                    },
                    {
                        path: "",
                        name: "index-manage",
                        component: () =>
                            import("@/components/pages/manage/Index.vue"),
                    },
                    {
                        path: "applications",
                        meta: {
                            breadcrumb: "Aplikasi",
                        },
                        children: [
                            {
                                path: "",
                                name: "index-applications",
                                component: () =>
                                    import(
                                        "@/components/pages/manage/applications/Index.vue"
                                    ),
                            },
                            {
                                path: "create",
                                name: "create-applications",
                                meta: {
                                    breadcrumb: "Tambah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/applications/Create.vue"
                                    ),
                            },
                            {
                                path: "update/:id",
                                name: "update-applications",
                                meta: {
                                    breadcrumb: "Ubah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/applications/Create.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "menus",
                        meta: {
                            breadcrumb: "Menu",
                        },
                        children: [
                            {
                                path: "",
                                name: "index-menus",
                                component: () =>
                                    import(
                                        "@/components/pages/manage/menus/Index.vue"
                                    ),
                            },
                            {
                                path: "create",
                                name: "create-menus",
                                meta: {
                                    breadcrumb: "Tambah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/menus/Create.vue"
                                    ),
                            },
                            {
                                path: "update/:id",
                                name: "update-menus",
                                meta: {
                                    breadcrumb: "Ubah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/menus/Create.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "authorities",
                        meta: {
                            breadcrumb: "Otoritas",
                        },
                        children: [
                            {
                                path: "",
                                name: "index-authorities",
                                component: () =>
                                    import(
                                        "@/components/pages/manage/authorities/Index.vue"
                                    ),
                            },
                            {
                                path: "create",
                                name: "create-authorities",
                                meta: {
                                    breadcrumb: "Tambah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/authorities/Create.vue"
                                    ),
                            },
                            {
                                path: "update/:id",
                                name: "update-authorities",
                                meta: {
                                    breadcrumb: "Ubah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/authorities/Create.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "organizations",
                        meta: {
                            breadcrumb: "Organisasi",
                        },
                        children: [
                            {
                                path: "",
                                name: "index-organizations",
                                component: () =>
                                    import(
                                        "@/components/pages/manage/organizations/Index.vue"
                                    ),
                            },
                            {
                                path: "create",
                                name: "create-organizations",
                                meta: {
                                    breadcrumb: "Tambah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/organizations/Create.vue"
                                    ),
                            },
                            {
                                path: "update/:id",
                                name: "update-organizations",
                                meta: {
                                    breadcrumb: "Ubah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/organizations/Create.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "users",
                        meta: {
                            breadcrumb: "Pengguna",
                        },
                        children: [
                            {
                                path: "",
                                name: "index-users",
                                component: () =>
                                    import(
                                        "@/components/pages/manage/users/Index.vue"
                                    ),
                            },
                            {
                                path: "create",
                                name: "create-users",
                                meta: {
                                    breadcrumb: "Tambah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/users/Create.vue"
                                    ),
                            },
                            {
                                path: "detail/:id",
                                name: "detail-users",
                                meta: {
                                    breadcrumb: "Detail",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/users/Detail.vue"
                                    ),
                            },
                            {
                                path: "update/:id",
                                name: "update-users",
                                meta: {
                                    breadcrumb: "Ubah",
                                },
                                component: () =>
                                    import(
                                        "@/components/pages/manage/users/Create.vue"
                                    ),
                            },
                        ],
                    },
                    {
                        path: "notifications/test",
                        name: "notification-test",
                        meta: {
                            breadcrumb: "Notification Test",
                        },
                        component: () =>
                            import(
                                "@/components/pages/settings/NotificationTest.vue"
                            ),
                    },
                    {
                        path: "notifications/test",
                        name: "notification-test",
                        meta: {
                            breadcrumb: "Notification Test",
                        },
                        component: () =>
                            import(
                                "@/components/pages/settings/NotificationTest.vue"
                            ),
                    },
                ],
            },
        ],
    },
];

export default settingsRoutes;

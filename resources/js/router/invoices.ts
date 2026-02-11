import { RouteRecordRaw } from 'vue-router';

const invoicesRoutes: RouteRecordRaw[] = [
    {
        path: "invoices",
        meta: {
            breadcrumb: "Invoices",
        },
        children: [
            {
                path: "quotations",
                name: "quotations-index",
                meta: {
                    breadcrumb: "Penawaran Harga",
                },
                component: () => import("@/components/pages/quotations/Index.vue"),
            },
            // Items routes - MUST be before invoices/:id to avoid matching "items" as :id
            {
                path: "items",
                name: "items-index",
                meta: {
                    breadcrumb: "Produk & Layanan",
                },
                component: () => import("@/components/pages/items/Index.vue"),
            },
            {
                path: "items/create",
                name: "items-create",
                meta: {
                    breadcrumb: "Tambah Produk",
                },
                component: () => import("@/components/pages/items/Form.vue"),
            },
            {
                path: "items/:id/edit",
                name: "items-edit",
                meta: {
                    breadcrumb: "Edit Produk",
                },
                component: () => import("@/components/pages/items/Form.vue"),
            },
            // Customers routes
            {
                path: "customers",
                name: "customers-index",
                meta: {
                    breadcrumb: "Pelanggan",
                },
                component: () => import("@/components/pages/customers/Index.vue"),
            },
            {
                path: "customers/create",
                name: "customers-create",
                meta: {
                    breadcrumb: "Buat Pelanggan",
                },
                component: () => import("@/components/pages/customers/Form.vue"),
            },
            {
                path: "customers/:id/edit",
                name: "customers-edit",
                meta: {
                    breadcrumb: "Edit Pelanggan",
                },
                component: () => import("@/components/pages/customers/Form.vue"),
            },
            // Payments routes
            {
                path: "payments",
                name: "payments-index",
                meta: {
                    breadcrumb: "Pembayaran",
                },
                component: () => import("@/components/pages/payments/Index.vue"),
            },
            {
                path: "payments/create",
                name: "payments-create",
                meta: {
                    breadcrumb: "Catat Pembayaran",
                },
                component: () => import("@/components/pages/payments/Form.vue"),
            },
            {
                path: "payments/:id/edit",
                name: "payments-edit",
                meta: {
                    breadcrumb: "Edit Pembayaran",
                },
                component: () => import("@/components/pages/payments/Form.vue"),
            },
            // Invoices routes - :id routes MUST be last to avoid matching other paths
            {
                path: "invoices",
                name: "invoices-index",
                meta: {
                    breadcrumb: "Faktur",
                },
                component: () => import("@/components/pages/invoices/Index.vue"),
            },
            {
                path: "invoices/create",
                name: "invoices-create",
                meta: {
                    breadcrumb: "Buat Faktur",
                },
                component: () => import("@/components/pages/invoices/Form.vue"),
            },
            {
                path: "invoices/:id/edit",
                name: "invoices-edit",
                meta: {
                    breadcrumb: "Edit Faktur",
                },
                component: () => import("@/components/pages/invoices/Form.vue"),
            },
            {
                path: "invoices/:id",
                name: "invoices-view",
                meta: {
                    breadcrumb: "Detail Faktur",
                },
                component: () => import("@/components/pages/invoices/View.vue"),
            },
        ],
    },
];

export default invoicesRoutes;

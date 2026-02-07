import { RouteRecordRaw } from 'vue-router';

const invoicesRoutes: RouteRecordRaw[] = [
    {
        path: "invoices",
        children: [
             {
                    path: "quotations",
                    name: "quotations-index",
                    component: () => import("@/components/pages/quotations/Index.vue"),
                    },
             {
                    path: "invoices",
                    name: "invoices-index",
                    component: () => import("@/components/pages/invoices/Index.vue"),
                    },
                    {
                    path: "invoices/create",
                    name: "invoices-create",
                    component: () => import("@/components/pages/invoices/Form.vue"),
                    },
                    {
                    path: "invoices/:id",
                    name: "invoices-view",
                    component: () => import("@/components/pages/invoices/View.vue"),
                    },
                    {
                    path: "invoices/:id/edit",
                    name: "invoices-edit",
                    component: () => import("@/components/pages/invoices/Form.vue"),
                    },
                    {
                    path: "customers",
                    name: "customers-index",
                    component: () => import("@/components/pages/customers/Index.vue"),
                    },
                    {
                    path: "customers/create",
                    name: "customers-create",
                    component: () => import("@/components/pages/customers/Form.vue"),
                    },
                    {
                    path: "customers/:id/edit",
                    name: "customers-edit",
                    component: () => import("@/components/pages/customers/Form.vue"),
                    },
                    {
                    path: "payments",
                    name: "payments-index",
                    component: () => import("@/components/pages/payments/Index.vue"),
                    },
                    {
                    path: "payments/create",
                    name: "payments-create",
                    component: () => import("@/components/pages/payments/Form.vue"),
                    },
                    {
                    path: "payments/:id/edit",
                    name: "payments-edit",
                    component: () => import("@/components/pages/payments/Form.vue"),
                    },
        ],
    },
];

export default invoicesRoutes;

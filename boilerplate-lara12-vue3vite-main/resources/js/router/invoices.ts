import { RouteRecordRaw } from 'vue-router';

const invoicesRoutes: RouteRecordRaw[] = [
    {
        path: "invoices",
        children: [
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
        ],
    },
];

export default invoicesRoutes;

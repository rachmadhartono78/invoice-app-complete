<template>
    <div class="container mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Management</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Manage your application's RBAC configuration, organizations, and users
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- System Dashboard Card (Relocated Old Home) -->
            <router-link
                to="/app/settings/manage/dashboard"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">System Dashboard</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Overview of system statistics including total users, organizations, and quick management links.
                </p>
            </router-link>

            <!-- Applications Card -->
            <router-link
                to="/app/settings/manage/applications"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Applications</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Manage applications in your system. Each application can have its own menus and access control.
                </p>
            </router-link>

            <!-- Menus Card -->
            <router-link
                to="/app/settings/manage/menus"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Menus</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Configure navigation menus with hierarchical structure and assign them to applications.
                </p>
            </router-link>

            <!-- Authorities Card -->
            <router-link
                to="/app/settings/manage/authorities"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Authorities</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Define roles and permissions. Link authorities to menus with specific actions.
                </p>
            </router-link>

            <!-- Organizations Card -->
            <router-link
                to="/app/settings/manage/organizations"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Organizations</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Manage organizational units for multi-tenancy and user segmentation.
                </p>
            </router-link>

            <!-- Users Card -->
            <router-link
                to="/app/settings/manage/users"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Users</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Manage user accounts, assign authorities, and configure access to organizations.
                </p>
            </router-link>

            <!-- Notification Test Card -->
            <router-link
                to="/app/settings/manage/notifications/test"
                class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
            >
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-teal-100 dark:bg-teal-900">
                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                </div>
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Notification Testing</h5>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    Test real-time notifications with Laravel Reverb and WebSocket private channels.
                </p>
            </router-link>
        </div>
    </div>
</template>

<script setup lang="ts">
// Management index page
</script>

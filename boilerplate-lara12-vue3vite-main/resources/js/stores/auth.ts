import { defineStore } from 'pinia';
import type { User } from '@/types';

interface MenuItem {
    name: string;
    url: string;
    order: number;
    icon?: string;
    menu_induk?: string | null;
    children?: MenuItem[];
}

interface AuthState {
    user: User | null;
}

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        user: null,
    }),
    getters: {
        isAuthenticated: (state): boolean => state.user !== null,

        getOrganizations: (state) => state.user ? state.user.organizations || [] : [],

        getApplications: (state): Application[] => {
            if (!state.user || !state.user.authorities) return [];
            return state.user.authorities.map((authority) => authority.application);
        },

        getMenus: (state) => {
            if (!state.user?.authorities) return [];
            return state.user.authorities.flatMap(({ menus }) =>
                menus.map(menu => ({
                    ...menu,
                    full_path: menu.menu_induk
                        ? `/app/${menu.application.url}/${menu.menu_induk.url}/${menu.url}`
                        : `/app/${menu.application.url}/${menu.url}`
                }))
            );
        },

        getMenuInduk: (state): MenuItem[] => {
            if (!state.user || !state.user.authorities) return [];

            const menuMap = new Map<string, MenuItem>();
            const rootMenus: MenuItem[] = [];

            state.user.authorities.forEach((authority) => {
                if (!authority.menus || !Array.isArray(authority.menus)) return;

                authority.menus.forEach((menu) => {
                    // Skip if menu doesn't have required properties
                    if (!menu || !menu.application) return;

                    if (menu.menu_induk && menu.menu_induk.name) {
                        const parentName = menu.menu_induk.name;

                        if (!menuMap.has(parentName)) {
                            // Create parent menu entry if not exists
                            menuMap.set(parentName, {
                                name: parentName,
                                url: menu.menu_induk.url,
                                order: menu.menu_induk.order,
                                icon: menu.menu_induk.icon,
                                children: [],
                            });
                            rootMenus.push(menuMap.get(parentName)!);
                        }

                        // Add child menu to the parent
                        const parentMenu = menuMap.get(parentName);
                        if (parentMenu && parentMenu.children) {
                            parentMenu.children.push({
                                name: menu.name,
                                url: `/app/${menu.application.url}/${menu.menu_induk.url}/${menu.url}`,
                                icon: menu.icon,
                                order: menu.order,
                            });
                        }
                    } else {
                        // Directly add standalone root menus
                        if (!menuMap.has(menu.name)) {
                            menuMap.set(menu.name, {
                                name: menu.name,
                                url: `/app/${menu.application.url}/${menu.url}`,
                                order: menu.order,
                                menu_induk: null,
                                icon: menu.icon,
                            });
                            rootMenus.push(menuMap.get(menu.name)!);
                        }
                    }
                });
            });

            // Sort parent menus by order
            rootMenus.sort((a, b) => a.order - b.order);

            // Sort child menus inside each parent
            rootMenus.forEach((parent) => {
                if (parent.children) {
                    parent.children.sort((a, b) => a.order - b.order);
                }
            });

            return rootMenus;
        }
    },
    actions: {
        setUser(userData: User | null) {
            this.user = userData;
        },
        clearUser() {
            this.user = null;
        },
    },
    persist: true,
});

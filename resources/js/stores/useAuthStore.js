import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import axios from 'axios';

export const useAuthStore = create(
    persist(
        (set, get) => ({
            user: null,
            token: null,
            isAuthenticated: () => get().user !== null,

            setUser: (user, token) => set({ user, token }),

            logout: async () => {
                const { token } = get();
                if (token) {
                    await axios.post('/api/auth/logout', {}, {
                        headers: { Authorization: `Bearer ${token}` },
                    }).catch(() => {});
                }
                set({ user: null, token: null });
            },

            hasRole: (role) => {
                const { user } = get();
                return user?.roles?.includes(role) ?? false;
            },
        }),
        { name: 'nextgen-auth' }
    )
);

// Configure Axios defaults
axios.defaults.baseURL = window.location.origin;
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

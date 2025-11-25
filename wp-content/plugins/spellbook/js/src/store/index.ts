import { create } from 'zustand';

interface State {
    notifications: Array<{
        id: number;
        message: string;
        type: 'success' | 'error';
    }>;
    showNotification: (message: string, type: 'success' | 'error') => void;
    removeNotification: (id: number) => void;
    forceLicenseRefresh: boolean;
    forceProductRefresh: boolean;
    forceAnnouncementRefresh: boolean;
    setForceLicenseRefresh: (force: boolean) => void;
    setForceProductRefresh: (force: boolean) => void;
    setForceAnnouncementRefresh: (force: boolean) => void;
}

const useStore = create<State>((set) => ({
    notifications: [],
    showNotification: (message, type) => {
        const notification = {
            id: Date.now(),
            message,
            type
        };

        set(state => ({
            notifications: [...state.notifications, notification]
        }));
    },
    removeNotification: (id) => {
        set(state => ({
            notifications: state.notifications.filter(notification => notification.id !== id)
        }));
    },
    forceLicenseRefresh: false,
    forceProductRefresh: false,
    forceAnnouncementRefresh: false,
    setForceLicenseRefresh: (force) => set({ forceLicenseRefresh: force }),
    setForceProductRefresh: (force) => set({ forceProductRefresh: force }),
    setForceAnnouncementRefresh: (force) => set({ forceAnnouncementRefresh: force })
}));

export default useStore;

import { useQueryClient } from '@tanstack/react-query';
import useStore from '../../store';
import { useCallback, useEffect } from 'react';

export const useRefreshAll = () => {
	const queryClient = useQueryClient();
	const setForceLicenseRefresh = useStore(state => state.setForceLicenseRefresh);
	const setForceProductRefresh = useStore(state => state.setForceProductRefresh);
	const setForceAnnouncementRefresh = useStore(state => state.setForceAnnouncementRefresh);
	const forceLicenseRefresh = useStore(state => state.forceLicenseRefresh);
	const forceProductRefresh = useStore(state => state.forceProductRefresh);
	const forceAnnouncementRefresh = useStore(state => state.forceAnnouncementRefresh);

	const refresh = useCallback(async () => {
		if (forceLicenseRefresh) {
			await queryClient.resetQueries({ queryKey: ['licenses'] });
		}

		if (forceProductRefresh) {
			await queryClient.resetQueries({ queryKey: ['products'] });
		}

		if (forceAnnouncementRefresh) {
			await queryClient.resetQueries({ queryKey: ['announcements'] });
		}
	}, [
		forceLicenseRefresh,
		forceProductRefresh,
		forceAnnouncementRefresh,
		queryClient
	]);

	useEffect(() => {
		refresh();
	}, [
		forceLicenseRefresh,
		forceProductRefresh,
		forceAnnouncementRefresh,
		queryClient,
	]);

	return () => {
		setForceLicenseRefresh(true);
		setForceProductRefresh(true);
		setForceAnnouncementRefresh(true);
	};
};

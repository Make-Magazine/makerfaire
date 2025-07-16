import { useQueryClient } from '@tanstack/react-query';
import useStore from '../../store';
import { useEffect } from 'react';

export const useRefreshAll = () => {
	const queryClient = useQueryClient();
	const setForceLicenseRefresh = useStore(state => state.setForceLicenseRefresh);
	const setForceProductRefresh = useStore(state => state.setForceProductRefresh);
	const forceLicenseRefresh = useStore(state => state.forceLicenseRefresh);
	const forceProductRefresh = useStore(state => state.forceProductRefresh);

	useEffect(() => {
		const refresh = async () => {
			if (forceLicenseRefresh) {
				await queryClient.resetQueries({ queryKey: ['licenses'] });
				setForceLicenseRefresh(false);
				setForceProductRefresh(true);
			} else if (forceProductRefresh) {
				await queryClient.resetQueries({ queryKey: ['products'] });
				setForceProductRefresh(false);
			}
		};
		refresh();
	}, [forceLicenseRefresh, forceProductRefresh, queryClient, setForceLicenseRefresh, setForceProductRefresh]);

	return () => {
		setForceLicenseRefresh(true);
		setForceProductRefresh(true);
	};
};

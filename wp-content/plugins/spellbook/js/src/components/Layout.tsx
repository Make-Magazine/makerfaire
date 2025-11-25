import { useEffect } from 'react';
import ModularSidebarLayout from '@gravityforms/components/react/admin/modules/Layouts/ModularSidebar';
import { Routes, Route } from 'react-router-dom';
import ErrorBoundary from './ErrorBoundary';
import Header from './Header';
import './Layout.css';
import PrimarySideBar from './PrimarySideBar';
import All from '../pages/All';
import Perks from '../pages/Perks';
import Shop from '../pages/Shop';
import Connect from '../pages/Connect';
import Licenses from '../pages/Licenses';
import Free from '../pages/Free';
import useStore from '../store';
import { useSnackbar } from '@gravityforms/components/react/admin/modules/SnackBar';
import { useProducts } from '../hooks/api/useProducts';
import { useLicense } from '../hooks/api/useLicenses';

const Layout = () => {
	const { notifications, removeNotification } = useStore();
	const addSnackbarMessage = useSnackbar();

	// These hooks will automatically fetch data
	useProducts();
	useLicense('perk');
	useLicense('connect');
	useLicense('shop');
	useLicense('wiz-bundle');

	// Pull notifications from Zustand store and show them using addSnackbarMessage then remove them from store.
	useEffect(() => {
		if (notifications.length > 0) {
			notifications.forEach(notification => {
				addSnackbarMessage(notification.message, notification.type, {
					delay: 5000,
					interactive: false,
				});
				removeNotification(notification.id);
			});
		}
	}, [notifications, addSnackbarMessage, removeNotification]);

	return (
		<div className="spellbook-app">
			<ModularSidebarLayout
				Header={Header}
				PrimarySideBarChildren={PrimarySideBar}
			>
				<div className="spellbook-app__content">
					<Routes>
						<Route path="/" element={<ErrorBoundary><All /></ErrorBoundary>} />
						<Route path="/perks" element={<ErrorBoundary><Perks /></ErrorBoundary>} />
						<Route path="/connect" element={<ErrorBoundary><Connect /></ErrorBoundary>} />
						<Route path="/shop" element={<ErrorBoundary><Shop /></ErrorBoundary>} />
						<Route path="/licenses" element={<ErrorBoundary><Licenses /></ErrorBoundary>} />
						<Route path="/free-plugins" element={<ErrorBoundary><Free /></ErrorBoundary>} />
						<Route path="*" element={<ErrorBoundary><div>Not Found</div></ErrorBoundary>} />
					</Routes>
				</div>
			</ModularSidebarLayout>
		</div>
	);
};

export default Layout;

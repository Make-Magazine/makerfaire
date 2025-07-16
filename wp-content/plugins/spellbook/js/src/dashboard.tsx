import React from 'react';
import { createRoot } from 'react-dom/client';
import Layout from './components/Layout';
import { createHashRouter, RouterProvider } from 'react-router-dom';
import { SnackbarProvider } from '@gravityforms/components/react/admin/modules/SnackBar';
import { QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { queryClient } from './queryClient';

// Create a hash router (uses #/ in URLs)
const router = createHashRouter([
	{
		path: '/*', // Catch all routes and let the nested routes handle them
		element: <Layout />
	}
]);

const Dashboard = () => {
	return (
		<QueryClientProvider client={queryClient}>
			<SnackbarProvider defaultSettings={{
				closeButtonAttributes: {
					icon: 'x',
					iconPrefix: 'gravity-component-icon',
				},
			}}>
				<RouterProvider router={router} />
			</SnackbarProvider>
			<ReactQueryDevtools />
		</QueryClientProvider>
	);
};

const container = document.getElementById('gwiz-spellbook');
if (container) {
	const root = createRoot(container);
	root.render(<Dashboard />);
}

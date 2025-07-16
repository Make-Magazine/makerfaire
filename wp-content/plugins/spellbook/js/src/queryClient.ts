import { QueryClient } from '@tanstack/react-query';
import useStore from './store';
import { __ } from '@wordpress/i18n';

interface WPError {
    error: {
        code: string;
        message: string;
        data?: {
            status: number;
            [key: string]: any;
        };
    };
}

interface WPRestError {
    code: string;
    message: string;
    data?: {
        status: number;
        [key: string]: any;
    };
}

function isWPError(error: unknown): error is WPError {
    return error !== null
        && typeof error === 'object'
        && 'error' in error
        && typeof (error as any).error === 'object'
        && 'message' in (error as any).error;
}

function isWPRestError(error: unknown): error is WPRestError {
    return error !== null
        && typeof error === 'object'
        && 'code' in error
        && 'message' in error
        && typeof (error as any).message === 'string';
}

export const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: Infinity, // Only refetch when we explicitly invalidate
            retry: false, // Don't retry failed requests
        },
        mutations: {
            onError: (error: unknown) => {
				console.log({ error });
                const message = isWPError(error)
                    ? error.error.message
                    : isWPRestError(error)
                        ? error.message
                        : __('An unknown error occurred', 'spellbook');
                useStore.getState().showNotification(message, 'error');
            }
        }
    }
});

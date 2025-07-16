import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import useStore from '../../store';

interface RegistrationStatus {
    is_registered: boolean;
    email?: string;
}

interface RegistrationError {
    success: false;
    error: string;
    message: string;
}

interface RegistrationSuccess {
    success: true;
    message: string;
}

type RegistrationResponse = RegistrationSuccess | RegistrationError;

export const useSpellbookRegistration = () => {
    const queryClient = useQueryClient();

    // Query for registration status
    const registrationQuery = useQuery<RegistrationStatus>({
        queryKey: ['spellbook', 'registration'],
        queryFn: () => apiFetch({ path: '/gwiz/v1/spellbook/registration-status' }),
        staleTime: Infinity // Only refetch when explicitly invalidated
    });

    // Mutation for registration
    const registerMutation = useMutation<
        RegistrationResponse,
        Error,
        { email: string; name: string }
    >({
        mutationFn: (data) =>
            apiFetch({
                path: '/gwiz/v1/spellbook/register',
                method: 'POST',
                data
            }),
        onSuccess: (response) => {
            if (response.success) {
                queryClient.invalidateQueries({ queryKey: ['spellbook', 'registration'] });
                useStore.getState().showNotification(response.message, 'success');
            } else {
                throw new Error(response.message);
            }
        },
        onError: (error) => {
            useStore.getState().showNotification(error.message, 'error');
        }
    });

    return {
        registrationStatus: registrationQuery.data,
        isLoading: registrationQuery.isLoading,
        register: registerMutation.mutate,
        isPending: registerMutation.isPending
    };
};

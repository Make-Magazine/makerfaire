import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import apiFetch from '@wordpress/api-fetch';
import useStore from '../../store';
import type { AnnouncementsResponse, DismissAnnouncementResponse } from '../../types/announcements';

export const useAnnouncements = () => {
    const queryClient = useQueryClient();
    const forceAnnouncementRefresh = useStore(state => state.forceAnnouncementRefresh);
    const setForceAnnouncementRefresh = useStore(state => state.setForceAnnouncementRefresh);

    // Query for fetching announcements
    const announcementsQuery = useQuery<AnnouncementsResponse>({
        queryKey: ['announcements'],
        queryFn: async ({ signal }) => {
            const announcements = await apiFetch<AnnouncementsResponse>({
                path: `/gwiz/v1/announcements${forceAnnouncementRefresh ? '?force=1' : ''}`,
                signal
            });

			if (forceAnnouncementRefresh) {
				setForceAnnouncementRefresh(false);
			}

			return announcements;
        },
        staleTime: Infinity // Only refetch when explicitly invalidated
    });

    // Mutation for dismissing announcements
    const dismissMutation = useMutation<
        DismissAnnouncementResponse,
        Error,
        string,
        { previousAnnouncements: AnnouncementsResponse | undefined }
    >({
        mutationFn: (id: string) =>
            apiFetch({
                path: `/gwiz/v1/announcements/${id}/dismiss`,
                method: 'POST',
                data: { id }
            }),
        onMutate: async (id: string) => {
            // Cancel any outgoing refetches
            await queryClient.cancelQueries({ queryKey: ['announcements'] });

            // Snapshot the previous value
            const previousAnnouncements = queryClient.getQueryData<AnnouncementsResponse>(['announcements']);

            // Optimistically update to remove the dismissed announcement
            queryClient.setQueryData<AnnouncementsResponse>(
                ['announcements'],
                (old) => {
                    if (!old) return old;
                    return {
                        announcements: old.announcements.filter(a => a.id !== id)
                    };
                }
            );

            // Return context with the previous value
            return { previousAnnouncements };
        },
        onError: (error, id, context) => {
            // Rollback on error
            if (context?.previousAnnouncements) {
                queryClient.setQueryData(['announcements'], context.previousAnnouncements);
            }
            useStore.getState().showNotification(error.message, 'error');
        },
        onSettled: () => {
            // Refetch to ensure we're in sync with the server
            queryClient.invalidateQueries({ queryKey: ['announcements'] });
        }
    });

    return {
        announcements: announcementsQuery.data?.announcements || [],
        isLoading: announcementsQuery.isLoading,
        dismiss: dismissMutation.mutate,
        isDismissing: dismissMutation.isPending
    };
};

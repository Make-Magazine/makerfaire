export interface AnnouncementConditions {
    php_version?: string;
    wp_version?: string;
    gf_version?: string;
    spellbook_version?: string;
    has_perks_license?: boolean;
    has_connect_license?: boolean;
    has_shop_license?: boolean;
    has_wiz_bundle_license?: boolean;
}

export interface Announcement {
    id: string;
    style: 'black-friday' | 'info' | 'warning' | 'success';
    heading: string;
    text: string;
    cta: {
        text: string;
        url: string;
    };
    image_url?: string;
    start_date: string;
    end_date: string;
    conditions?: AnnouncementConditions;
}

export interface AnnouncementsResponse {
    announcements: Announcement[];
}

export interface DismissAnnouncementResponse {
    success: boolean;
    dismissed: string[];
}

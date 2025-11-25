import { useAnnouncements } from '../hooks/api/useAnnouncements';
import { addUtmParams } from '../helpers/urls';
import { processMarkdown } from '../helpers/markdown';
import type { Announcement } from '../types/announcements';
import './AnnouncementBanner.css';

const AnnouncementBanner = () => {
    const { announcements, isLoading, dismiss } = useAnnouncements();

    // Don't render anything while loading or if no announcements
    if (isLoading || announcements.length === 0) {
        return null;
    }

    const handleDismiss = (id: string) => {
        dismiss(id);
    };

    const renderAnnouncement = (announcement: Announcement) => {
        const styleClass = `announcement-banner--${announcement.style}`;

        // Get CTA URL with UTM params
        const getCtaUrl = () => {
            if (!announcement.cta?.url) {
                return '#';
            }

            try {
                return addUtmParams(announcement.cta.url, {
                    component: 'announcement-banner',
                    text: announcement.id
                });
            } catch (error) {
                // If URL is invalid, return as-is
                return announcement.cta.url;
            }
        };

        return (
            <div key={announcement.id} className={`announcement-banner ${styleClass}`}>
                <button
                    className="announcement-banner__close"
                    onClick={() => handleDismiss(announcement.id)}
                    aria-label="Dismiss announcement"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 1L1 13M1 1L13 13" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                </button>

                <div className="announcement-banner__content">
                    {announcement.image_url && (
                        <img
                            src={announcement.image_url}
                            alt=""
                            className="announcement-banner__image"
                        />
                    )}

                    <div className="announcement-banner__text">
                        <h3 className="announcement-banner__heading">{announcement.heading}</h3>
                        <div className="announcement-banner__description">
                            {processMarkdown(announcement.text, {
                                addUtm: true,
                                utmComponent: 'announcement-banner',
                                utmText: announcement.id
                            })}
                        </div>
                        {announcement.cta?.url && announcement.cta?.text && (
                            <a
                                href={getCtaUrl()}
                                className="announcement-banner__cta"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                {announcement.cta.text}
                            </a>
                        )}
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div className="announcement-banner-container">
            {announcements.map(renderAnnouncement)}
        </div>
    );
};

export default AnnouncementBanner;

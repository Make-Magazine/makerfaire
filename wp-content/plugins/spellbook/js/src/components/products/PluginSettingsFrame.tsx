import { useRef, useState, useEffect } from 'react';
import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';

interface PluginSettingsFrameProps {
    src: string;
}

const PluginSettingsFrame = ({ src }: PluginSettingsFrameProps) => {
    const ref = useRef<HTMLIFrameElement>(null);
    const [height, setHeight] = useState('500px');
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const updateHeight = () => {
            if (ref.current?.contentWindow?.document.body) {
                const newHeight = ref.current.contentWindow.document.body.scrollHeight;
                setHeight(`${newHeight}px`);
            }
        };

        // Update height and loading state when iframe loads
        if (ref.current) {
            ref.current.onload = () => {
                updateHeight();
                setIsLoading(false);
            };
        }

        // Set up interval to check for dynamic content changes
        const interval = setInterval(updateHeight, 500);

        return () => clearInterval(interval);
    }, []);

    return (
        <div style={{ minHeight: '400px', position: 'relative' }}>
            {isLoading && (
                <div style={{ position: 'absolute', width: '100%', height: '100%', display: 'flex', justifyContent: 'center', alignItems: 'center', padding: '20px' }}>
                    <RingLoader />
                </div>
            )}
            <iframe
                ref={ref}
                src={src}
                style={{
                    width: '100%',
                    height,
                    border: 'none',
                    overflow: 'hidden',
                    visibility: isLoading ? 'hidden' : 'visible'
                }}
                scrolling="no"
            />
        </div>
    );
};

export default PluginSettingsFrame;

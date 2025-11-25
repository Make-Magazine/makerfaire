import { addUtmParams } from './urls';

interface ProcessMarkdownOptions {
    addUtm?: boolean;
    utmComponent?: string;
    utmText?: string;
}

/**
 * Process light markdown: **bold**, *italic*, [link](url), and double newlines to paragraphs
 */
export function processMarkdown(text: string, options: ProcessMarkdownOptions = {}): JSX.Element[] | null {
    if (!text) return null;

    const { addUtm = false, utmComponent = '', utmText = '' } = options;

    // Normalize line endings (handle \r\n, \r, and \n)
    const normalizedText = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

    // Split by double newlines to create paragraphs
    const paragraphs = normalizedText.split(/\n\n+/);

    return paragraphs.map((paragraph, pIndex) => {
        // Process inline markdown
        const parts: (string | JSX.Element)[] = [];
        let lastIndex = 0;

        // Combined regex for **bold**, *italic*, and [link](url)
        const regex = /(\*\*([^*]+)\*\*|\*([^*]+)\*|\[([^\]]+)\]\(([^)]+)\))/g;
        let match;

        while ((match = regex.exec(paragraph)) !== null) {
            // Add text before the match
            if (match.index > lastIndex) {
                parts.push(paragraph.substring(lastIndex, match.index));
            }

            if (match[2]) {
                // **bold**
                parts.push(<strong key={`${pIndex}-${match.index}`}>{match[2]}</strong>);
            } else if (match[3]) {
                // *italic*
                parts.push(<em key={`${pIndex}-${match.index}`}>{match[3]}</em>);
            } else if (match[4] && match[5]) {
                // [link](url)
                let linkUrl = match[5];

                // Add UTM params if requested
                if (addUtm) {
                    try {
                        linkUrl = addUtmParams(linkUrl, {
                            component: utmComponent,
                            text: utmText
                        });
                    } catch (error) {
                        // If URL is invalid, use original
                        console.error('Error adding UTM params to markdown link:', error);
                    }
                }

                parts.push(
                    <a
                        key={`${pIndex}-${match.index}`}
                        href={linkUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {match[4]}
                    </a>
                );
            }

            lastIndex = match.index + match[0].length;
        }

        // Add remaining text
        if (lastIndex < paragraph.length) {
            parts.push(paragraph.substring(lastIndex));
        }

        return <p key={pIndex}>{parts}</p>;
    });
}

import { useMemo } from 'react';
import Fuse from 'fuse.js';
import type { BaseProduct } from '../types';
import type { FuseResultMatch } from 'fuse.js';

type ProductWithMatches<T extends BaseProduct> = T & {
    matches?: FuseResultMatch[];
};

interface UseProductSearchProps<T extends BaseProduct> {
    products: Record<string, T>;
    searchTerm: string;
}

type FuseIndices = [number, number];

export const useProductSearch = <T extends BaseProduct>({
    products,
    searchTerm
}: UseProductSearchProps<T>) => {
    const options = useMemo(() => ({
        keys: [
            { name: 'name', weight: 2 },
            { name: 'sections.description', weight: 1 },
            { name: 'categories', weight: 1 }
        ],
        threshold: 0.3,
        includeMatches: true,
        minMatchCharLength: 3
    }), []);

    const fuse = useMemo(
        () => new Fuse(Object.values(products), options),
        [products, options]
    );

    const results = useMemo(() => {
        if (!searchTerm) return products;

        return Object.fromEntries(
            fuse.search(searchTerm).map(({ item, matches }) => [
                item.plugin_file,
                { ...item, matches }
            ])
        ) as Record<string, ProductWithMatches<T>>;
    }, [searchTerm, products, fuse]);

    const highlight = (text: string, matches?: FuseResultMatch[], field?: string) => {
        if (!matches) return text;

        const fieldMatches = matches.filter((m: FuseResultMatch) => {
            if (field === 'description') {
                return m.key === 'sections.description';
            }
            return m.key === field;
        });

        if (!fieldMatches.length) return text;

        // Get all matches sorted by position
        const allIndices = fieldMatches
            .flatMap((match: FuseResultMatch) => match.indices)
            .sort((a: FuseIndices, b: FuseIndices) => a[0] - b[0]);

        // Apply highlights
        let result = text;
        let offset = 0;

        allIndices.forEach(([start, end]: FuseIndices) => {
            const matchText = result.slice(start + offset, end + offset + 1);
            const highlight = `<mark class="spellbook-app__search-highlight">${matchText}</mark>`;
            result = result.slice(0, start + offset) + highlight + result.slice(end + offset + 1);
            offset += highlight.length - matchText.length;
        });

        return result;
    };

    return { results, highlight };
};

import { ProductType } from '.';
import { FilterOption } from './filters';
import { __ } from '@wordpress/i18n';

export const getBaseFilterOptions = (type: ProductType | 'all'): FilterOption[] => [{
    id: 'all',
    label: type === 'perk' ? __('All Perks', 'spellbook')
         : type === 'connect' ? __('All Connections', 'spellbook')
         : type === 'shop' ? __('All Plugins', 'spellbook')
         : type === 'free' ? __('All Free Plugins', 'spellbook')
         : __('All Plugins', 'spellbook')
}];

export const SPECIAL_FILTER_OPTIONS: FilterOption[] = [
    { id: 'unregistered', label: __('Unregistered', 'spellbook') },
    { id: 'update-available', label: __('Update Available', 'spellbook') }
];

export const STATUS_FILTER_OPTIONS: FilterOption[] = [
    { id: 'active', label: __('Activated', 'spellbook') },
    { id: 'inactive', label: __('Installed', 'spellbook') },
    { id: 'not-installed', label: __('Not Installed', 'spellbook') }
];

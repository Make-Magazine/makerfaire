import { __ } from '@wordpress/i18n';
import Button from '@gravityforms/components/react/admin/elements/Button';
import { useRefreshAll } from '../../hooks/api/useRefreshAll';
import './EmptyProductsNotice.css';

const EmptyProductsNotice = () => {
	const refresh = useRefreshAll();

	return (
		<div className="empty-products-notice">
			<p className="empty-products-notice__message">
				{__('Unable to load products. Our server may be temporarily unavailable.', 'spellbook')}
			</p>
			<p className="empty-products-notice__support">
				{__('If this persists, please contact', 'spellbook')}{' '}
				<a
					href="https://gravitywiz.com/support/"
					target="_blank"
					rel="noopener noreferrer"
				>
					{__('Gravity Wiz Support', 'spellbook')}
				</a>.
			</p>
			<div className="empty-products-notice__actions">
				<Button
					type="white"
					size="size-height-m"
					onClick={() => refresh()}
				>
					{__('Try Again', 'spellbook')}
				</Button>
			</div>
		</div>
	);
};

export default EmptyProductsNotice;

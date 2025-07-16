import { __ } from '@wordpress/i18n';
import { Icon, warning } from '@wordpress/icons';
import Text from '@gravityforms/components/react/admin/elements/Text';
import type { LicenseData } from '../../types';
import './LicenseStatus.css';

const LicenseStatus = ({ license }: { license: LicenseData }) => {
	const { status, extend_url } = license;

	const statusText = (() => {
		switch (status) {
			case 'valid':
				return __('Active', 'spellbook');
			case 'item_name_mismatch':
				return __('License Mismatch', 'spellbook');
			case 'invalid':
				return __('Invalid', 'spellbook');
			case 'expired':
				return __('Expired', 'spellbook');
			case 'site_inactive':
				return __('Site Inactive', 'spellbook');
			default:
				return __('Invalid', 'spellbook');
		}
	})();

	const statusColor = status === 'valid' ? 'success' : status === 'site_inactive' ? 'warning' : 'error';

	if (status === 'expired') {
		return (
			<div className="license-status--expired">
				<Icon icon={warning} />
				<Text
					size="text-sm"
					weight="medium"
					color="error"
				>
					{statusText}
				</Text>
			</div>
		);
	}

	return (
		<Text
			size="text-sm"
			weight="medium"
			color={statusColor}
		>
			{statusText}
		</Text>
	);
};

export default LicenseStatus;

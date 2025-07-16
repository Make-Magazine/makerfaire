import Button from '@gravityforms/components/react/admin/elements/Button';
import { useLicenseMutations } from '../../../hooks/api/useLicenses';
import type { LicenseData, LicensedProductType } from '../../../types';
import { useCallback } from 'react';
import { __ } from '@wordpress/i18n';
import { addUtmParams } from '../../../helpers/urls';

interface LicenseActionsProps {
	type: LicensedProductType;

	license: LicenseData;
}

const LicenseActions = ({ type, license }: LicenseActionsProps) => {
	const { deactivate } = useLicenseMutations(type);

	const handleDeactivate = async () => {
		if (window.confirm(__('Are you sure you want to deactivate this license?', 'spellbook'))) {
			try {
				await deactivate.mutateAsync();
			} catch (err) {
				// Error is handled by the store
			}
		}
	};

	const handleUpgrade = useCallback(() => {
		const win = window.open(addUtmParams(license.upgrade_url, {
			component: 'license-actions',
			text: 'upgrade-license'
		}), '_blank');
		if (win) win.focus();
	}, [license.upgrade_url]);

	const handleManage = useCallback(() => {
		const win = window.open(addUtmParams(license.manage_url, {
			component: 'license-actions',
			text: 'manage-license'
		}), '_blank');
		if (win) win.focus();
	}, [license.manage_url]);

	return (
		<div className="license-box__actions">
			<Button
				type="white"
				label={deactivate.isPending ? __('Deactivating...', 'spellbook') : __('Deactivate', 'spellbook')}
				size="size-r"
				onClick={handleDeactivate}
				disabled={deactivate.isPending}
			/>
			<Button
				type="white"
				label={__('Manage', 'spellbook')}
				size="size-r"
				onClick={handleManage}
			/>
			{
				license.upgrade_url && (
					<Button
						type="primary-new"
						label={__('Upgrade', 'spellbook')}
						size="size-r"
						onClick={handleUpgrade}
					/>
				)
			}
		</div>
	);
};

export default LicenseActions;

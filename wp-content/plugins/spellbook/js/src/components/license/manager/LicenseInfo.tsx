import Text from '@gravityforms/components/react/admin/elements/Text';
import type { LicenseData, ProductType } from '../../../types';
import { format, addDays, isBefore } from 'date-fns';
import { __ } from '@wordpress/i18n';
import LicenseStatus from '../LicenseStatus';
import { addUtmParams } from '../../../helpers/urls';

interface LicenseInfoProps {
	license: LicenseData;
	type: ProductType;
}

const LicenseInfo = ({ license, type }: LicenseInfoProps) => {
	const renderPluginLimit = () => {
		if (type === 'perk') {
			return (
				<div className="license-box__stat">
					<Text size="text-sm" color="comet">Perks</Text>
					<Text size="text-sm" weight="medium">
						{Object.entries(license.registered_products).length}/
						{license.type.toLowerCase() === 'pro' ? '∞' : '1'}
					</Text>
				</div>
			);
		}

		if (type === 'connect') {
			return (
				<div className="license-box__stat">
					<Text size="text-sm" color="comet">Connections</Text>
					<Text size="text-sm" weight="medium">
						{Object.entries(license.registered_products).length}/
						{license.type.toLowerCase() === 'pro' ? '∞' : '1'}
					</Text>
				</div>
			);
		}

		return null;
	};

	return (
		<div className="license-box__details">
			<div className="license-box__stat">
				<Text size="text-sm" color="comet">{__('License Key', 'spellbook')}</Text>
				<Text size="text-sm" customClasses="license-box__key-value">
					{license.key ? (
						<>
							{license.key.slice(0, 2)}
							<span className="license-box__key-dots-long">{'•'.repeat(26)}</span>
							<span className="license-box__key-dots-medium">{'•'.repeat(16)}</span>
							<span className="license-box__key-dots-short">{'•'.repeat(8)}</span>
							{license.key.slice(-4)}
						</>
					) : __('No License Key', 'spellbook')}
				</Text>
			</div>
			<div className="license-box__stat">
				<Text size="text-sm" color="comet">{__('Status', 'spellbook')}</Text>
				<LicenseStatus license={license} />
				{
					license.status === 'expired' && (
						<a
							href={addUtmParams(license.extend_url, {
								component: 'license-info',
								text: 'renew-expired'
							})}
							target="_blank"
							style={{ fontWeight: '500', fontSize: '14px' }}
						>
							{__('Renew License', 'spellbook')}
						</a>
					)
				}
			</div>
			<div className="license-box__stat">
				<Text size="text-sm" color="comet">{__('Sites', 'spellbook')}</Text>
				<Text
					size="text-sm"
					weight="medium"
					color={license.site_count >= license.site_limit && license.site_limit > 0 ? 'warning' : undefined}
				>
					{license.site_limit === 0 ? `${license.site_count}/∞` : `${license.site_count}/${license.site_limit}`}
				</Text>
			</div>
			{renderPluginLimit()}
			<div className="license-box__stat">
				<Text size="text-sm" color="comet">
					{
						license.status === 'expired' ? __('Expired', 'spellbook') : __('Expires', 'spellbook')
					}
				</Text>
				<Text
					size="text-sm"
					weight="medium"
					color={license.expiration && license.expiration !== 'lifetime' && isBefore(new Date(license.expiration), addDays(new Date(), 30)) ? 'warning' : undefined}
				>
					{(() => {
						if (!license.expiration) return __('N/A', 'spellbook');
						if (license.expiration === 'lifetime') return __('Never', 'spellbook');

						const expirationDate = new Date(license.expiration);

						return format(expirationDate, 'MMM d, yyyy');
					})()}
				</Text>
			</div>
		</div>
	);
};

export default LicenseInfo;

import { useState } from 'react';
import Text from '@gravityforms/components/react/admin/elements/Text';
import Input from '@gravityforms/components/react/admin/elements/Input';
import Button from '@gravityforms/components/react/admin/elements/Button';
import { __ } from '@wordpress/i18n';
import { useLicenseMutations } from '../../../hooks/api/useLicenses';
import type { LicensedProductType } from '../../../types';
import { addUtmParams } from '../../../helpers/urls';

interface LicenseFormProps {
	type: LicensedProductType;
	description?: string;
	learnMoreUrl?: string;
	buyLicenseUrl?: string;
	isSimple?: boolean;
	shouldRedirect?: boolean;
}

const LicenseForm = ({ type, description, learnMoreUrl, buyLicenseUrl, isSimple, shouldRedirect = true }: LicenseFormProps) => {
	const [licenseKey, setLicenseKey] = useState('');
	const { validate } = useLicenseMutations(type);
	const isValidating = validate.isPending;
	const error = validate.error;

	const handleActivate = async () => {
		if (!licenseKey.trim()) {
			return;
		}

		try {
			await validate.mutateAsync(licenseKey);

			if (shouldRedirect) {
				window.location.hash = '/licenses';
			}
		} catch (err) {
			// Errors are handled by the store
		}
	};

	const renderInputs = () => (
		<div className="license-box__form-inputs">
			<div className="license-box__input-wrapper">
				<Input
					name="license_key"
					value={licenseKey}
					onChange={(value: string) => {
						setLicenseKey(value);
						validate.reset();
					}}
					placeholder={__('Enter License Key', 'spellbook')}
					size="size-r"
				/>
				{error && (
					<Text size="text-sm" customClasses="license-box__error-message">
						{error.message}
					</Text>
				)}
			</div>
			<Button
				type="primary-new"
				label={isValidating ? __('Validating...', 'spellbook') : __('Activate License', 'spellbook')}
				size="size-r"
				onClick={handleActivate}
				disabled={isValidating}
			/>
		</div>
	);


	return (
		<div className="license-box__empty">
			<div className="license-box__form">
				{renderInputs()}
				{
					buyLicenseUrl && !error && (
						<Text size="text-sm" color="comet">
							{__('Don\'t have a license?', 'spellbook')}&nbsp;
							<a
								href={addUtmParams(buyLicenseUrl, {
									component: 'license-form',
									text: 'buy-now'
								})}
								target="_blank"
								rel="noopener noreferrer" className="gform-link">{__('Get one now', 'spellbook')}</a>
						</Text>
					)
				}
			</div>
			{
				!isSimple && (
					<>
						<div className="license-box__separator"></div>
						<div className="license-box__info">
							<Text size="text-sm" color="comet">{description}</Text>
							{learnMoreUrl && (
								<a href={addUtmParams(learnMoreUrl, {
									component: 'license-form',
									text: 'learn-more'
								})} target="_blank" rel="noopener noreferrer" className="gform-link">{__('Learn more', 'spellbook')}</a>
							)}
						</div>
					</>
				)
			}
		</div>
	);
};

export default LicenseForm;

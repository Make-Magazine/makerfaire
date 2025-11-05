import { useState } from 'react';
import { useLicense, useLicenseMutations } from '../../hooks/api/useLicenses';
import './LicenseBarSuite.css';
import type { LicensedProductType } from '../../types';
import StarLarge from '../../svgs/star-large.svg';
import StarSmall from '../../svgs/star-small.svg';
import SuiteIcon from '../SuiteIcon';
import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';
import LicenseStatus from './LicenseStatus';
import { __ } from '@wordpress/i18n';
import { addUtmParams, getPricingUrl } from '../../helpers/urls';

interface LicenseManagerProps {
    type: LicensedProductType;
}

const LicenseBarSuite = ({ type }: LicenseManagerProps) => {
    const { data: license, isLoading } = useLicense(type);
    const { validate } = useLicenseMutations(type);
    const [licenseKey, setLicenseKey] = useState('');

    // Show loading state
    if (isLoading) {
        return <RingLoader />;
    }

    // Show form if no license data or no key
    if (!license?.key) {
        const handleActivate = (e: React.FormEvent) => {
            e.preventDefault();
            if (!licenseKey.trim()) return;
            validate.mutate(licenseKey, {
                onSuccess: () => {
                    window.location.hash = '/licenses';
                }
            });
        };

        return (
            <div className="license-bar-suite">
                <div className="spellbook-stars-background">
                    <div className="spellbook-star-large">
                        <StarLarge />
                    </div>
                    <div className="spellbook-star-small">
                        <StarSmall />
                    </div>
                </div>

                <div className="spellbook-license">
                    <form onSubmit={handleActivate} className="spellbook-form-container">
                        <input
                            type="text"
                            className={`spellbook-form-input ${validate.error ? 'spellbook-form-input-error' : ''}`}
                            value={licenseKey}
                            onChange={(e) => {
                                setLicenseKey(e.target.value);
                                validate.reset();
                            }}
                            placeholder="Enter license key"
                        />
                        <button
                            type="submit"
                            className="spellbook-form-button"
                            disabled={!licenseKey.trim() || validate.isPending}
                        >
                            {validate.isPending ? 'Activating...' : 'Activate license'}
                        </button>

                        {validate.error && (
                            <div className="spellbook-error-message">
                                {validate.error.message}
                            </div>
                        )}
                    </form>
                    <div className="spellbook-links">
                        <a href={addUtmParams(getPricingUrl(type), {
                            component: "license-bar-suite",
                            text: "buy-license"
                        })} target="_blank" rel="noopener noreferrer">
                            Buy License
                        </a>
                        <div className="spellbook-vertical-divider" />
                        <a href="#/licenses">View Licenses</a>
                    </div>
                </div>
            </div>
        );
    }

    const {
        upgrade_url: upgradeUrl,
        registered_products_limit: registeredProductsLimit,
        registered_products: registeredProducts,
        type: priceName,
    } = license;

    const suiteName = (() => {
        switch (type) {
            case 'perk':
                return 'Gravity Perks';
            case 'connect':
                return 'Gravity Connect';
            case 'shop':
                return 'Gravity Shop';
        }
    })();

    return (
        <div className="license-bar-suite license-bar-suite--activated">
            <div className={`suite-icon suite-icon--${type}`}>
                <SuiteIcon type={type} width={40} />
            </div>

            <div className="spellbook-title">
                {`${suiteName} ${priceName}`}
            </div>

            <div className="spellbook-usage">
                {type === 'perk' && registeredProducts && (
                    <>{`${Object.entries(registeredProducts).length}/${registeredProductsLimit == 0 ? '∞' : registeredProductsLimit} perks registered`}</>
                )}
                {type === 'connect' && registeredProducts && (
                    <>{`${Object.entries(registeredProducts).length}/${registeredProductsLimit == 0 ? '∞' : registeredProductsLimit} connections registered`}</>
                )}

				{license.status === 'expired' && (
                    <LicenseStatus license={license} />
                )}
            </div>

			<div className="license-bar-suite__right">
				{license.status === 'expired' && (
					<div className="spellbook-renew">
						<a href={addUtmParams(license.extend_url, {
                            component: "license-bar-suite",
                            text: "renew-license"
                        })} target="_blank">
							{__('Renew License', 'spellbook')}
						</a>
					</div>
				)}

				<div className="spellbook-upgrade">
					{upgradeUrl && (
						<a href={addUtmParams(upgradeUrl, {
                            component: "license-bar-suite",
                            text: "upgrade-license"
                        })} target="_blank">{__('Upgrade', 'spellbook')}</a>
					)}
				</div>

				<button
					type="button"
					className="spellbook-link-button"
					onClick={() => {
						window.location.hash = '/licenses';
					}}
				>
					{__('Manage License', 'spellbook')}
				</button>
			</div>


        </div>
    );
};

export default LicenseBarSuite;

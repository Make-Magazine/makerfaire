import { useState } from 'react';
import { useLicense, useValidateLicenseWithUnknownProductType } from '../../hooks/api/useLicenses';
import { useSpellbookRegistration } from '../../hooks/api/useSpellbook';
import { addUtmParams } from '../../helpers/urls';
import './LicenseBarAll.css';
import StarLarge from '../../svgs/star-large.svg';
import StarSmall from '../../svgs/star-small.svg';

const LicenseBarAll = () => {
    const { data: perkLicense, isLoading: perkLoading } = useLicense('perk');
    const { data: connectLicense, isLoading: connectLoading } = useLicense('connect');
    const { data: shopLicense, isLoading: shopLoading } = useLicense('shop');
    const { data: wizBundleLicense, isLoading: wizBundleLoading } = useLicense('wiz-bundle');
    const { register, isPending: isRegistering, registrationStatus, isLoading: registrationLoading } = useSpellbookRegistration();
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [licenseKey, setLicenseKey] = useState('');

    const { mutate: validateUnknownLicense, isPending: isValidating, error, reset } = useValidateLicenseWithUnknownProductType();

	// Show loading state
	if (perkLoading || connectLoading || shopLoading || wizBundleLoading || registrationLoading) {
		return null;
	}

	// If registered or has valid license, hide the license bar
	if (registrationStatus?.is_registered || perkLicense?.valid || connectLicense?.valid || shopLicense?.valid || wizBundleLicense?.valid) {
		return null;
	}

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!name || !email) return;
        await register({ name, email });
    };

    const handleLicense = (e: React.FormEvent) => {
        e.preventDefault();
        if (!licenseKey.trim()) return;
        validateUnknownLicense(licenseKey, {
            onSuccess: () => {
                window.location.hash = '/licenses';
            }
        });
    };

    return (
        <div className="license-bar-all">
            {/* Stars background */}
            <div className="spellbook-stars-background">
                <div className="spellbook-star-large">
                    <StarLarge />
                </div>
                <div className="spellbook-star-small">
                    <StarSmall />
                </div>
            </div>

            {/* License section */}
            <div className="spellbook-section">
                <h2 className="spellbook-section-title">Got a license?</h2>
                <form onSubmit={handleLicense} className="spellbook-form-container">
                    <div className="spellbook-input-group">
						<input
							type="text"
							className="spellbook-form-input"
							value={licenseKey}
							onChange={(e) => {
								setLicenseKey(e.target.value);
								reset();
							}}
							placeholder="Enter license key"
						/>
                        <button
                            type="submit"
                            className="spellbook-form-button"
                            disabled={isValidating}
                        >
                            {isValidating ? 'Activating...' : 'Activate license'}
                        </button>
                    </div>
                    <div className="spellbook-license-link">
                        {error ? (
                            <div className="spellbook-error-message">
                                {error.message}
                            </div>
                        ) : (
                            <>
                                <span>Don't have a license yet? </span>
                                <a href={addUtmParams("https://gravitywiz.com/", {
                                    component: "license-bar-all",
                                    text: "get-license"
                                })} target="_blank" rel="noopener noreferrer">
                                    Get one now
                                </a>
                            </>
                        )}
                    </div>
                </form>
            </div>

            {/* Vertical divider */}
            <div className="spellbook-vertical-divider" />

            {/* Registration section */}
            <div className="spellbook-section">
                <h2 className="spellbook-section-title">No license? Register for access to free plugins.</h2>
                <form onSubmit={handleRegister} className="spellbook-form-container">
					<div className="spellbook-input-group">
						<input
							type="text"
							className="spellbook-form-input"
							value={name}
							onChange={(e) => setName(e.target.value)}
							placeholder="Name"
						/>
						<input
							type="email"
							className="spellbook-form-input"
							value={email}
							onChange={(e) => setEmail(e.target.value)}
							placeholder="Email Address"
						/>
						<button
							type="submit"
							className="spellbook-form-button"
							disabled={isRegistering}
						>
							{isRegistering ? 'Registering...' : 'Get Access'}
						</button>
					</div>

                    <div className="spellbook-license-link">
                        <span>By submitting your email, you agree to our </span>
                        <a href={addUtmParams("https://gravitywiz.com/privacy-policy/", {
                            component: "license-bar-all",
                            text: "privacy-policy"
                        })} target="_blank" rel="noopener noreferrer">
                            Privacy Policy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default LicenseBarAll;

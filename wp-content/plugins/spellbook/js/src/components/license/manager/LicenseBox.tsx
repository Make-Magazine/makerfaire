import Box from '@gravityforms/components/react/admin/elements/Box';
import Text from '@gravityforms/components/react/admin/elements/Text';
import MetaBox from '@gravityforms/components/react/admin/modules/MetaBox';
import { __ } from '@wordpress/i18n';
import { useLicense, useLicenseMutations } from '../../../hooks/api/useLicenses';
import LicenseInfo from './LicenseInfo';
import LicenseActions from './LicenseActions';
import LicenseForm from './LicenseForm';
import './LicenseBox.css';
import SuiteIcon from '../../SuiteIcon';
import { LicensedProductType, ProductType } from '../../../types';
import RingLoader from '@gravityforms/components/react/admin/modules/Loaders/RingLoader';

interface LicenseBoxProps {
    type: LicensedProductType;
    title: string;
    description: string;
    learnMoreUrl: string;
	buyLicenseUrl: string;
}

const Header = ({ title, licenseType }: { title: string; licenseType?: string }) => (
    <Text size="text-lg" weight="medium">
        {licenseType ? `${title} ${licenseType}` : title}
    </Text>
);

const LicenseBox = ({ type, title, description, learnMoreUrl, buyLicenseUrl }: LicenseBoxProps) => {
    const { data: license, isLoading, error } = useLicense(type);
	const { validate } = useLicenseMutations(type);

    // Handle error state
    if (error) {
        return (
            <MetaBox HeaderContent={() => <Header title={title} />} customClasses="license-box--error">
                <Box>
                    <div className="license-box__content">
                        <SuiteIcon type={type} />
                        <div className="license-box__error">
                            <Text color="error">{__('Failed to load license info.', 'spellbook')}</Text>
                        </div>
                    </div>
                </Box>
            </MetaBox>
        );
    }

    // Handle loading state
    if (isLoading || validate.isPending) {
        return (
            <MetaBox HeaderContent={() => <Header title={title} />} customClasses="license-box--loading">
                <Box>
                    <div className="license-box__content">
                        <SuiteIcon type={type} />
                        <div className="license-box__loading" style={{ display: 'flex', alignItems: 'center', paddingTop: '20px', justifyContent: 'center' }}>
                            <RingLoader foreground="#aaa" />
                        </div>
                    </div>
                </Box>
            </MetaBox>
        );
    }

    // Detect corrupted license data (has key but null/missing critical fields)
    // Still show the key + deactivate so users can take action
    if (license && license.key && (license.site_count === null || license.site_count === undefined || !license.status)) {
        return (
            <MetaBox HeaderContent={() => <Header title={title} />} customClasses="license-box--activated">
                <Box>
                    <div className="license-box__content">
                        <SuiteIcon type={type} />
                        <div className="license-box__details">
                            <div className="license-box__stat">
                                <Text size="text-sm" color="comet">{__('License Key', 'spellbook')}</Text>
                                <Text size="text-sm" customClasses="license-box__key-value">
                                    {license.key.slice(0, 2)}
                                    <span className="license-box__key-dots-long">{'•'.repeat(26)}</span>
                                    <span className="license-box__key-dots-medium">{'•'.repeat(16)}</span>
                                    <span className="license-box__key-dots-short">{'•'.repeat(8)}</span>
                                    {license.key.slice(-4)}
                                </Text>
                            </div>
                            <div className="license-box__stat">
                                <Text size="text-sm" color="comet">{__('Status', 'spellbook')}</Text>
                                <Text size="text-sm" weight="medium" color="warning">
                                    {__('Unable to Verify', 'spellbook')}
                                </Text>
                            </div>
                        </div>
                        <div className="license-box__unavailable-notice">
                            <Text size="text-sm" color="comet">
                                {__('Our server may be temporarily unavailable. Try clicking Refresh Licenses above, or deactivate and reactivate your license. If this persists, ', 'spellbook')}
                                <a href="https://gravitywiz.com/support/" target="_blank" rel="noopener noreferrer" className="license-box__support-link">
                                    {__('contact support', 'spellbook')}
                                </a>.
                            </Text>
                        </div>
                        <LicenseActions type={type} license={license} />
                    </div>
                </Box>
            </MetaBox>
        );
    }

    // Show license info if we have data and a valid key
    if (license && license.key) {
        return (
            <MetaBox HeaderContent={() => <Header title={title} licenseType={license.type} />} customClasses="license-box--activated">
                <Box>
                    <div className="license-box__content">
                        <SuiteIcon type={type} />
                        <LicenseInfo license={license} type={type} />
                        <LicenseActions type={type} license={license} />
                    </div>
                </Box>
            </MetaBox>
        );
    }

    // Show license form if no license
    return (
        <MetaBox HeaderContent={() => <Header title={title} />} customClasses="license-box--empty">
            <Box>
                <div className="license-box__content">
                    <SuiteIcon type={type} />
                    <LicenseForm
                        type={type}
                        description={description}
                        learnMoreUrl={learnMoreUrl}
						buyLicenseUrl={buyLicenseUrl}
                        shouldRedirect
                    />
                </div>
            </Box>
        </MetaBox>
    );
};

export default LicenseBox;

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

import Heading from '@gravityforms/components/react/admin/elements/Heading';
import Text from '@gravityforms/components/react/admin/elements/Text';
import Button from '@gravityforms/components/react/admin/elements/Button';
import LicenseBox from '../components/license/manager/LicenseBox';
import './Licenses.css';
import { useRefreshAll } from '../hooks/api/useRefreshAll';

const LicensesPage = () => {
    const refresh = useRefreshAll();

    const handleRefresh = () => {
        refresh();
    };

    return (
        <div className="licenses-page">
            <div className="licenses-page__header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '24px' }}>
                <Heading size="display-sm" weight="semibold">
                    Licenses
                </Heading>
                <Button type="white" onClick={handleRefresh}>
                    Refresh Licenses
                </Button>
            </div>
            <div className="licenses-page__grid">
                <LicenseBox
                    type="perk"
                    title="Gravity Perks"
                    description="Enhance Gravity Forms with powerful features and customization options."
                    learnMoreUrl="https://gravitywiz.com/gravity-perks"
					buyLicenseUrl="https://gravitywiz.com/gravity-perks/pricing"
                />
                <LicenseBox
                    type="connect"
                    title="Gravity Connect"
                    description="Integrate your forms with third-party services and applications."
                    learnMoreUrl="https://gravitywiz.com/gravity-connect"
					buyLicenseUrl="https://gravitywiz.com/gravity-connect/pricing"
                />
                <LicenseBox
                    type="shop"
                    title="Gravity Shop"
                    description="Power up your WooCommerce store with advanced form integrations."
                    learnMoreUrl="https://gravitywiz.com/gravity-shop"
					buyLicenseUrl="https://gravitywiz.com/gravity-shop/pricing"
                />
            </div>
        </div>
    );
};

export default LicensesPage;

import PerksIcon from '../svgs/perks-color.svg';
import ConnectIcon from '../svgs/connect-color.svg';
import ShopIcon from '../svgs/shop-color.svg';
import WizBundle from '../svgs/wiz-bundle-color.svg';
import { LicensedProductType } from '../types';

const SuiteIcon = ({ type, width = 74 }: { type: LicensedProductType, width?: number }) => {
	let Icon;

	switch (type) {
		case 'perk':
			Icon = PerksIcon;
			break;
		case 'connect':
			Icon = ConnectIcon;
			break;
		case 'shop':
			Icon = ShopIcon;
			break;
		case 'wiz-bundle':
			Icon = WizBundle;
			break;
		default:
			Icon = PerksIcon;
	}

	return (
		<div className="license-box__icon">
			<Icon width={width} height="auto" />
		</div>
	);
};

export default SuiteIcon;

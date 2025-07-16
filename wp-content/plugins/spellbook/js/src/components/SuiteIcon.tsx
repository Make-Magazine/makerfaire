import PerksIcon from '../svgs/perks-color.svg';
import ConnectIcon from '../svgs/connect-color.svg';
import ShopIcon from '../svgs/shop-color.svg';
import { ProductType } from '../types';

const SuiteIcon = ({ type, width = 74 }: { type: ProductType, width?: number }) => {
	const Icon = type === 'perk' ? PerksIcon : type === 'connect' ? ConnectIcon : ShopIcon;
	return (
		<div className="license-box__icon">
			<Icon width={width} height="auto" />
		</div>
	);
};

export default SuiteIcon;

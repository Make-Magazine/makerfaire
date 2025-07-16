import Box from '@gravityforms/components/react/admin/elements/Box';
import Heading from '@gravityforms/components/react/admin/elements/Heading';
import Text from '@gravityforms/components/react/admin/elements/Text';
import LicenseBarAll from './license/LicenseBarAll';
import type { ProductType } from '../types';

interface PageHeaderProps {
    title: string;
    description: string;
    type: ProductType;
}

const PageHeader = ({ title, description, type }: PageHeaderProps) => {
    return (
        <Box spacing={4}>
            <Heading size="display-sm" weight="semibold" spacing={1} content={title} />
            <Text size="text-sm" color="comet" spacing={6}>
                {description}
            </Text>
        </Box>
    );
};

export default PageHeader;

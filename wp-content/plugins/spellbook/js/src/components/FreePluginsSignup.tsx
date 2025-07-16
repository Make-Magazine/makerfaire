import { useState } from 'react';
import Button from '@gravityforms/components/react/admin/elements/Button';
import Input from '@gravityforms/components/react/admin/elements/Input';
import Text from '@gravityforms/components/react/admin/elements/Text';
import { useUserManagement } from '../hooks/features/useUserManagement';

const FreePluginsSignup = () => {
    const { registerUser } = useUserManagement();
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (!name || !email) return;
        await registerUser.mutateAsync({ name, email });
    };

    return (
        <>
            <Text size="text-md" weight="medium" customClasses={['license-manager__form-title']}>
                Get Access to Free Plugins
            </Text>
            <form onSubmit={handleSubmit} className="license-manager__form license-manager__form--vertical">
                <Input
                    name="name"
                    value={name}
                    onChange={(value: string) => setName(value)}
                    placeholder="Your Name"
                    size="size-r"
                />
                <Input
                    name="email"
                    type="email"
                    value={email}
                    onChange={(value: string) => setEmail(value)}
                    placeholder="Your Email"
                    size="size-r"
                />
                <Button
                    customClasses={['user-form__submit']}
                    label="Get Access"
                    type="primary-new"
                    size="size-r"
                    icon="arrow-right"
                    iconPosition="trailing"
                />
            </form>
        </>
    );
};

export default FreePluginsSignup;

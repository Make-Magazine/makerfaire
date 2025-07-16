import React from 'react';
import { __ } from '@wordpress/i18n';
import Button from '@gravityforms/components/react/admin/elements/Button';
import './ErrorBoundary.css';

interface Props {
    children?: React.ReactNode;
}

interface State {
    hasError: boolean;
    error?: Error;
}

export class ErrorBoundary extends React.Component<Props, State> {
    public state: State = {
        hasError: false
    };

    static getDerivedStateFromError(error: Error): State {
        return { hasError: true, error };
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        console.error('Spellbook encountered an error:', error);
        console.error('Component stack:', errorInfo.componentStack);
    }

    render() {
        if (this.state.hasError) {
            return <ErrorFallback error={this.state.error} />;
        }

        return this.props.children;
    }
}

interface ErrorFallbackProps {
    error?: Error;
}

const ErrorFallback = ({ error }: ErrorFallbackProps) => {
    return (
        <div className="error-boundary">
            <h2 className="error-boundary__title">
                {__('Oops! Something went wrong.', 'spellbook')}
            </h2>
            <p className="error-boundary__message">
                {__('We\'ve encountered an unexpected error. Please try refreshing the page.', 'spellbook')}
            </p>
            {error && (
                <div className="error-boundary__details">
                    <p className="error-boundary__error-name">{error.name}</p>
                    <p className="error-boundary__error-message">{error.message}</p>
                    {error.stack && (
                        <pre className="error-boundary__stack-trace">
                            {error.stack}
                        </pre>
                    )}
                </div>
            )}
            <p className="error-boundary__support">
                {__('If this error persists, please contact', 'spellbook')}{' '}
                <a
                    href="https://gravitywiz.com/support"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="error-boundary__support-link"
                >
                    {__('Gravity Wiz Support', 'spellbook')}
                </a>
                {__(' for assistance.', 'spellbook')}
            </p>
            <div className="error-boundary__actions">
                <Button
                    onClick={() => window.location.reload()}
                    variant="primary"
                    icon="update"
                >
                    {__('Refresh Page', 'spellbook')}
                </Button>
            </div>
        </div>
    );
};

export default ErrorBoundary;

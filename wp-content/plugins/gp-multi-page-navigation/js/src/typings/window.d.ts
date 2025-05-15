
export {};

declare global {
    interface Window {
        GPMultiPageNavigation: any;
        [key: `gpmpn_${number}`]: any;
        [key: `GPPageTransitions_${number}`]: {
            enableSoftValidation: boolean;
            validate: () => boolean;
            swiper: any;
            currentPage: number;
            sourcePage: number;
            updateProgressIndicator: (page: number) => void;
        };
        gformInitSpinner: (formId: number, spinnerUrl?: string) => void;
        gformOrigInitSpinner: (formId: number, spinnerUrl?: string) => void;
        gf_global: {
            spinnerUrl: string;
            [key: string]: any;
        };
    }
}

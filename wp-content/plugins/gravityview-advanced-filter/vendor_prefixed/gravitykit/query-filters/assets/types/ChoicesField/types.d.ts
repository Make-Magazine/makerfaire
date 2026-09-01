/**
 * ChoicesField types.
 *
 * @since 2.12.0
 */
/**
 * A single choice — a value/label pair, optionally disabled.
 *
 * @since 2.12.0
 * @since 2.16.0 Added the optional `count`.
 */
export interface Choice {
    value: string;
    label: string;
    disabled?: boolean;
    count?: number;
}
/**
 * Grouping wrapper for `ChoiceItem`s. Groups can nest recursively.
 *
 * @since 2.12.0
 */
export interface ChoiceGroup {
    label: string;
    choices: ChoiceItem[];
}
/**
 * Anything that can appear in a choice list — a leaf `Choice` or a `ChoiceGroup`.
 *
 * @since 2.12.0
 */
export type ChoiceItem = Choice | ChoiceGroup;
/**
 * Combobox mode. Mirrors bits-ui's Combobox `type` prop.
 *
 * @since 2.12.0
 */
export type ChoicesType = 'single' | 'multiple';
/**
 * Value shape per mode — string for single, string[] for multiple.
 *
 * @since 2.12.0
 */
export type ChoicesValue<T extends ChoicesType> = T extends 'multiple' ? string[] : string;
/**
 * Request payload sent to a `ChoicesEndpoint`.
 *
 * @since 2.12.0
 */
export interface ChoicesEndpointRequest {
    /** Current search query (the text typed in the input). */
    query: string;
    /** Values whose labels the host should resolve — used to hydrate chip labels after a page reload. */
    resolveValues?: string[];
    /**
     * Number of already-loaded results to skip; set when paginating to the next page.
     *
     * @since 2.14.0
     */
    offset?: number;
    /** Abort signal for cancelling in-flight requests when the query changes. */
    signal: AbortSignal;
}
/**
 * Response shape an endpoint must return. Both arrays are merged into the component's internal
 * choice map so a chip never renders as a bare value when the host knows the label.
 *
 * @since 2.12.0
 */
export interface ChoicesEndpointResponse {
    /** Matches for the search query — may include nested groups. */
    choices: ChoiceItem[];
    /** Resolved {value,label} pairs for the `resolveValues` requested in the request. Always flat. */
    resolved?: Choice[];
    /** Whether more results exist beyond what was returned; drives the load-more pagination. */
    hasMore?: boolean;
}
/**
 * Endpoint callback signature.
 *
 * @since 2.12.0
 */
export type ChoicesEndpoint = (request: ChoicesEndpointRequest) => Promise<ChoicesEndpointResponse>;
/**
 * Value emitted by `onChange`. Value shape is narrowed by `type`.
 *
 * @since 2.12.0
 */
export interface ChoicesOutput<T extends ChoicesType = ChoicesType> {
    type: T;
    value: ChoicesValue<T>;
    choices: Choice[];
    query?: string;
}
/**
 * Shared props for every `ChoicesField` mode.
 *
 * @since 2.12.0
 */
export interface ChoicesFieldBaseProps {
    choices?: ChoiceItem[];
    endpoint?: ChoicesEndpoint;
    label?: string;
    /**
     * Id of an external element that labels the field, referenced from the root group's
     * `aria-labelledby`. Composes with the internal label when both are present.
     *
     * @since 2.16.0
     */
    labelledBy?: string;
    placeholder?: string;
    translations?: Record<string, string>;
    inputElementName?: string;
    hideClear?: boolean;
    disabled?: boolean;
    dev?: boolean;
    debounceMs?: number;
    /**
     * Distance from the viewport bottom, in pixels, at which scrolling fetches the next page.
     *
     * @since 2.14.0
     */
    loadMoreThresholdPx?: number;
    /**
     * How many items from the end keyboard navigation prefetches the next endpoint page.
     *
     * @since 2.14.0
     */
    prefetchLookahead?: number;
    /**
     * Fires whenever the internal label cache grows.
     *
     * @since 2.14.0
     */
    onLabelsResolved?: (choices: Choice[]) => void;
    /**
     * Fires after the endpoint attempts to resolve selected values on mount, reporting the subset it
     * returned no match for.
     *
     * @since 2.14.0
     */
    onUnresolvedValues?: (values: string[]) => void;
    /**
     * Offer the trimmed search query as a selectable custom value when it matches no known
     * option or current selection. In single mode it becomes the value; in multiple mode it is
     * added as a new chip.
     *
     * @since 2.14.0
     */
    allowCustomValue?: boolean;
    /**
     * A pinned option rendered at the bottom of the list, in single mode only, always visible
     * regardless of the search query. Selecting it emits `value` through the normal single
     * `onChange` path.
     *
     * @since 2.14.0
     */
    customValueOption?: {
        value: string;
        label: string;
    };
}
/**
 * Props for a single-select `ChoicesField`.
 *
 * @since 2.12.0
 */
export interface ChoicesFieldSingleProps extends ChoicesFieldBaseProps {
    type?: 'single';
    value?: string;
    onChange?: (output: ChoicesOutput<'single'>) => void;
}
/**
 * Props for a multi-select `ChoicesField`.
 *
 * @since 2.12.0
 */
export interface ChoicesFieldMultipleProps extends ChoicesFieldBaseProps {
    type: 'multiple';
    value?: string[];
    onChange?: (output: ChoicesOutput<'multiple'>) => void;
}
/**
 * Discriminated union of all `ChoicesField` prop shapes.
 *
 * @since 2.12.0
 */
export type ChoicesFieldProps = ChoicesFieldSingleProps | ChoicesFieldMultipleProps;
/**
 * Options accepted by every programmatic mutator on a ChoicesField instance.
 *
 * When `silent` is true the field mutates its internal state but does NOT invoke its `onChange`
 * callback — useful for host-driven updates (popstate, controlled forms) where the host already
 * owns the source of truth.
 *
 * @since 2.12.0
 */
export interface ChoicesUpdateOptions {
    silent?: boolean;
}
/**
 * Public instance API exposed by `ChoicesField`. Value shape is narrowed by `type`.
 *
 * @since 2.12.0
 */
export interface ChoicesFieldInstance<T extends ChoicesType = ChoicesType> {
    updateValue: (value: ChoicesValue<T>, options?: ChoicesUpdateOptions) => void;
    addChoice: (choice: Choice, options?: ChoicesUpdateOptions) => void;
    removeValue: (value: string, options?: ChoicesUpdateOptions) => void;
    clear: (options?: ChoicesUpdateOptions) => void;
    reset: (options?: ChoicesUpdateOptions) => void;
    open: () => void;
    close: () => void;
    isVisible: () => boolean;
    undo: () => boolean;
    redo: () => boolean;
    canUndo: () => boolean;
    canRedo: () => boolean;
    setSingle: () => void;
    setMultiple: () => void;
    getLabel: (value: string) => string | undefined;
}

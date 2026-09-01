/**
 * Date segment part from bits-ui DatePicker.
 *
 * @since 2.12.0
 */
export type SegmentPart = "month" | "day" | "year" | "hour" | "minute" | "second" | "dayPeriod" | "timeZoneName" | "literal";
/**
 * Date segment from bits-ui DatePicker / DateRangePicker.
 *
 * @since 2.9.0
 */
export interface DateSegment {
    part: SegmentPart;
    value: string;
}
/**
 * Base date format order.
 *
 * @since 2.9.0
 */
export type BaseDateFormat = 'mdy' | 'dmy' | 'ymd';
/**
 * Optional separator suffix.
 *
 * @since 2.9.0
 */
export type SeparatorSuffix = '' | '_dash' | '_dot';
/**
 * Gravity Forms date format.
 * Combines base format (mdy/dmy/ymd) with optional separator (_dash/_dot).
 *
 * @since 2.9.0
 */
export type DateFormat = `${BaseDateFormat}${SeparatorSuffix}`;
/**
 * Separator type.
 *
 * @since 2.9.0
 */
export type SeparatorType = 'dash' | 'dot' | undefined;
/**
 * Order mapping from format character to date part.
 *
 * @since 2.9.0
 */
export type OrderMap = {
    [key: string]: 'day' | 'month' | 'year';
};
/**
 * Single date value used by the DatePicker component as input.
 *
 * @since 2.11.0
 */
export interface SingleDate {
    date?: string;
}
/**
 * Single date value emitted by the DatePicker's onChange callback.
 *
 * Extends SingleDate with the format code and a canonical ISO-8601 mirror so consumers always have
 * a parseable YYYY-MM-DD representation available regardless of the active dateFormat.
 *
 * @since 2.11.0
 */
export interface SingleDateOutput extends SingleDate {
    outputFormat: DateFormat;
    dateIso: string | null;
}
/**
 * Base props shared by DatePicker and DateRangePicker.
 *
 * @since 2.11.0
 */
export interface BaseDatePickerProps {
    label?: string;
    translations?: Record<string, string>;
    dev?: boolean;
    dateFormat?: DateFormat;
    outputFormat?: DateFormat;
    inputElementName?: string;
    hideReset?: boolean;
    locale?: string;
    showTodayButton?: boolean;
    minDate?: string | null;
    maxDate?: string | null;
}
/**
 * Options accepted by every programmatic mutator on a picker instance.
 *
 * When `silent` is true the picker mutates its internal state but does NOT invoke its
 * `onChange` callback. Use this for host-driven updates (popstate, controlled forms,
 * test setup) where the host already owns the source of truth and doesn't want the
 * picker firing an event back at it.
 *
 * @since 2.12.0
 */
export interface PickerUpdateOptions {
    silent?: boolean;
}
/**
 * Public methods shared by every picker component (DatePicker, DateRangePicker, CalendarPopover).
 *
 * Update methods are component-specific (different value shapes), so each component's instance
 * interface extends this with its own `update*()` signature.
 *
 * @since 2.12.0
 */
export interface BasePickerInstance {
    /**
     * Revert the picker to the value it was rendered with.
     *
     * @param options Optional mutation options.
     */
    reset: (options?: PickerUpdateOptions) => void;
    /**
     * Clear all selected values.
     *
     * @param options Optional mutation options.
     */
    clear: (options?: PickerUpdateOptions) => void;
    /**
     * Open the picker's calendar popover programmatically.
     */
    open: () => void;
    /**
     * Close the picker's calendar popover programmatically.
     */
    close: () => void;
    /**
     * Whether the calendar popover is currently visible.
     */
    isVisible: () => boolean;
}

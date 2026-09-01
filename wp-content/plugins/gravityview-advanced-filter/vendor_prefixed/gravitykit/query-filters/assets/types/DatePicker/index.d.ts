import { SingleDate, BaseDatePickerProps, BasePickerInstance, PickerUpdateOptions, SingleDateOutput } from './types';
export type { SingleDate, SingleDateOutput, BaseDatePickerProps, BasePickerInstance, PickerUpdateOptions, DateFormat, BaseDateFormat, SeparatorSuffix, SeparatorType, OrderMap, DateSegment } from './types';
/**
 * DatePicker component props.
 *
 * @since 2.11.0
 */
export interface DatePickerProps extends BaseDatePickerProps {
    value?: SingleDate;
    onChange?: (value: SingleDateOutput) => void;
}
/**
 * DatePicker component instance with public methods.
 *
 * @since 2.11.0
 */
export interface DatePickerInstance extends BasePickerInstance {
    updateDate: (value: SingleDate, options?: PickerUpdateOptions) => void;
}
/**
 * CalendarPopover component props — lightweight calendar-only companion picker designed to sit
 * next to a consumer-owned <input type="text">.
 *
 * @since 2.11.0
 */
export interface CalendarPopoverProps extends Omit<BaseDatePickerProps, 'dev' | 'inputElementName' | 'hideReset' | 'label'> {
    value?: string;
    onSelect?: (value: SingleDateOutput) => void;
    class?: string;
    /**
     * Render a reset button that clears the picker's selected date.
     *
     * @since 2.16.0
     */
    showReset?: boolean;
    /**
     * Called after the reset button clears the picker's selected date.
     *
     * @since 2.16.0
     */
    onClear?: () => void;
}
/**
 * CalendarPopover instance with public methods.
 *
 * @since 2.11.0
 */
export interface CalendarPopoverInstance extends BasePickerInstance {
    updateDate: (value: string, options?: PickerUpdateOptions) => void;
    /**
     * Open the popover with optional focus suppression. Extends the base `open()` so consumers can
     * keep focus on a companion input instead of moving it into the calendar grid.
     */
    open: (options?: {
        focus?: boolean;
        focusElement?: HTMLElement | null;
    }) => void;
}

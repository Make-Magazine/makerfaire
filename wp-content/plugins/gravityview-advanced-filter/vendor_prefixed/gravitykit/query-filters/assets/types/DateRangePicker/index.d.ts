import { DateRange, DateRangePreset, DateRangeOutput } from './types';
import { BaseDatePickerProps, BasePickerInstance, PickerUpdateOptions } from '../DatePicker/types';
export type { DateRange, DateRangeOutput, DateRangePreset } from './types';
export type { DateFormat, BaseDateFormat, SeparatorSuffix, SeparatorType, OrderMap, DateSegment } from '../DatePicker/types';
/**
 * DateRangePicker component props.
 *
 * @since 2.9.0
 */
export interface DateRangePickerProps extends Omit<BaseDatePickerProps, 'inputElementName'> {
    value?: DateRange;
    presets?: Array<DateRangePreset>;
    onChange?: (range: DateRangeOutput) => void;
    /**
     * Name(s) used for the hidden form inputs that mirror the selected range. Accepts either a
     * bracket-notation base (e.g. `filter` → `filter[start]` / `filter[end]`) or an object with
     * explicit `start` / `end` names.
     *
     * @since 2.11.0
     */
    inputElementName?: string | {
        start?: string;
        end?: string;
    };
}
/**
 * DateRangePicker component instance with public methods.
 *
 * @since 2.9.0
 */
export interface DateRangePickerInstance extends BasePickerInstance {
    updateRange: (range: DateRange, options?: PickerUpdateOptions) => void;
}

import { DateFormat } from '../DatePicker/types';
/**
 * Date range with start and end dates (input shape).
 *
 * @since 2.9.0
 */
export interface DateRange {
    start?: string;
    end?: string;
}
/**
 * Date range emitted by the DateRangePicker's onChange callback.
 *
 * Extends DateRange with the format code and canonical ISO-8601 mirrors of each endpoint so
 * consumers always have a parseable YYYY-MM-DD representation available regardless of the
 * active dateFormat.
 *
 * @since 2.11.0
 */
export interface DateRangeOutput extends DateRange {
    outputFormat: DateFormat;
    startIso: string | null;
    endIso: string | null;
}
/**
 * Date range preset.
 *
 * @since 2.9.0
 */
export interface DateRangePreset {
    label: string;
    range: DateRange;
}

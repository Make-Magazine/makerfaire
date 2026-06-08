/**
 * Base date format order.
 *
 * @since 2.9.0
 */
export declare type BaseDateFormat = 'mdy' | 'dmy' | 'ymd';

/**
 * Gravity Forms date format.
 * Combines base format (mdy/dmy/ymd) with optional separator (_dash/_dot).
 *
 * @since 2.9.0
 */
export declare type DateFormat = `${BaseDateFormat}${SeparatorSuffix}`;

/**
 * Date range with start and end dates.
 *
 * @since 2.9.0
 */
export declare interface DateRange {
    start?: string;
    end?: string;
}

/**
 * DateRangePicker component instance with public methods.
 *
 * @since 2.9.0
 */
export declare interface DateRangePickerInstance {
    updateRange: (range: DateRange) => void;
}

/**
 * DateRangePicker component props.
 *
 * @since 2.9.0
 */
export declare interface DateRangePickerProps {
    label?: string;
    translations?: Record<string, string>;
    dev?: boolean;
    value?: DateRange;
    presets?: Array<DateRangePreset>;
    dateFormat?: string;
    onChange?: (range: DateRange) => void;
    inputElementName?: string;
}

/**
 * Date range preset.
 *
 * @since 2.9.0
 */
export declare interface DateRangePreset {
    label: string;
    range: DateRange;
}

/**
 * Date segment from bits-ui DateRangePicker.
 *
 * @since 2.9.0
 */
export declare interface DateSegment {
    part: 'day' | 'month' | 'year' | 'literal';
    value: string;
}

/**
 * Order mapping from format character to date part.
 *
 * @since 2.9.0
 */
export declare type OrderMap = {
    [key: string]: 'day' | 'month' | 'year';
};

/**
 * Optional separator suffix.
 *
 * @since 2.9.0
 */
export declare type SeparatorSuffix = '' | '_dash' | '_dot';

/**
 * Separator type.
 *
 * @since 2.9.0
 */
export declare type SeparatorType = 'dash' | 'dot' | undefined;

export { }

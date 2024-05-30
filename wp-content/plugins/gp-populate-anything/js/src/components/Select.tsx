import { useState, useEffect, useRef, useId } from 'react';
import classnames from 'classnames';
const { strings } = window.GPPA_ADMIN;
import useDeepCompareEffect from 'use-deep-compare-effect';

const Select = ({
	value,
	className,
	objectTypeInstance,
	flattenedProperties,
	loading,
	injectCustomValueOption,
	forceCustomInput,
	children,
	onChange,
	disabled,
}: {
	value: number | string;
	className?: string;
	objectTypeInstance: GPPAObjectType;
	flattenedProperties: { [property: string]: GPPAProperty };
	loading?: boolean;
	injectCustomValueOption?: boolean;
	forceCustomInput?: boolean;
	children: React.ReactNode;
	onChange: (value: string) => void;
	disabled?: boolean;
}) => {
	const _uid = useId();
	const [showCustomInput, setShowCustomInput] = useState(false);
	const customInputRef = useRef<HTMLInputElement>(null);
	const mergeTagsObjRef = useRef<any>(null);
	const inputValue =
		typeof value === 'undefined'
			? ''
			: value.toString().replace(/^gf_custom:?/, '');

	// Toggle showing the custom input if the value is a custom value
	useEffect(() => {
		if (
			(value && value.toString().indexOf('gf_custom') === 0) ||
			forceCustomInput
		) {
			setShowCustomInput(true);
		} else {
			setShowCustomInput(false);
		}
	}, [value, forceCustomInput]);

	// Create merge tag selector
	useDeepCompareEffect(() => {
		if (!customInputRef.current || mergeTagsObjRef?.current) {
			return;
		}

		const getMergeTags = (
			fields: any,
			elementId: any,
			hideAllFields: boolean,
			excludeFieldTypes: string[],
			isPrepop: boolean,
			option: any
		) => {
			const mergeTags: {
				[key: string]: {
					label: string;
					tags: {
						tag: string;
						label: string;
					}[];
				};
			} = {
				gppaProperties: {
					label: 'Properties',
					tags: [],
				},
			};

			for (const [groupId, group] of Object.entries(
				objectTypeInstance.groups
			)) {
				mergeTags[groupId] = {
					label: group.label,
					tags: [],
				};
			}

			for (const property of Object.values(flattenedProperties)) {
				mergeTags[property.group || 'gppaProperties'].tags.push({
					tag:
						'{' +
						objectTypeInstance.id +
						':' +
						property.value +
						'}',
					label: property.label,
				});
			}

			return window.gform.applyFilters(
				'gppa_template_merge_tags',
				mergeTags,
				elementId,
				hideAllFields,
				excludeFieldTypes,
				isPrepop,
				option
			);
		};

		mergeTagsObjRef.current = new window.gfMergeTagsObj(
			window.form,
			jQuery(customInputRef.current)
		);
		mergeTagsObjRef.current.getMergeTags = getMergeTags;
		mergeTagsObjRef.current.getTargetElement = function() {
			return jQuery(customInputRef.current!);
		};

		/* GF Merge Tag selector doesn't trigger change by default, so we need to shim that in. */
		jQuery(customInputRef.current).on('propertychange', (event) => {
			onChange('gf_custom:' + event.target.value);
		});

		return () => {
			mergeTagsObjRef?.current.destroy();
			mergeTagsObjRef.current = null;
		};
	}, [customInputRef.current, flattenedProperties]);

	return (
		<div
			className={classnames(
				'gppa-select-with-custom',
				{
					'gppa-show-custom-input': showCustomInput,
					'gppa-no-reset': forceCustomInput,
					'gppa-has-merge-tag-selector': !!mergeTagsObjRef?.current,
				},
				className
			)}
		>
			{loading && !showCustomInput && (
				<select disabled defaultValue="">
					<option value="" disabled hidden>
						{strings.loadingEllipsis}
					</option>
				</select>
			)}

			{!showCustomInput && !loading && (
				<select
					value={value ?? ''}
					disabled={disabled}
					onChange={(event) => {
						onChange(event.target.value);
					}}
				>
					{children}

					{injectCustomValueOption && (
						<option value="gf_custom">
							{strings.addCustomValue}
						</option>
					)}
				</select>
			)}

			<div className="gppa-select-with-custom-input-container">
				<input
					type="text"
					value={inputValue}
					disabled={disabled}
					onChange={(event) => {
						onChange(`gf_custom:${event.target.value}`);
					}}
					id={`gppa-select-with-custom-input_${_uid}`}
					ref={customInputRef}
					className="mt-position-right"
				/>

				{!forceCustomInput && !disabled && (
					// eslint-disable-next-line jsx-a11y/anchor-is-valid
					<a
						href="#"
						className="custom-reset"
						onClick={(event) => {
							event.preventDefault();
							onChange('');
						}}
					>
						{strings.reset}
					</a>
				)}
			</div>
		</div>
	);
};

export default Select;

import useGPPAStore from '../store/store';
import truncateStringMiddle from '../helpers/truncateStringMiddle';
import Select from './Select';
import useDeepCompareEffect from 'use-deep-compare-effect';

const { strings, defaultOperators } = window.GPPA_ADMIN;

const useFilterOperators = ({
	filter,
	flattenedProperties,
	objectTypeInstance,
}: {
	filter: GPPAFilter;
	flattenedProperties: { [property: string]: GPPAProperty };
	objectTypeInstance: GPPAObjectType | null;
}): string[] => {
	/* Labels for operators are pulled from i18nStrings in the Vue bindings */
	if (filter.property in flattenedProperties && objectTypeInstance) {
		const property = flattenedProperties[filter.property];

		if ('operators' in property) {
			return property.operators!;
		}

		if ('group' in property && property.group) {
			const group = objectTypeInstance.groups[property.group];

			if ('operators' in group) {
				return group.operators!;
			}
		}
	}

	return defaultOperators;
};

const Filter = ({
	index,
	groupIndex,
	filter,
	populate,
	filters,
}: {
	index: number;
	groupIndex: number;
	filter: GPPAFilter;
	filters: GPPAFilter[];
	populate: GPPAPopulate;
}) => {
	const useStore = useGPPAStore(populate);
	const fieldId = useStore((state) => state.field?.id);
	const propertiesLoaded = useStore(
		(state) => state.computed.propertiesLoaded
	);
	const flattenedProperties = useStore(
		(state) => state.computed.flattenedProperties
	);
	const objectTypeInstance = useStore(
		(state) => state.computed.objectTypeInstance
	);
	const filterPropertiesUngrouped = useStore(
		(state) => state.computed.filterPropertiesUngrouped
	);
	const filterPropertiesGrouped = useStore(
		(state) => state.computed.filterPropertiesGrouped
	);
	const filterSpecialValues = useStore(
		(state) => state.computed.filterSpecialValues
	);
	const filterFormFieldValues = useStore(
		(state) => state.computed.filterFormFieldValues
	);
	const propertyValues = useStore((state) => state.propertyValues);
	const getPropertyValues = useStore((state) => state.getPropertyValues);
	const updateFilter = useStore((state) => state.updateFilter);
	const addFilter = useStore((state) => state.addFilter);
	const removeFilter = useStore((state) => state.removeFilter);

	const propertyValuesLoaded = filter.property in propertyValues;

	const operators = useFilterOperators({
		filter,
		flattenedProperties,
		objectTypeInstance,
	});

	// If filter.property is not set, set it to the first property
	useDeepCompareEffect(() => {
		if (!filter.property) {
			let property = '';

			// Get the first from either ungrouped or grouped properties. First, try ungrouped.
			if (filterPropertiesUngrouped?.length) {
				property = filterPropertiesUngrouped[0].value;
			} else {
				const firstGroup = Object.values(filterPropertiesGrouped)[0];
				if (firstGroup?.length) {
					property = firstGroup[0].value;
				}
			}

			updateFilter(groupIndex, index, {
				property,
			});
		}
	}, [
		filter.property,
		filterPropertiesUngrouped,
		filterPropertiesGrouped,
		groupIndex,
		index,
		updateFilter,
	]);

	useDeepCompareEffect(() => {
		getPropertyValues(filter.property);
	}, [getPropertyValues, filter.property, fieldId]);

	if (!objectTypeInstance) {
		return null;
	}

	return (
		<div
			className="gppa-filter"
			aria-label={strings.filterAriaLabel.gformFormat(index + 1)}
			role="group"
		>
			<select
				disabled={!propertiesLoaded}
				className="gppa-filter-property"
				value={propertiesLoaded ? filter.property : ''}
				onChange={(event) =>
					updateFilter(groupIndex, index, {
						property: event.target.value,
						value: '',
					})
				}
			>
				{!propertiesLoaded || !objectTypeInstance ? (
					<option value="" disabled hidden>
						{strings.loadingEllipsis}
					</option>
				) : (
					<>
						{filterPropertiesUngrouped?.map((option) => (
							<option value={option.value} key={option.value}>
								{truncateStringMiddle(option.label)}
							</option>
						))}

						{Object.entries(filterPropertiesGrouped).map(
							([groupID, options]) => (
								<optgroup
									key={groupID}
									label={
										groupID in objectTypeInstance.groups
											? objectTypeInstance.groups[groupID]
													.label
											: undefined
									}
								>
									{options.map((option) => (
										<option
											value={option.value}
											key={option.value}
										>
											{truncateStringMiddle(option.label)}
										</option>
									))}
								</optgroup>
							)
						)}
					</>
				)}
			</select>

			<select
				className="gppa-filter-operator"
				value={filter.operator}
				onChange={(event) =>
					updateFilter(groupIndex, index, {
						operator: event.target.value,
					})
				}
				disabled={!propertiesLoaded || !propertyValuesLoaded}
			>
				{operators.map((operator) => (
					<option value={operator} key={operator}>
						{/* @ts-ignore */}
						{strings.operators[operator]}
					</option>
				))}
			</select>

			<Select
				className="gppa-filter-value"
				value={
					propertiesLoaded && propertyValuesLoaded ? filter.value : ''
				}
				objectTypeInstance={objectTypeInstance}
				flattenedProperties={flattenedProperties}
				forceCustomInput={filter.operator.indexOf('like') !== -1}
				disabled={!propertiesLoaded || !propertyValuesLoaded}
				onChange={(value) => updateFilter(groupIndex, index, { value })}
			>
				{!(propertiesLoaded && propertyValuesLoaded) && (
					<option value="" disabled hidden>
						{strings.loadingEllipsis}
					</option>
				)}
				{!filter.value && (
					<option value="" disabled hidden>
						&ndash; Value &ndash;
					</option>
				)}

				<optgroup label={strings.specialValues}>
					<option value="gf_custom">{strings.addCustomValue}</option>

					{filterSpecialValues.map((option, optionIndex) => (
						<option key={option.value} value={option.value}>
							{option.label}
						</option>
					))}
				</optgroup>

				{filterFormFieldValues && filterFormFieldValues.length && (
					<optgroup label={strings.formFieldValues}>
						{filterFormFieldValues.map((option, optionIndex) => (
							<option key={option.value} value={option.value}>
								{truncateStringMiddle(option.label)}
							</option>
						))}
					</optgroup>
				)}

				{propertyValues?.[filter.property] &&
					propertyValues[filter.property].map(
						(option, optionIndex) => (
							<option
								key={option.value}
								value={option.value}
								disabled={option.disabled}
							>
								{truncateStringMiddle(option.label)}
							</option>
						)
					)}
			</Select>

			<div className="repeater-buttons">
				<button
					className="add-item gform-st-icon gform-st-icon--circle-plus"
					title={strings.addFilter}
					onClick={() => addFilter(index, groupIndex)}
				/>
				<button
					className="remove-item gform-st-icon gform-st-icon--circle-minus"
					title={strings.removeFilter}
					onClick={() => removeFilter(index, groupIndex)}
					aria-label={strings.removeFilterAriaLabel.gformFormat(
						index + 1
					)}
				/>
			</div>

			{filters.length > 1 && index !== filters.length - 1 && (
				<div className="gppa-filter-and" aria-label={strings.and}>
					{strings.and}
				</div>
			)}
		</div>
	);
};

export default Filter;

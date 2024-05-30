import useGPPAStore from '../store/store';
import truncateStringMiddle from '../helpers/truncateStringMiddle';

const { strings } = window.GPPA_ADMIN;

const PrimaryPropertySelect = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const objectTypeInstance = useStore(
		(state) => state.computed.objectTypeInstance
	);
	const usingFieldObjectType = useStore(
		(state) => state.computed.usingFieldObjectType
	);
	const propertyValues = useStore((state) => state.propertyValues);
	const primaryProperty = useStore((state) => state.primaryProperty);
	const setPrimaryProperty = useStore((state) => state.setPrimaryProperty);

	const CustomPrimaryPropertySelect = useStore(
		(state) => state.computed.CustomPrimaryPropertySelect
	);

	if (
		!objectTypeInstance ||
		!('primary-property' in objectTypeInstance) ||
		usingFieldObjectType
	) {
		return null;
	}

	if (CustomPrimaryPropertySelect) {
		return (
			<CustomPrimaryPropertySelect
				populate={populate}
				propertyValues={propertyValues}
				primaryProperty={primaryProperty}
				setPrimaryProperty={setPrimaryProperty}
			/>
		);
	}

	return (
		<>
			<label
				htmlFor={`gppa-${populate}-primary-property`}
				className="section_label gppa-primary-property-label"
				style={{ marginTop: 15 }}
			>
				{objectTypeInstance?.['primary-property']?.label}
			</label>

			{!('primary-property' in propertyValues) ? (
				<select
					className="gppa-primary-property"
					disabled
					id={`gppa-${populate}-primary-property`}
					value=""
				>
					<option value="" disabled>
						{strings.loadingEllipsis}
					</option>
				</select>
			) : (
				<select
					className="gppa-primary-property"
					value={primaryProperty}
					onChange={(event) => setPrimaryProperty(event.target.value)}
					id={`gppa-${populate}-primary-property`}
				>
					{!primaryProperty && (
						<option value="" hidden disabled>
							{strings.selectAnItem.replace(
								/%s/g,
								objectTypeInstance?.['primary-property']?.label!
							)}
						</option>
					)}
					{propertyValues['primary-property'].map((option: any) => (
						<option key={option.value} value={option.value}>
							{truncateStringMiddle(option.label)}
						</option>
					))}
				</select>
			)}
		</>
	);
};

export default PrimaryPropertySelect;

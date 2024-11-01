import useGPPAStore from '../store/store';
const { strings, objectTypes } = window.GPPA_ADMIN;

interface GPPAOptGroup {
	options: GPPAObjectType[];
	label: string;
	optGroupId: string;
}

type GPPAOption = GPPAObjectType | GPPAOptGroup;

function isOptGroup(option: GPPAOption): option is GPPAOptGroup {
	return 'options' in option && 'label' in option && 'optGroupId' in option;
}

/**
 * Converts the objectTypes object to an array of options OR
 * option groups that can easily be used to render the
 * ObjectTypeSelect object type options.
 */
function objectTypesToOptionsArray(objectTypes: {
	[objectTypeId: string]: GPPAObjectType;
}): GPPAOption[] {
	const opts: GPPAOption[] = [];
	const groupedOpts: { [objectTypeId: string]: GPPAOptGroup } = {};

	for (const objectTypeId in objectTypes) {
		const objectType = objectTypes[objectTypeId];
		const { optionGroupId } = objectType;

		if (!optionGroupId) {
			opts.push(objectType);
			continue;
		}

		if (!groupedOpts[optionGroupId]) {
			groupedOpts[optionGroupId] = {
				options: [],
				label: objectType.optionGroupLabel ?? optionGroupId,
				optGroupId: optionGroupId,
			};
		}

		groupedOpts[optionGroupId].options.push(objectType);
	}

	for (const optGroupId in groupedOpts) {
		const objectTypeGroup = groupedOpts[optGroupId];
		opts.push(objectTypeGroup);
	}

	return opts;
}

const Option = ({
	objectType,
	isSuperAdmin,
}: {
	objectType: GPPAObjectType;
	isSuperAdmin: boolean;
}) => {
	if (objectType.restricted && !isSuperAdmin) {
		return null;
	}

	return <option value={objectType.id}>{objectType.label}</option>;
};

const ObjectTypeSelect = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const objectType = useStore((state) => state.objectType);
	const setObjectType = useStore((state) => state.setObjectType);
	const fieldValueObjects = useStore(
		(state) => state.computed.fieldValueObjects
	);
	const isSuperAdmin = useStore((state) => state.computed.isSuperAdmin);

	return (
		<select
			className="gppa-object-type"
			value={typeof objectType === 'undefined' ? '' : objectType}
			onChange={(event) => setObjectType(event.target.value)}
		>
			<option value="" disabled>
				&ndash; {strings.objectType} &ndash;
			</option>

			{objectTypesToOptionsArray(objectTypes).map((objectType) => {
				if (isOptGroup(objectType)) {
					return (
						<optgroup
							key={objectType.optGroupId}
							label={objectType.label}
						>
							{objectType.options.map((objectType) => (
								<Option
									key={objectType.id}
									isSuperAdmin={isSuperAdmin}
									objectType={objectType}
								></Option>
							))}
						</optgroup>
					);
				}

				return (
					<Option
						key={objectType.id}
						objectType={objectType}
						isSuperAdmin={isSuperAdmin}
					/>
				);
			})}

			{populate === 'values' &&
				fieldValueObjects.map((field: GravityFormsField) => (
					<option
						key={field.id}
						value={`field_value_object:${field.id}`}
					>
						Field Value Object: {field.label}
					</option>
				))}
		</select>
	);
};

export default ObjectTypeSelect;

import useGPPAStore from '../store/store';
const { strings, objectTypes } = window.GPPA_ADMIN;

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

			{Object.values(objectTypes).map((objectType) =>
				objectType.restricted && !isSuperAdmin ? null : (
					<option key={objectType.id} value={objectType.id}>
						{objectType.label}
					</option>
				)
			)}

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

import truncateStringMiddle from '../helpers/truncateStringMiddle';
import useGPPAStore from '../store/store';
const { strings } = window.GPPA_ADMIN;

const Ordering = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const orderingProperty = useStore((state) => state.orderingProperty);
	const orderingMethod = useStore((state) => state.orderingMethod);
	const setOrderingProperty = useStore((state) => state.setOrderingProperty);
	const setOrderingMethod = useStore((state) => state.setOrderingMethod);
	const propertiesLoaded = useStore(
		(state) => state.computed.propertiesLoaded
	);
	const orderingPropertiesUngrouped = useStore(
		(state) => state.computed.orderingPropertiesUngrouped
	);
	const orderingPropertiesGrouped = useStore(
		(state) => state.computed.orderingPropertiesGrouped
	);
	const objectTypeInstance = useStore(
		(state) => state.computed.objectTypeInstance
	);

	if (!objectTypeInstance) {
		return null;
	}

	return (
		<div className="gppa-ordering-container">
			{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
			<label
				className="section_label gppa-ordering-label"
				style={{ marginTop: 15 }}
			>
				{strings.ordering}
			</label>

			<div className="gppa-ordering">
				<select
					className="gppa-ordering-property"
					value={!propertiesLoaded ? '' : orderingProperty}
					disabled={!propertiesLoaded}
					onChange={(event) =>
						setOrderingProperty(event.target.value)
					}
				>
					{propertiesLoaded ? (
						<option value="" disabled hidden>
							&ndash; Select a Property &ndash;
						</option>
					) : (
						<option value="" disabled hidden>
							{strings.loadingEllipsis}
						</option>
					)}

					{orderingPropertiesUngrouped?.map((option) => (
						<option key={option.value} value={option.value}>
							{truncateStringMiddle(option.label)}
						</option>
					))}

					{Object.entries(orderingPropertiesGrouped)?.map(
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
										key={option.value}
										value={option.value}
									>
										{truncateStringMiddle(option.label)}
									</option>
								))}
							</optgroup>
						)
					)}
				</select>

				<select
					className="gppa-ordering-method"
					value={orderingMethod}
					disabled={!propertiesLoaded}
					onChange={(event) => setOrderingMethod(event.target.value)}
				>
					<option value="asc">{strings.ascending}</option>
					<option value="desc">{strings.descending}</option>
					<option value="rand">{strings.random}</option>
				</select>
			</div>
		</div>
	);
};

export default Ordering;

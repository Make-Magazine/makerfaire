import truncateStringMiddle from '../helpers/truncateStringMiddle';
import useGPPAStore from '../store/store';
import Select from './Select';
const { strings } = window.GPPA_ADMIN;

const Templates = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const objectTypeInstance = useStore(
		(state) => state.computed.objectTypeInstance
	);
	const templateRows = useStore((state) => state.computed.templateRows);
	const updateTemplate = useStore((state) => state.updateTemplate);
	const flattenedProperties = useStore(
		(state) => state.computed.flattenedProperties
	);
	const templates = useStore((state) => state.templates);
	const propertiesLoaded = useStore(
		(state) => state.computed.propertiesLoaded
	);
	const templatePropertiesUngrouped = useStore(
		(state) => state.computed.templatePropertiesUngrouped
	);
	const templatePropertiesGrouped = useStore(
		(state) => state.computed.templatePropertiesGrouped
	);

	if (!objectTypeInstance) {
		return null;
	}

	return (
		<div className="gppa-templates">
			{populate === 'choices' ? (
				// eslint-disable-next-line jsx-a11y/label-has-associated-control
				<label className="section_label" style={{ marginTop: 15 }}>
					{strings.choiceTemplate}
				</label>
			) : (
				// eslint-disable-next-line jsx-a11y/label-has-associated-control
				<label className="section_label" style={{ marginTop: 15 }}>
					{strings.valueTemplates}
				</label>
			)}

			<table className="field_custom_inputs_ui gppa-templates">
				<tbody>
					{templateRows.map((templateRow) => (
						<tr
							className="field_custom_input_row gppa-template-row"
							key={templateRow.label}
						>
							<td>
								{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
								<label className="inline">
									{templateRow.label}
								</label>
							</td>

							<td>
								{!propertiesLoaded ? (
									<select disabled defaultValue="">
										<option value="" disabled>
											{strings.loadingEllipsis}
										</option>
									</select>
								) : (
									<Select
										objectTypeInstance={objectTypeInstance}
										flattenedProperties={
											flattenedProperties
										}
										value={templates[templateRow.id]}
										onChange={(value) =>
											updateTemplate(
												templateRow.id,
												value
											)
										}
									>
										{!!(
											!templates[templateRow.label] ||
											!templates[templateRow.label].value
										) && (
											<option value="" disabled hidden>
												&ndash; Property &ndash;
											</option>
										)}

										<option value="gf_custom">
											{strings.addCustomValue}
										</option>

										{templatePropertiesUngrouped.map(
											(property) => (
												<option
													key={property.value}
													value={property.value}
												>
													{truncateStringMiddle(
														property.label
													)}
												</option>
											)
										)}

										{Object.entries(
											templatePropertiesGrouped
										).map(([groupID, properties]) => (
											<optgroup
												key={groupID}
												label={
													groupID in
													objectTypeInstance.groups
														? objectTypeInstance
																.groups[groupID]
																.label
														: undefined
												}
											>
												{properties.map((property) => (
													<option
														key={property.value}
														value={property.value}
													>
														{truncateStringMiddle(
															property.label
														)}
													</option>
												))}
											</optgroup>
										))}
									</Select>
								)}
							</td>
						</tr>
					))}
				</tbody>
			</table>
		</div>
	);
};

export default Templates;

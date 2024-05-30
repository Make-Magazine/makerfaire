import useGPPAStore from '../store/store';
import ObjectTypeSelect from './ObjectTypeSelect';
import { useEffect } from 'react';
import $ from 'jquery';
import {
	hideStaticSettings,
	showStaticSettings,
} from '../helpers/staticSettings';
import FilterGroups from './FilterGroups';
import PrimaryPropertySelect from './PrimaryPropertySelect';
import Ordering from './Ordering';
import ResultsPreview from './ResultsPreview';
import Templates from './Templates';

const { strings } = window.GPPA_ADMIN;

const PopulateDynamically = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const isSupportedField = useStore(
		(state) => state.computed.isSupportedField
	);
	const field = useStore((state) => state.field);

	const enabled = useStore((state) => state.enabled);
	const setEnabled = useStore((state) => state.setEnabled);
	const objectType = useStore((state) => state.objectType);
	const primaryPropertySelected = useStore(
		(state) => state.computed.primaryPropertySelected
	);
	const usingFieldObjectType = useStore(
		(state) => state.computed.usingFieldObjectType
	);
	const isRestrictedObjectTypeActive = useStore(
		(state) => state.computed.isRestrictedObjectTypeActive
	);
	const isSuperAdmin = useStore((state) => state.computed.isSuperAdmin);
	const uniqueResults = useStore((state) => state.uniqueResults);
	const setUniqueResults = useStore((state) => state.setUniqueResults);

	useEffect(() => {
		const $field = $('.gfield').filter('#field_' + field?.id);

		if (enabled === true) {
			$field.addClass(`gppa-${populate}-enabled`);
			hideStaticSettings(isSupportedField, populate);
		} else {
			$field.removeClass(`gppa-${populate}-enabled`);
			showStaticSettings(isSupportedField, populate);
		}
	}, [enabled, field?.id, isSupportedField, populate]);

	if (!isSupportedField) {
		return null;
	}

	return (
		<li id={`gppa-${populate}`} className="gppa field_setting">
			<input
				type="checkbox"
				id={`gppa-${populate}-enabled`}
				checked={enabled}
				onChange={(event) => setEnabled(event.target.checked)}
			/>

			<label className="inline" htmlFor={`gppa-${populate}-enabled`}>
				<span>
					{populate === 'choices'
						? strings.populateChoices
						: strings.populateValues}
				</span>
			</label>

			{enabled && isRestrictedObjectTypeActive && !isSuperAdmin && (
				<div className="gp-child-settings">
					<div className="gppa-warning">
						{strings.restrictedObjectTypeNonPrivileged}
					</div>
				</div>
			)}

			{enabled && !(isRestrictedObjectTypeActive && !isSuperAdmin) && (
				<div className="gp-child-settings">
					{isRestrictedObjectTypeActive && isSuperAdmin && (
						<div className="gppa-warning">
							{strings.restrictedObjectTypePrivileged}
						</div>
					)}

					{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
					<label className="section_label">{strings.type}</label>

					<ObjectTypeSelect populate={populate} />

					{objectType && (
						<>
							<PrimaryPropertySelect populate={populate} />

							{primaryPropertySelected && (
								<div className="gppa-main-settings">
									{!usingFieldObjectType && (
										<>
											{/* eslint-disable-next-line jsx-a11y/label-has-associated-control */}
											<label
												className="section_label gppa-filters-label"
												style={{ marginTop: 15 }}
											>
												{strings.filters}
											</label>

											<FilterGroups populate={populate} />

											<div style={{ marginTop: 15 }}>
												<input
													type="checkbox"
													id={`gppa-${populate}-unique-results`}
													checked={uniqueResults}
													onChange={(event) =>
														setUniqueResults(
															event.target.checked
														)
													}
												/>

												<label
													className="inline"
													htmlFor={`gppa-${populate}-unique-results`}
												>
													<span>
														{strings.unique}
													</span>
												</label>
											</div>

											<ResultsPreview
												populate={populate}
											/>

											<Ordering populate={populate} />
										</>
									)}

									<Templates populate={populate} />
								</div>
							)}
						</>
					)}
				</div>
			)}
		</li>
	);
};

export default PopulateDynamically;

import useGPPAStore from '../store/store';
import { Fragment } from 'react';
import Filter from './Filter';

const { strings } = window.GPPA_ADMIN;

const FilterGroups = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const filterGroups = useStore((state) => state.filterGroups);
	const addFilterGroup = useStore((state) => state.addFilterGroup);
	const propertiesLoaded = useStore(
		(state) => state.computed.propertiesLoaded
	);

	return (
		<div
			className="gppa-filter-groups"
			aria-label={strings.filterGroups}
			role="group"
		>
			{filterGroups.map((filters, filterGroupIndex) => (
				<Fragment key={filterGroupIndex}>
					<div
						className="gppa-filter-group"
						// @ts-ignore
						aria-label={strings.filterGroupAriaLabel.gformFormat(
							filterGroupIndex + 1
						)}
						role="group"
					>
						{filters.map((filter, filterIndex) => (
							<Filter
								key={filterIndex}
								groupIndex={filterGroupIndex}
								index={filterIndex}
								filter={filter}
								filters={filters}
								populate={populate}
							/>
						))}
					</div>

					{filterGroups.length > 1 &&
						filterGroupIndex !== filterGroups.length - 1 && (
							<div
								className="gppa-filter-group-or"
								aria-label={strings.or}
							>
								&mdash; {strings.or} &mdash;
							</div>
						)}
				</Fragment>
			))}

			<button
				className="gppa-add-filter-group button button-secondary"
				onClick={addFilterGroup}
				disabled={!propertiesLoaded}
			>
				<i className="gficon-add"></i> {strings.addFilterGroup}
			</button>
		</div>
	);
};

export default FilterGroups;

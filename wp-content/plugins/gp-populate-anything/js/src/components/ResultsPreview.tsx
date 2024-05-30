import classnames from 'classnames';
import useGPPAStore from '../store/store';
import { useEffect, useRef } from 'react';

const ResultsPreview = ({ populate }: { populate: GPPAPopulate }) => {
	const useStore = useGPPAStore(populate);
	const field = useStore((state) => state.field);
	const objectTypeInstance = useStore(
		(state) => state.computed.objectTypeInstance
	);
	const objectType = useStore((state) => state.objectType);
	const hasFilterFieldValue = useStore(
		(state) => state.computed.hasFilterFieldValue
	);
	const missingTemplates = useStore(
		(state) => state.computed.missingTemplates
	);

	const results = useStore((state) => state.previewResults?.results);
	const limit = useStore((state) => state.previewResults?.limit);
	const error = useStore((state) => state.previewError);
	const loading = useStore((state) => state.previewResultsLoading);
	const resultColumns = useStore((state) => state.computed.resultColumns);
	const getPreviewResults = useStore((state) => state.getPreviewResults);
	const uniqueResults = useStore((state) => state.uniqueResults);
	const orderingMethod = useStore((state) => state.orderingMethod);
	const orderingProperty = useStore((state) => state.orderingProperty);
	const templates = useStore((state) => state.templates);
	const filterGroups = useStore((state) => state.filterGroups);

	const resultsPreviewLinkRef = useRef<HTMLAnchorElement>(null);

	useEffect(() => {
		getPreviewResults();
	}, [
		getPreviewResults,
		objectType,
		uniqueResults,
		orderingMethod,
		orderingProperty,
		templates,
		filterGroups,
	]);

	if (!field) {
		return null;
	}

	let result = null;

	if (hasFilterFieldValue) {
		result = (
			<>
				<strong>Heads-up!</strong> Cannot preview results when filtering
				by Form&nbsp;Field&nbsp;Value.
			</>
		);
	} else if (missingTemplates.length) {
		result = (
			<>
				Select{' '}
				{missingTemplates.map((missingTemplate, index) => (
					<span key={index}>
						<strong>{missingTemplate}</strong>
						{index + 1 < missingTemplates.length && ' and '}
					</span>
				))}{' '}
				{missingTemplates.length > 1 ? 'templates' : 'template'} to
				preview results.
			</>
		);
	} else if (error) {
		result = (
			<>
				<strong>Error Loading Results:</strong> <code>{error}</code>
			</>
		);
	} else if (loading) {
		result = (
			<>
				{/* eslint-disable-next-line jsx-a11y/alt-text */}
				<img src={window.gf_global.spinnerUrl} /> Loading Results
			</>
		);
	} else if (results && results.length === 0) {
		result = (
			<>
				<strong>{results.length}</strong> results found
			</>
		);
	} else if (results && results.length > 0) {
		result = (
			<>
				<a
					className="thickbox"
					title="Results Preview"
					href={`#TB_inline?width=600&height=450&inlineId=gppa-results-${populate}-thickbox`}
					ref={resultsPreviewLinkRef}
					onClick={(event) => {
						event.preventDefault();

						if (!resultsPreviewLinkRef.current) {
							return;
						}

						window.tb_click.call(resultsPreviewLinkRef.current);
					}}
				>
					<strong>
						{results.length}
						{!!(limit && results.length >= limit) ? '+' : ''}
					</strong>{' '}
					{results.length === 1 ? 'result' : 'results'}
				</a>{' '}
				found.
			</>
		);
	}

	return (
		<div
			className={classnames('gppa-results', {
				'gppa-results-loading': loading,
			})}
		>
			{!!(results && results.length) && (
				<div
					id={`gppa-results-${populate}-thickbox`}
					style={{ display: 'none' }}
				>
					<div className="gppa-results-preview-contents">
						{!!(limit && results.length >= limit * 0.8) && (
							<div
								className="notice notice-warning notice-alt"
								style={{ margin: '0 0 15px', display: 'block' }}
							>
								<p>
									For optimal performance, only the first{' '}
									{limit} results will be populated. You may
									increase this limit using the{' '}
									<a href="https://gravitywiz.com/documentation/gppa_query_limit/">
										gppa_query_limit
									</a>{' '}
									filter.
								</p>
							</div>
						)}

						<table className="wp-list-table widefat fixed striped">
							<thead>
								<tr>
									{resultColumns.map((column, index) => (
										<th key={index}>{column}</th>
									))}
								</tr>
							</thead>
							<tbody>
								{results.map((row, index) => (
									<tr key={index}>
										{Object.values(row).map(
											(columnValue, index) => (
												<td
													key={index}
													dangerouslySetInnerHTML={{
														__html: columnValue,
													}}
												/>
											)
										)}
									</tr>
								))}
							</tbody>
						</table>
					</div>
				</div>
			)}

			{result}
		</div>
	);
};

export default ResultsPreview;

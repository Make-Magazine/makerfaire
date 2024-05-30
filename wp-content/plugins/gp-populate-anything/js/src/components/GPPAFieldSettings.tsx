import PopulateDynamically from './PopulateDynamically';

const GPPAFieldSettings = () => {
	return (
		<div id="gppa">
			<ul>
				<PopulateDynamically populate="choices" />
				<PopulateDynamically populate="values" />
			</ul>
		</div>
	);
};

export default GPPAFieldSettings;

export default function truncateStringMiddle(str: string) {
	/**
	 * Filter the max string length used in the Form Editor.
	 *
	 * @param int maxLength The max length of the string.
	 *
	 * @since 2.0.37
	 */
	const maxLength = window.gform.applyFilters(
		'gppa_form_editor_max_string_length',
		50
	);

	if (!str || !maxLength) {
		return str;
	}

	if (str.length > maxLength) {
		return (
			str.substr(0, maxLength * 0.45) +
			' ... ' +
			str.substr(str.length - maxLength * 0.4, str.length)
		);
	}

	return str;
}

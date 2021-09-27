"use strict";


window.dceAmountField = {
	refresherGenerators: {}
};

dceAmountField.registerRefresherGenerator = function(field_id, refresherGenerator) {
	this.refresherGenerators[field_id] = refresherGenerator;
}

dceAmountField.getFieldValue = (form, id) => {
	let data = new FormData(form);
	let key = `form_fields[${id}]`;
	if (data.has(key)) {
		return data.get(key);
	}
	key = `form_fields[${id}][]`
	if (data.has(key))  {
		return data.getAll(key);
	}
	return "";
}

dceAmountField.makeGetFieldFunction = (form) => {
	return (id) => {
		let val = dceAmountField.getFieldValue(form, id);
		let parsed = parseFloat(val);
		return isNaN(parsed) ? 0 : parsed;
	}
}

function initializeAmountField(wrapper, widget) {
	let input = wrapper.getElementsByTagName('input')[0];
	let form = widget.getElementsByTagName('form')[0];
	let fieldId = input.dataset.fieldId;
	let textBefore = input.dataset.textBefore;
	let textAfter = input.dataset.textAfter;
	let realTime = input.dataset.realTime === 'yes';
	if (input.dataset.hide == 'yes') {
		wrapper.style.display = "none";
	}
	let refresherGenerator = dceAmountField.refresherGenerators[fieldId];
	// if the user code has a syntax error the registration will have failed and
	// we won't find the field:
	if (! refresherGenerator) {
		input.value = amountFieldLocale.syntaxError;
		return;
	}
	let refresher = refresherGenerator(dceAmountField.makeGetFieldFunction(form));
	let onChange = () => {
		input.value = textBefore + refresher() + textAfter;
		if ("createEvent" in document) {
			var evt = document.createEvent("HTMLEvents");
			evt.initEvent("change", false, true);
			input.dispatchEvent(evt);
		}
		else {
			input.fireEvent("onchange");
		};
	}
	onChange();
	form.addEventListener('input', onChange);
}

function initializeAllAmountFields($scope) {
	$scope.find('.elementor-field-type-amount').each((_, w) => initializeAmountField(w, $scope[0]));
}

jQuery(window).on('elementor/frontend/init', function() {
	elementorFrontend.hooks.addAction('frontend/element_ready/form.default', initializeAllAmountFields);
});

"use strict";
if (pagedSettings.selector !== 'body') {
	let element = document.querySelector(pagedSettings.selector);
	document.body.innerHTML = '';
	document.body.append(element);
}

window.addEventListener('afterprint', (event) => {
	window.history.back();
});

window.PagedConfig = {
	after: () => {
		window.print();
		setTimeout(() => window.history.back(), 1000);
	},
};

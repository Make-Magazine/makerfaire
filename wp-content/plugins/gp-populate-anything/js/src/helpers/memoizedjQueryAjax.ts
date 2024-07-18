import memoize from 'memoizee';

const deepSortedEntries = (object: {
	[key: string | symbol | number]: any;
}): any[] =>
	Object.entries(object)
		.map(([key, value]) => {
			if (value && typeof value === 'object')
				return [key, deepSortedEntries(value)];
			return [key, value];
		})
		.sort();

export const memoizedjQueryAjax = memoize(
	(url: string, settings: JQuery.AjaxSettings): JQuery.jqXHR => {
		return jQuery.ajax(url, settings);
	},
	{
		promise: true,
		normalizer(args) {
			// args is arguments object as accessible in memoized function
			return JSON.stringify([args[0], deepSortedEntries(args[1])]);
		},
		maxAge: 1000 * 5, // 5 seconds, it will be enough to improve performance and prevent multiple requests, but not too long to prevent stale data
	}
);

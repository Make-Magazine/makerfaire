module.exports = function(grunt) {

	let sources = [
		'parts/index.js',
		'parts/list-table.js',
		'parts/list-table-bookings.js',
		'parts/datepicker.js',
		'parts/timepicker.js',
		'parts/selectize.js',
		'parts/tippy.js',
		'parts/maps.js',
		'parts/modal.js',
		'parts/search.js',
		'parts/calendar.js',
		'parts/phone.js',
		'parts/externals.js',
		'parts/final.js'
	];

	// Project configuration.
	grunt.initConfig({
		concat: {
			options: {
				sourceMap: true,
				separator: '\n\n',
			},
			base: {
				sourceMap: true,
				src: sources,
				dest: '../events-manager.js',
			},
		},
		terser: {
			options: {
				compress: true,
				mangle: true,
				sourceMap: {
					root: 'src',
					url: 'events-manager.min.js.map'
				},
			},
			build: {
				src: sources,
				dest: '../events-manager.min.js'
			}
		},
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-terser');

	// Default task(s).
	grunt.registerTask('default', ['concat','terser']);

};
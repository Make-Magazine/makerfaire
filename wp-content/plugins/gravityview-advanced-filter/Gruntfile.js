module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			options: { mangle: false },
			advanced_filter: {
				files: [{
		          expand: true,
		          cwd: 'assets',
		          src: ['**/*.js','!**/*.min.js'],
		          dest: 'assets',
		          ext: '.min.js',
		          extDot: 'last'
		      }],
			},
		},

		watch: {
			advanced_filter: {
				files: ['assets/js/*.js','!assets/js/*.min.js','readme.txt'],
				tasks: ['uglify:advanced_filter','wp_readme_to_markdown']
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'readme.md': 'readme.txt'
				},
			},
		},

		dirs: {
			lang: 'languages'
		},

		// Convert the .po files to .mo files
		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: '<%= dirs.lang %>',
					src: ['*.po'],
					dest: '<%= dirs.lang %>',
					ext: '.mo',
					nonull: true
				}]
			}
		},

		// Build translations without POEdit
		makepot: {
			target: {
				options: {
					mainFile: 'advanced-filter.php',
					type: 'wp-plugin',
					domainPath: '/languages',
					updateTimestamp: false,
					exclude: ['node_modules/.*', 'assets/.*', 'tmp/.*', 'vendor/.*', 'includes/lib/xml-parsers/.*', 'includes/lib/jquery-cookie/.*', 'includes/lib/standalone-phpenkoder/.*' ],
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					processPot: function( pot, options ) {
						pot.headers['language'] = 'en_US';
						pot.headers['language-team'] = 'Katz Web Services, Inc. <support@katz.co>';
						pot.headers['last-translator'] = 'Katz Web Services, Inc. <support@katz.co>';
						pot.headers['report-msgid-bugs-to'] = 'https://gravityview.co/support/';

						var translation,
							excluded_meta = [
								'GravityView - Advanced Filter Extension',
								'Filter which entries are shown in a View based on their values.',
								'https://gravityview.co/extensions/advanced-filter/',
								'https://gravityview.co/?utm_source=advanced-filter&utm_medium=meta&utm_content=author_uri&utm_campaign=internal',
								'https://gravityview.co/extensions/advanced-filter/?utm_source=advanced-filter&utm_content=plugin_uri&utm_medium=meta&utm_campaign=internal',
								'Katz Web Services, Inc.',
								'http://www.katzwebservices.com',
							    'https://gravityview.co'
							];

						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][ translation ].msgid ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][ translation ].msgid );
									delete pot.translations[''][ translation ];
								}
							}
						}

						return pot;
					}
				}
			}
		},

		// Add textdomain to all strings, and modify existing textdomains in included packages.
		addtextdomain: {
			options: {
				textdomain: 'gravityview-advanced-filter',    // Project text domain.
				updateDomains: [ 'gravity-view-advanced-filter', 'gravityview', 'gravity-view', 'gravityforms', 'edd_sl', 'edd' ]  // List of text domains to replace.
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**'
					]
				}
			}
		},

		// Pull in the latest translations
		exec: {
			transifex: 'tx pull -a',

			// Create a ZIP file
			zip: 'git-archive-all ../gravityview-advanced-filter.zip'
		}

	});

	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-potomo');
	grunt.loadNpmTasks('grunt-exec');


	grunt.registerTask( 'default', ['uglify','exec:transifex','potomo', 'addtextdomain', 'makepot', 'watch'] );

};
module.exports = function(grunt) {

	// Only need to install one package and this will load them all for you. Run:
	// npm install --save-dev load-grunt-tasks
	require('load-grunt-tasks')(grunt);

	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		uglify: {
			options: {
				mangle: false
			},
			datatables: {
				files: [{
					expand: true,
					cwd: 'assets/js',
					src: ['**/*.js','!**/*.min.js'],
					dest: 'assets/js',
					ext: '.min.js',
					extDot: 'last'
				}]
			},
			buttons: {
				files: {
					'assets/datatables-buttons/js/gv-buttons.min.js': [
						'assets/datatables-buttons/js/dataTables.buttons.js',
						'assets/datatables-buttons/js/buttons.flash.js',
						'assets/datatables-buttons/js/buttons.html5.js',
						'assets/datatables-buttons/js/buttons.print.js'
					]
				}
			},
			extensions: {
				files: {
					'assets/datatables-fixedcolumns/js/dataTables.fixedColumns.min.js': ['assets/datatables-fixedcolumns/js/dataTables.fixedColumns.js'],
					'assets/datatables-fixedheader/js/dataTables.fixedHeader.min.js': ['assets/datatables-fixedheader/js/dataTables.fixedHeader.js'],
					'assets/datatables-responsive/js/dataTables.responsive.min.js': ['assets/datatables-responsive/js/dataTables.responsive.js'],
					'assets/datatables-scroller/js/dataTables.scroller.min.js': ['assets/datatables-scroller/js/dataTables.scroller.js']
				}
			}
		},

		jshint: {
			files: [ 'Gruntfile.js', 'assets/js/*.js', '!assets/js/combo-*', '!assets/js/*.min.js', '!Gruntfile.js' ],
			options: {
				// options here to override JSHint defaults
				globals: {
					jQuery: true,
					console: true,
					module: true,
					document: true
				}
			}
		},

		/*concat: {
			options: {
				stripBanners: true
			},
			dist: {
				files: {
					/!*'assets/js/combo-all-plugins.js': [
						'assets/datatables-tabletools/js/js.dataTables.tableTools.js',
						'assets/datatables-buttons/js/js.dataTables.buttons.js',
						'assets/datatables-scroller/js/dataTables.scroller.js',
						'assets/datatables-responsive/js/dataTables.responsive.js',
						'assets/datatables-fixedheader/js/dataTables.fixedHeader.js',
						'assets/datatables-fixedcolumns/js/dataTables.fixedColumns.js',
					],*!/

				}
			}
		},*/

		sass: {
			options: {
				outputStyle: 'compressed'
			},
			dist: {
				files: [{
					expand: true,
					cwd: 'assets/css/scss',
					src: ['*.scss'],
					dest: 'assets/css',
					ext: '.css'
				}]
			},
			buttons: {
				files: [{
					expand: true,
					cwd: 'assets/datatables-buttons/css',
					src: ['buttons.dataTables.scss'],
					dest: 'assets/datatables-buttons/css',
					ext: '.css'
				}]
			},
			fixedcolumns: {
				files: [{
					expand: true,
					cwd: 'assets/datatables-fixedcolumns/css',
					src: ['*.scss'],
					dest: 'assets/datatables-fixedcolumns/css',
					ext: '.css'
				}]
			},
			fixedheader: {
				files: [{
					expand: true,
					cwd: 'assets/datatables-fixedheader/css',
					src: ['*.scss'],
					dest: 'assets/datatables-fixedheader/css',
					ext: '.css'
				}]
			},
			responsive: {
				files: [{
					expand: true,
					cwd: 'assets/datatables-responsive/css',
					src: ['*.scss'],
					dest: 'assets/datatables-responsive/css',
					ext: '.css'
				}]
			},
			scroller: {
				files: [{
					expand: true,
					cwd: 'assets/datatables-scroller/css',
					src: ['*.scss'],
					dest: 'assets/datatables-scroller/css',
					ext: '.css'
				}]
			}
		},

		// copy DT images assets to the 'right' place
		copy: {
			images: {
				files: [{
					expand: true,
					cwd: 'assets/datatables/media/images',
					src: '**',
					dest: 'assets/images/',
				}],
			},
		},

		watch: {
			datatables: {
				files: ['assets/js/*.js','!assets/js/*.min.js','readme.txt' ],
				tasks: ['uglify:datatables','wp_readme_to_markdown']
			},
			scss: {
				files: ['assets/css/scss/*.scss'],
				tasks: ['sass']
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

		// Pull in the latest translations
		exec: {
			transifex: 'tx pull -a',

			// Create a ZIP file
			zip: 'git-archive-all ../gravityview-datatables.zip',

			// Install / update all packages, then remove jQuery
			bower: 'bower install; bower update; bower uninstall jquery --force;'
		},

		// Build translations without POEdit
		makepot: {
			target: {
				options: {
					mainFile: 'datatables.php',
					type: 'wp-plugin',
					domainPath: '/languages',
					updateTimestamp: false,
					exclude: ['node_modules/.*', 'assets/.*', 'tmp/.*', 'tests/.*' ],
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					processPot: function( pot, options ) {
						pot.headers.language = 'en_US';
						pot.headers['language-team'] = 'Katz Web Services, Inc. <support@katz.co>';
						pot.headers['last-translator'] = 'Katz Web Services, Inc. <support@katz.co>';
						pot.headers['report-msgid-bugs-to'] = 'https://gravityview.co/support/';

						var translation,
							excluded_meta = [
								'GravityView - DataTables Extension',
								'Display entries in a dynamic table powered by DataTables & GravityView.',
								'http://gravityview.co/extensions/datatables/',
								'Katz Web Services, Inc.',
								'http://www.katzwebservices.com',
							    'gv-datatables',
							    'GPLv2 or later',
							    'http://www.gnu.org/licenses/gpl-2.0.html'
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
				textdomain: 'gv-datatables',    // Project text domain.
				updateDomains: [ 'gravityview', 'gravity-view', 'gravityforms', 'edd_sl', 'edd' ]  // List of text domains to replace.
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!tests/**',
						'!tmp/**'
					]
				}
			}
		}

	});

	// First, bower updates all packages, installs datatables-src (needed for SASS compilation), then bower_rm_src removes the src, since it's not needed.
	grunt.registerTask( 'default', [ 'exec:bower', 'sass', 'jshint', 'uglify', 'exec:transifex','potomo','copy','watch' ] );

	// Translation stuff
	grunt.registerTask( 'translate', [ 'exec:transifex', 'potomo', 'addtextdomain', 'makepot' ] );

};
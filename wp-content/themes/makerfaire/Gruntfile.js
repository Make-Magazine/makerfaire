module.exports = function(grunt) {
  var watchList = ['less/**/*.less', 'js/**/*.js', '!js/*.js', 'js/angular/**/*.js', 'js/angular/global-faires-map-app.js'];
  var cssFiles = {
    'css/bootstrap.min.css': 'less/bootstrap/bootstrap.less',
    'css/style.css': ['less/global.less', 'less/**/*.less', '!less/bootstrap/*', '!less/admin-styles/*'],
    'css/mf-admin-style.css': ['less/admin-styles/*']
  };
  // All configurations go here
  grunt.initConfig({
    // Reads the package.json file
    pkg: grunt.file.readJSON('package.json'),
    // Compile the less to css
    less: {
      dev: {
        options: {
          compress: false,
          dumpLineNumbers: 'comments'
        },
        files: cssFiles
      },
      prod: {
        options: {
          compress: true
        },
        files: cssFiles
      }
    },
    replace: {
      cachebust: {
        src: 'style.css',
        dest: 'style.css',
        replacements: [{
          from: /Version(.)+(\n)/,
          to: function(matchedWord) {
            // version with increment adding .01 increments:
            var v = parseFloat(matchedWord.match(/(\d)\.(\d)+/)[0]) + 0.01;
            v = 'Version: ' + v + '\n';
            // or version with unix epoch time:
            // v = 'Version: ' + (new Date).getTime() + '\n';
            return v;
          }
        }]
      }
    },
    // Concat js files
    concat: {
      options: {
        banner: '// Compiled file - any changes will be overwritten by grunt task\n',
        separator: ';',
        process: function(src, filepath) {
          return '//!!\n//!! ' + filepath + '\n' + src;
        }
      },
      dist: {
        files: {
          'js/built.js': ['js/src/misc-libs.js', 'js/src/*.js'],
          'js/built-libs.js': [
            'node_modules/bootstrap/**/bootstrap.min.js',
            'node_modules/bootstrap-dialog/**/bootstrap-dialog.min.js',
            'node_modules/jquery.cookie/**/jquery-cookie.js',
            'node_modules/fancybox/**/jquery.fancybox.pack.js',
            'node_modules/hammerjs/hammer.min.js',
            'js/libs-src/*.js'
          ],
          'js/built-admin-scripts.js': [
            'js/src-admin-scripts/*.js'
          ],
          'js/angular/global-faires-map-app.js': [
            'js/angular/global-faires-map-app/faireMapsApp.js',
            'js/angular/global-faires-map-app/*.js'
          ],
        }
      },
    },
    // uglify js
    uglify: {
      js: {
        options: {
          mangle: false,
          banner: '// Compiled file - any changes will be overwritten by grunt task\n',
        },
        files: {
          'js/built.js': 'js/built.js',
          'js/angular/global-faires-map-app.js': 'js/angular/global-faires-map-app.js',
          'js/built-admin-scripts.js': 'js/built-admin-scripts.js'
        }
      },
      libs: {
        options: {
          mangle: false,
          banner: '// Compiled file - any changes will be overwritten by grunt task\n',
          preserveComments: /(?:(^!!)|@(?:license|preserve|cc_on))/
        },
        files: {
          'js/built-libs.js': 'js/built-libs.js'
        }
      }
    },
    // Watch for changes on save and livereload
    watch: {
      dev: {
        files: watchList,
        tasks: ['less:dev', 'concat']
      },
      prod: {
        files: watchList,
        tasks: ['less:prod', 'concat', 'uglify']
      },
      reload: {
        files: watchList,
        tasks: ['less:prod', 'concat', 'uglify'],
        options: {
          livereload: true
        }
      }
    }
  });
  // Load up tasks
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-text-replace');
  // Register the tasks with Grunt
  // To only watch for less changes and process without browser reload type in "grunt"
  grunt.registerTask('default', ['less:prod', 'replace:cachebust', 'concat', 'uglify', 'watch:prod']);
  // To only watch for less changes and process without browser reload type in "grunt dev"
  grunt.registerTask('dev', ['less:dev', 'concat', 'watch:dev']);
  // To watch for less changes and process them with livereload type in "grunt reload"
  grunt.registerTask('reload', ['less:dev', 'concat', 'watch:reload']);
};

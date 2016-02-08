module.exports = function(grunt) {
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
        files: {
          'css/bootstrap.min.css': 'less/bootstrap/bootstrap.less',
          'css/style.css': ['less/global.less', 'less/**/*.less', '!less/bootstrap/*']
        }
      },
      prod: {
        options: {
          compress: true
        },
        files: {
          'css/bootstrap.min.css': 'less/bootstrap/bootstrap.less',
          'css/style.css': ['less/global.less', 'less/**/*.less', '!less/bootstrap/*']
        }
      }
    },
    // Concat js files
    concat: {
      options: {
        banner: '// Compiled file - any changes will be overwritten by grunt task\n\n',
        separator: ';',
        process: function(src, filepath) {
          return '/* ' + filepath + ' */\n' + src;
        }
      },
      dist: {
        files: {
          'js/built.js': ['js/src/misc-libs.js', 'js/src/*.js'],
          'js/built-libs.js': [
            'node_modules/jquery/**/jquery.min.js',
            'node_modules/bootstrap/**/bootstrap.min.js',
            'node_modules/bootstrap-dialog/**/bootstrap-dialog.min.js',
            'node_modules/jquery.cookie/**/jquery-cookie.js',
            'node_modules/fancybox/**/jquery.fancybox.pack.js',
            'node_modules/hammerjs/hammer.min.js',
            'js/libs-src/*.js'
          ],
        }
      },
    },
    // uglify js
    uglify: {
      options: {
        mangle: false
      },
      my_target: {
        files: {
          'js/built.js': 'js/built.js'
        }
      }
    },
    // Watch for changes on save and livereload
    watch: {
      dev: {
        files: ['less/**/*.less', 'js/src/*.js'],
        tasks: ['less:dev', 'concat']
      },
      prod: {
        files: ['less/**/*.less', 'js/src/*.js'],
        tasks: ['less:prod', 'concat', 'uglify']
      },
      reload: {
        files: ['less/**/*.less', 'js/**/*.js'],
        tasks: ['js'],
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
  // Register the tasks with Grunt
  // To only watch for less changes and process without browser reload type in "grunt"
  grunt.registerTask('default', ['less:prod', 'concat', 'uglify', 'watch:prod']);
  // To only watch for less changes and process without browser reload type in "grunt dev"
  grunt.registerTask('dev', ['less:dev', 'concat', 'watch:dev']);
  // To watch for less changes and process them with livereload type in "grunt reload"
  grunt.registerTask('reload', ['less:dev', 'concat', 'watch:dev', 'watch:reload']);
};

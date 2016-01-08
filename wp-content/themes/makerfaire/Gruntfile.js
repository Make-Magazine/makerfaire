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
          'css/bootstrap.min.css': './node_modules/bootstrap/less/bootstrap.less',
          'css/style.css': ['less/global.less', 'less/**/*.less']
        }
      },
      prod: {
        options: {
          compress: true
        },
        files: {
          'css/bootstrap.min.css': './node_modules/bootstrap/less/bootstrap.less',
          'css/style.css': ['less/global.less', 'less/**/*.less']
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
        src: ['js/src/misc-libs.js', 'js/src/*.js'],
        dest: 'js/built.js',
      },
    },
    // Watch for changes on save and livereload
    watch: {
      dev: {
        files: ['less/**/*.less', 'js/src/*.js'],
        tasks: ['less:dev', 'concat']
      },
      prod: {
        files: ['less/**/*.less', 'js/src/*.js'],
        tasks: ['less:prod', 'concat']
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
  // Register the tasks with Grunt
  // To only watch for less changes and process without browser reload type in "grunt"
  grunt.registerTask('default', ['less:prod', 'concat', 'watch:prod']);
  // To only watch for less changes and process without browser reload type in "grunt"
  grunt.registerTask('dev', ['less:dev', 'concat', 'watch:dev']);
  // To watch for less changes and process them with livereload type in "grunt reload"
  grunt.registerTask('reload', ['less', 'watch:reload']);
};

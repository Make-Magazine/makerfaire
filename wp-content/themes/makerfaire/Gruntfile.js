module.exports = function(grunt) {
  // All configurations go here
  grunt.initConfig({
    // Reads the package.json file
    pkg: grunt.file.readJSON('package.json'),
    // Compile the less to css
    less: {
      development: {
        options: {
          compress: true
        },
        files: {
          'css/style.css': 'less/style.less',
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
      css: {
        files: ['less/**/*.less'],
        tasks: ['less']
      },
      js: {
        files: ['js/**/*.js'],
        tasks: ['concat']
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
  grunt.registerTask('default', ['less', 'concat', 'watch:css', 'watch:js']);
  // To watch for less changes and process them with livereload type in "grunt reload"
  grunt.registerTask('reload', ['less', 'watch:reload']);
};

module.exports = (grunt) ->
  grunt.initConfig
    phpunit:
      test: {}

    watch:
      options:
        atBegin: true
      test:
        files: ['Gruntfile.coffee', 'src/**/*.php', 'test/**/*.php']
        tasks: 'test'

  grunt.loadNpmTasks 'grunt-contrib-watch'
  grunt.loadNpmTasks 'grunt-phpunit'

  grunt.registerTask 'test', ['phpunit']

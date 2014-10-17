module.exports = (grunt) ->
	grunt.initConfig
		pkg: grunt.file.readJSON("package.json")
		directory:
			components: "Resources/Public/WebComponents"
			build: "Resources/Public/Build"
			source: "Resources/Public/Source"
			temporary: "Temporary"

	############################ Assets ############################

	##
	# Assets: clean up environment
	##
		clean:
			temporary:
				src: ["Temporary"]

	##
	# Assets: copy some files to the distribution dir
	##
		copy:
			js:
				files: [
					# includes files within path
					expand: true
					flatten: true
					src: [
						"<%= directory.components %>/datatables/media/js/jquery.dataTables.*"
					]
					dest: "<%= directory.build %>/JavaScript/"
					filter: "isFile"
				]
			css:
				files: [
					# includes files within path
					expand: true
					flatten: true
					src: [
						"<%= directory.components %>/datatables/media/css/*"
					]
					dest: "<%= directory.build %>/StyleSheets/"
					filter: "isFile"
				]

	##
	# Assets: optimize assets for the web
	##
		pngmin:
			images:
				options:
					ext: '.png'
				files: [
					src: '<%= directory.source %>/Images/*.png'
					dest: '<%= directory.temporary %>'
				]

		gifmin:
			src: [
				'<%= directory.ext_jquerycolorbox %>/css/images/*.gif'
			],
			dest: '<%= directory.temporary %>'

		jpgmin:
			src: [
				'<%= directory.ext_jquerycolorbox %>/css/images/*.jpg'
			],
			dest: '<%= directory.temporary %>'

	############################ StyleSheets ############################

	##
	# StyleSheet: compiling to CSS
	##
		sass: # Task
			build: # Target
				options: # Target options
				# output_style = expanded or nested or compact or compressed
					style: "expanded"

				files:
				# must comme last in the concatation process
					"<%= directory.temporary %>/Source/zzz_main.css": "<%= directory.source %>/StyleSheets/Sass/main.scss"


	##
	# StyleSheet: minification of CSS
	##
		cssmin:
			options: {}
			build:
				files:
					"<%= directory.build %>/StyleSheets/site.min.css": [
						"<%= directory.temporary %>/Build/*"
					]


	############################ JavaScript ############################

	##
	# JavaScript: check javascript coding guide lines
	##
		jshint:
			files: [
				"<%= directory.source %>/JavaScript/*.js"
			]

			options:
			# options here to override JSHint defaults
				curly: true
				eqeqeq: true
				immed: true
				latedef: true
				newcap: true
				noarg: true
				sub: true
				undef: true
				boss: true
				eqnull: true
				browser: true
				globals:
					jQuery: true
					console: true
					module: true
					document: true
					Modernizr: true

	##
	# JavaScript: minimize javascript
	##
		uglify:
			options:
				banner: "/*! <%= pkg.name %> <%= grunt.template.today(\"dd-mm-yyyy\") %> */\n"
			dist:
				files:
					"<%= directory.temporary %>/main.min.js": ["<%= jshint.files %>"]

	########## concat css + js ############
		concat:
#			css:
#				src: [
#					"<%= directory.temporary %>/Source/*.css"
#					"<%= directory.source %>/StyleSheets/**/*.css"
#				],
#				dest: "<%= directory.temporary %>/Build/site.css",
			options:
				separator: "\n"
			js:
				src: [
					"<%= directory.components %>/datatables/media/js/jquery.dataTables.min.js"
					"<%= directory.temporary %>/main.min.js"
				]
				dest: "<%= directory.build %>/JavaScript/app.min.js"

	########## Watcher ############
		watch:
#			css:
#				files: [
#					"<%= directory.source %>/StyleSheets/**/*.scss"
#					"<%= directory.source %>/StyleSheets/**/*.css"
#				]
#				tasks: ["build-css"]
			js:
				files: ["<%= jshint.files %>"]
				tasks: ["build-js"]


	########## Help ############
	grunt.registerTask "help", "Just display some helping output.", () ->
		grunt.log.writeln "Usage:"
		grunt.log.writeln ""
		grunt.log.writeln "- grunt watch        : watch your file and compile as you edit"
		grunt.log.writeln "- grunt build        : build your assets ready to be deployed"
		grunt.log.writeln "- grunt build-css    : only build your css files"
		grunt.log.writeln "- grunt build-js     : only build your js files"
		grunt.log.writeln "- grunt build-icons  : only build icons"
		grunt.log.writeln ""
		grunt.log.writeln "Use grunt --help for a more verbose description of this grunt."
		return

	# Load Node module
	grunt.loadNpmTasks "grunt-contrib-uglify"
	grunt.loadNpmTasks "grunt-contrib-jshint"
	grunt.loadNpmTasks "grunt-contrib-watch"
	grunt.loadNpmTasks "grunt-contrib-concat"
	grunt.loadNpmTasks "grunt-contrib-sass";
	grunt.loadNpmTasks "grunt-contrib-cssmin"
	grunt.loadNpmTasks "grunt-contrib-copy"
	grunt.loadNpmTasks "grunt-contrib-clean"
	grunt.loadNpmTasks "grunt-string-replace"
	grunt.loadNpmTasks "grunt-imagine"
	grunt.loadNpmTasks "grunt-pngmin"

	# Alias tasks
	grunt.task.renameTask("string-replace", "import")

	# Tasks
	#grunt.registerTask "build", ["build-js", "build-css", "build-icons"]
	grunt.registerTask "build", ["build-js"]
	grunt.registerTask "build-js", ["jshint", "uglify", "concat:js", "clean"]
#	grunt.registerTask "build-css", ["sass", "concat:css", "import", "cssmin", "clean"]
#	grunt.registerTask "build-icons", ["pngmin", "gifmin", "jpgmin","copy", "clean"]
#	grunt.registerTask "css", ["build-css"]
#	grunt.registerTask "js", ["build-js"]
#	grunt.registerTask "icons", ["build-icons"]
	grunt.registerTask "default", ["help"]
	return
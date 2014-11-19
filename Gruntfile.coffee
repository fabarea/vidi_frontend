module.exports = (grunt) ->
	grunt.initConfig
		pkg: grunt.file.readJSON("package.json")
		directory:
			components: "Resources/Public/WebComponents"
			build: "Resources/Public/Build"
			source: "Resources/Public/Source"

	############################ Assets ############################

	##
	# Assets: clean up environment
	##
		clean:
			temporary:
				src: [".tmp"]

	##
	# Assets: copy some files to the distribution dir
	##
#		copy:
#			js:
#				files: [
#					# includes files within path
#					expand: true
#					flatten: true
#					src: [
#						"<%= directory.components %>/datatables/media/js/jquery.dataTables.*"
#					]
#					dest: "<%= directory.build %>/JavaScript/"
#					filter: "isFile"
#				]
#			css:
#				files: [
#					# includes files within path
#					expand: true
#					flatten: true
#					src: [
#						"<%= directory.components %>/datatables/media/css/*"
#					]
#					dest: "<%= directory.build %>/StyleSheets/"
#					filter: "isFile"
#				]

	##
	# Assets: optimize assets for the web
	##
		pngmin:
			images:
				options:
					ext: '.png'
				files: [
					src: "<%= directory.source %>/Images/*.png"
					dest: "<%= directory.build %>/Images"
				]
			images_datatables:
				options:
					ext: '.png'
				files: [
					src: "<%= directory.components %>/datatables/media/images/*.png"
					dest: "<%= directory.build %>/Images"
				]
			images_bootstrap:
				options:
					ext: '.png'
				files: [
					src: "<%= directory.components %>/datatables-plugins/integration/bootstrap/images/*.png"
					dest: "<%= directory.build %>/Images/Bootstrap"
				]

	############################ StyleSheets ############################

	##
	# StyleSheet: minification of CSS
	##
		cssmin:
			options: {}
			css:
				files:
					"<%= directory.build %>/StyleSheets/vidi_frontend.min.css": ".tmp/replace/jquery.dataTables.css"
			css_bootstrap:
				files:
					"<%= directory.build %>/StyleSheets/vidi_frontend.bootstrap.min.css": [
						".tmp/replace/dataTables.bootstrap.css"
						"<%= sass.css.files[0].dest %>"
					]

	##
	# StyleSheet: compiling to CSS
	##
		sass:
			css:
				options:
				# output_style = expanded or nested or compact or compressed
					style: "expanded"
					sourcemap: 'none'

				files: [
					src: "<%= directory.source %>/StyleSheets/Sass/main.scss"
					dest: ".tmp/sass/main.css"
				]

	##
	# StyleSheet: importation of "external" stylesheets form third party extensions.
	##
		replace:
			css:
				files: [
					expand: true
					flatten: true
					src: "<%= directory.components %>/datatables/media/css/jquery.dataTables.css"
					dest: ".tmp/replace"
				]
				options:
					replacements: [
						pattern: /\.\.\/images/ig,
						replacement: '../Images'
					]
			css_bootstrap:
				files: [
					expand: true
					flatten: true
					src: "<%= directory.components %>/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css"
					dest: ".tmp/replace"
				]
				options:
					replacements: [
						pattern: /\.\.\/images/ig,
						replacement: '../Images/Bootstrap'
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
				loopfunc: true
				globals:
					jQuery: true
					console: true
					module: true
					Uri: true
					define: true
					require: true
					VidiFrontend: true
					VS: true
					_: true

	##
	# JavaScript: minimize javascript
	##
		uglify:
			js:
				files: [
					src: "<%= jshint.files %>"
					dest: ".tmp/uglify/main.min.js"
				]
			js_bootstrap:
				files: [
					src: "<%= directory.components %>/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.js"
					dest: ".tmp/uglify/dataTables.bootstrap.min.js"
				]

	########## concat css + js ############
		concat:
			options:
				separator: "\n\n"
			js:
				src: [
					"<%= directory.components %>/datatables/media/js/jquery.dataTables.js"
					"<%= jshint.files %>"
				]
				dest: "<%= directory.build %>/JavaScript/vidi_frontend.js"
			js_bootstrap:
				src: [
					"<%= directory.components %>/datatables/media/js/jquery.dataTables.js"
					"<%= uglify.js_bootstrap.files[0].src %>"
					"<%= jshint.files %>"
				]
				dest: "<%= directory.build %>/JavaScript/vidi_frontend.bootstrap.js"
			js_min:
				src: [
					"<%= directory.components %>/datatables/media/js/jquery.dataTables.min.js"
					"<%= uglify.js.files[0].dest %>"
				]
				dest: "<%= directory.build %>/JavaScript/vidi_frontend.min.js"
			js_bootstrap_min:
				src: [
					"<%= directory.components %>/datatables/media/js/jquery.dataTables.min.js"
					"<%= uglify.js_bootstrap.files[0].dest %>"
					"<%= uglify.js.files[0].dest %>"
				]
				dest: "<%= directory.build %>/JavaScript/vidi_frontend.bootstrap.min.js"

	########## Watcher ############
		watch:
			css:
				files: [
					"<%= directory.source %>/StyleSheets/**/*.scss"
				]
				tasks: ["build-css"]
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
		grunt.log.writeln "- grunt clean        : clean behind you the temporary files"
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
	grunt.task.renameTask("string-replace", "replace")

	# Tasks
	grunt.registerTask "build", ["build-js", "build-css", "build-icons"]
	grunt.registerTask "build-js", ["jshint", "uglify", "concat"]
	grunt.registerTask "build-css", ["sass", "replace", "cssmin"]
	grunt.registerTask "build-icons", ["pngmin"]
	grunt.registerTask "default", ["help"]
	return
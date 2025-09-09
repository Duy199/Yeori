// Load Grunt
const sass = require('sass');
module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		// Tasks
		sass: {                                   
		    dist: {                               // Target 
		    	options: {              
					implementation: sass,            // Target options 
		        	style: 'expanded',
		        	// lineNumbers: true, // 1
  					sourceMap: true
		      	},
		      	files: {                                   // Dictionary of files 
		        	'css/duydev.css': 'sass/all.scss',        // 'destination': 'source' 		        	
		      	}
		    }
		},
		cssmin: {
			css: {
				src: 'css/duydev.css',
				dest: 'css/duydev.min.css'
			},
		},
		concat: {
			js_all: {
				src: [
					/* lib */
					'js/lib/swiper-bundle.min.js',

					/* custom */
					'js/duydev/duydev.js',

					/* widgets */
					'js/widgets/aText.js',
					'js/widgets/yeori-skin.js',
				],
				dest: 'js/duydev.js'
			}
		},		
		uglify: {
			js_all: {
				src: 'js/duydev.js',
				dest: 'js/duydev.min.js',
			},
		},
		watch: {            
            css: {
                files: ['sass/*.scss', 'sass/**/*.scss'],
                tasks: ['sass:dist', 'cssmin:css'],
                options: {
			      spawn: false,
			    }
            },
            js: {
                files: ['js/duydev/*.js', 'js/widgets/*.js'],
                tasks: ['concat:js_all', 'uglify:js_all'],
                options: {
			      spawn: false,
			    }
            }
        }
	});
	
	// Load Grunt plugins
	var dir_node_modules = '../../../../../../node_modules/';
	
	grunt.loadNpmTasks(dir_node_modules + 'grunt-contrib-concat');
	grunt.loadNpmTasks(dir_node_modules + 'grunt-contrib-cssmin');
	grunt.loadNpmTasks(dir_node_modules + 'grunt-sass'); 	
	grunt.loadNpmTasks(dir_node_modules + 'grunt-contrib-uglify');
	grunt.loadNpmTasks(dir_node_modules + 'grunt-contrib-watch');
	// Register Grunt tasks
	grunt.registerTask('default', ['sass', 'cssmin', 'concat', 'uglify', 'watch']);
};
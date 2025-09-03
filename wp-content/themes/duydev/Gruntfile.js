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
		        	'assets/css/all.css': 'assets/sass/all.scss',        // 'destination': 'source' 		        	
		      	}
		    }
		},
		cssmin: {
			css: {
				src: 'assets/css/all.css',
				dest: 'assets/css/all.min.css'
			},
		},
		concat: {
			js_all: {
				src: [
					/* lib */
					// 'assets/js/lib/swiper-bundle.min.js',
					/* end lib */
					'assets/js/duydev/duydev.js',
					'assets/js/duydev/loader.js',
					'assets/js/duydev/cursor.js',
					'assets/js/duydev/home.js',
					'assets/js/duydev/service-page.js',
					'assets/js/duydev/single-service.js',
					'assets/js/duydev/team-page.js',
					'assets/js/duydev/single-team.js',
			 	],
				dest: 'assets/js/all.js'
			}
		},		
		uglify: {
			js_all: {
				src: 'assets/js/all.js',
				dest: 'assets/js/all.min.js',
			},
		},
		watch: {            
            css: {
                files: ['assets/sass/*.scss', 'assets/sass/**/*.scss'],
                tasks: ['sass:dist', 'cssmin:css'],
                options: {
			      spawn: false,
			    }
            },
            js: {
                files: ['assets/js/duydev/*.js'],
                tasks: ['concat:js_all', 'uglify:js_all'],
                options: {
			      spawn: false,
			    }
            }
        }
	});
	
	// Load Grunt plugins
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-sass'); 	
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	// Register Grunt tasks
	grunt.registerTask('default', ['sass', 'cssmin', 'concat', 'uglify', 'watch']);
};
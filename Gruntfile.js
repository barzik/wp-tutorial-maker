module.exports = function(grunt) {
    grunt.initConfig({ //object with all tasks.
        phpcs: {
            application: {
                src: ['./public/**/*.php' , './admin/**/*.php']
            },
            options: {
                standard: 'WordPress',
                errorSeverity: 1
            }
        },
        phpunit: {
            dir: 'tests/',
            options: {
                configuration: 'phpunit.xml'
            }
        },
		 sass: {                              // Task
			dist: {                            // Target
			  options: {                       // Target options
				style: 'expanded'
			  },
			  files: {                         // Dictionary of files
				'public/assets/css/public.css': 'public/assets/css/public.scss',       // 'destination': 'source'
			  }
			}
       }		
    });
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpunit');
	
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.registerTask('default', ['sass']);
};

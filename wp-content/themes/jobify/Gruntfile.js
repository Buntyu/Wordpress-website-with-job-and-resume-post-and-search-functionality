'use strict';
module.exports = function(grunt) {

	grunt.initConfig({

		dirs: {
			js: 'js',
			css: 'css',
			wp_job_manager: 'inc/integrations/wp-job-manager/js',
		},

		watch: {
			options: {
				livereload: true,
			},
			js: {
				files: [
					'Gruntfile.js',
					'js/vendor/*.js',
					'js/app/*.js',
					'inc/integrations/**/*.js'
				],
				tasks: ['uglify', 'clean' ]
			},
			css: {
				files: [
					'css/sass/*/*.scss',
					'css/sass/*.scss'
				],
				tasks: ['sass', 'concat', 'cssmin', 'clean']
			}
		},

		// uglify to concat, minify, and make source maps
		uglify: {
			dist: {
				options: {
					sourceMap: true
				},
				files: {
					'js/vendor.min.js': [ 'js/vendor/*.js' ],
					'inc/integrations/wp-job-manager/js/wp-job-manager.js': [
						'inc/integrations/wp-job-manager/js/source/wp-job-manager.js',
						'inc/integrations/wp-job-manager-apply-linkedin/js/wp-job-manager-apply-linkedin.js'
					],
					'inc/integrations/wp-job-manager/js/wp-job-manager-map.min.js': [
						'inc/integrations/wp-job-manager/js/vendor/*.js',
						'inc/integrations/wp-job-manager/js/source/wp-job-manager-map.js'
					],
					'js/jobify.min.js': [
						'js/vendor.min.js',
						'inc/integrations/wp-job-manager/js/wp-job-manager.js',
						'js/app/jobify.js'
					],
				}
			}
		},

		sass: {
			dist: {
				files: {
					'css/style.css' : 'css/sass/style.scss'
				}
			}
		},

		concat: {
			dist: {
				files: {
					'css/style.css': [ 'css/_theme.css', 'css/vendor/*.css', 'css/style.css']
				}
			}
		},

		cssmin: {
			dist: {
				files: {
					'style.css': [ 'css/style.css' ]
				}
			}
		},

		clean: {
			dist: [
				'js/vendor.min.js',
				'js/vendor.js.map',
				'css/vendor.css'
			]
		},

		cssjanus: {
			theme: {
				options: {
					swapLtrRtlInUrl: false
				},
				files: [
					{
						src: 'css/style.min.css',
						dest: 'css/style.min-rtl.css'
					}
				]
			}
		},

		makepot: {
			theme: {
				options: {
					type: 'wp-theme'
				}
			}
		},

		exec: {
			txpull: {
				cmd: 'tx pull -a --minimum-perc=75'
			},
			txpush_s: {
				cmd: 'tx push -s'
			},
		},

		potomo: {
			dist: {
				options: {
					poDel: false // Set to true if you want to erase the .po
				},
				files: [{
					expand: true,
					cwd: 'languages',
					src: ['*.po'],
					dest: 'languages',
					ext: '.mo',
					nonull: true
				}]
			}
		}

	});

	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-cssjanus' );
	grunt.loadNpmTasks( 'grunt-exec' );
	grunt.loadNpmTasks( 'grunt-potomo' );

	// register task
	grunt.registerTask('default', ['watch']);

	grunt.registerTask( 'tx', ['exec:txpull', 'potomo']);
	grunt.registerTask( 'makeandpush', ['makepot', 'exec:txpush_s']);

	grunt.registerTask('build', ['uglify', 'sass', 'concat', 'cssmin', 'clean', 'makepot', 'tx', 'makeandpush','cssjanus' ]);
};

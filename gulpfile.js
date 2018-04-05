var gulp = require('gulp'); 

gulp.task('modules_js', function() {
    sources = [
      './node_modules/jquery/dist/jquery.min.js',
      './node_modules/jquery-ui-dist/jquery-ui.min.js',
      './node_modules/fullcalendar/dist/fullcalendar.min.js',
      './node_modules/moment/min/moment.min.js',
	  './node_modules/daterangepicker/daterangepicker.min.js',
	  './node_modules/boostrap/dist/js/bootstrap.min.js',

    ]
    gulp.src( sources ).pipe(gulp.dest('./public/assets/js/'));
});

gulp.task('modules_css', function() {
    sources = [
		'./node_modules/fullcalendar/dist/fullcalendar.min.css',
		'./node_modules/daterangepicker/daterangepicker.css',
		'./node_modules/boostrap/dist/js/bootstrap.min.css',

    ]
    gulp.src( sources ).pipe(gulp.dest('./public/assets/css/'));
});

gulp.task('copy-modules', ['modules_js']);
gulp.task('copy-modules', ['modules_css']);
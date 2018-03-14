var gulp = require('gulp');
var fileinclude  = require('gulp-file-include');
//var minifyHtml = require("gulp-minify-html");

gulp.task('fileinclude', function() {
//  	gulp.src(['./admin/**.html','./admin/**/**.html','./admin/**/**.css','./admin/**/**.js'])
		gulp.src(['./adminTem/**/**.html','./adminTem/**.html'])
        .pipe(fileinclude({
          prefix: '@@',
          basepath: '@file'
        }))
    .pipe(gulp.dest('./'));    
}); 
//gulp.task('init', function() {
//  gulp.src(['./pages/**.html','./pages/**/**.html','./pages/**/**.css','./pages/**/**.js'])
//      
//      .pipe(fileinclude({
//        prefix: '@@',
//        basepath: '@file'
//      }))
//      .pipe(minifyHtml())
//  .pipe(gulp.dest('./v2/'));    
//});
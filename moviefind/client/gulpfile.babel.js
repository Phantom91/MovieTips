/* Author Darius
 *
 * Directory structure:
 *
 * project/
 *        dev/
 *           package.json
 *           gulpfile.js
 *           node_modules/
 *        build/
 *
 *  Builds two files: a vendor file for big dependencies and an app file
 *  Watch SCSS file and compile them to css
 *  Watch JS files and transpile them from ES6 -> ES5 for browsers compatibility
 *  Check JS files for syntax error
 *  for just your app.
 */

const SOURCE_PATH = "./sources",
    BUILD_PATH = "./webapp";
    
import gulp from 'gulp';
import { configure } from 'babelify';
import browserify from 'browserify';
import source from 'vinyl-source-stream';
import sass, { logError } from 'gulp-sass';
import jshint, { reporter } from 'gulp-jshint';
import watch from 'gulp-watch';
import changed from 'gulp-changed';
import autoprefixer from 'gulp-autoprefixer';
import htmlMinifier from 'gulp-html-minifier';
import minify from 'gulp-minify';
import concat from 'gulp-concat';
import order from 'gulp-order';
import livereload from "gulp-livereload";
import jsonminify from "gulp-json-minify";
import util from "gulp-util";
import plumber from "gulp-plumber";

gulp.task('concat-vendors', () => {
    gulp.src(SOURCE_PATH + '/vendor/**/*.js')
        .pipe(order(['jquery/jquery.min.js', 'bootstrap', 'couterup.min.js', '*.js']))
        .pipe(concat("vendor.js"))
        .pipe(minify())
        .pipe(gulp.dest(BUILD_PATH + '/vendor'))
        .pipe(plumber(function (error) {
            gutil.log(error.message);
            this.emit('end');
        }));  
});

// configure the jshint task
gulp.task('jshint', () => {
  return gulp.src(SOURCE_PATH + '/js/**/*.js')
    .pipe(jshint())
    .pipe(reporter('jshint-stylish'))
    .pipe(plumber(function (error) {
        gutil.log(error.message);
        this.emit('end');
    }));
});

// Sass - Compile Sass files into CSS
gulp.task('sass', () => {
	gulp.src(SOURCE_PATH + '/scss/**/*.scss')
        .pipe(changed(SOURCE_PATH + '/css/'))
        .pipe(sass(
            { 
                outputStyle: 'expanded',
                includePaths: ['./node_modules/bootstrap/scss/', './node_modules/font-awesome/scss/', './node_modules/roboto-fontface/css/roboto/sass/', './node_modules/sass-mediaqueries']
            }
        ))
        .pipe(autoprefixer())
        .pipe(plumber(function (error) {
            gutil.log(error.message);
            this.emit('end');
        }))
        .pipe(gulp.dest(BUILD_PATH + '/css/'))
});

gulp.task('build-js', () => {
    var b = browserify({
        extensions: ['.js'],
        debug: true,
    });

    b.transform(configure({
        presets: ["env"],
        babelrc: true
	  }))
     .plugin('minifyify', {
         map: 'app.map',
         uglify: {
             mangle: false,
             compress: {
                drop_debugger: false,
                drop_console: false,
             }
         },
         output: BUILD_PATH + '/js/app.map'
     })
     .add(SOURCE_PATH + "/js/components/main/MainApp.js")
     .bundle()
     .pipe(source('app.min.js'))
     .pipe(gulp.dest(BUILD_PATH + "/js"))
     .pipe(plumber(function (error) {
        gutil.log(error.message);
        this.emit('end');
    }));
});

gulp.task('move-translations', () => {
    gulp.src(SOURCE_PATH + "/translations/**/*.json")
        .pipe(jsonminify())
        .pipe(gulp.dest(BUILD_PATH + "/translations"))
        .pipe(plumber(function (error) {
            gutil.log(error.message);
            this.emit('end');
        }));
})

gulp.task('css-scss', () => {

});

gulp.task('move-templates', () => {
    gulp.src([SOURCE_PATH + "/**/*.ejs"])
      .pipe(htmlMinifier({collapseWhitespace: true}))
      .pipe(gulp.dest(BUILD_PATH))
      .pipe(plumber(function (error) {
        gutil.log(error.message);
        this.emit('end');
    }));
});

gulp.task('reload', () => livereload());

gulp.task('watch', () => {
	livereload.listen(28001);
	const files = [SOURCE_PATH + "/js/**/*.js", SOURCE_PATH + "/scss/**/*.scss", SOURCE_PATH + "/**/*.ejs", "../../server/**/*.php"];
    watch(files, () => {
        console.log('refresing..');
        gulp.start(['jshint', 'build-js', 'sass', 'move-templates', 'move-translations', 'reload']);
	});
});

gulp.task('default', ['watch', 'jshint', 'concat-vendors', 'build-js', 'sass', 'move-templates', 'move-translations', 'reload']);
var gulp = require('gulp');
var file = require('gulp-file');
var sass = require('gulp-sass');
var copy = require('gulp-copy');
var clean = require('gulp-clean');
var zip = require('gulp-zip');
var pkg = require('./package.json');

gulp.task('default', function() {
    // place code for your default task here
});

gulp.task('sass', function(){
    return gulp.src('src/scss/**/*.scss')
        .pipe(sass()) // Using gulp-sass
        .pipe(gulp.dest('src/css'))
});
gulp.task('watch', function(){
    gulp.watch('app/scss/**/*.scss', ['sass']);
});

// INIT TASKS
// creates info.xml and other modules files (php, css, js)
gulp.task('init-info-xml', function () {
    var infoXml =
        '<?xml version="1.0" encoding="UTF-8"?>\n' +
        '<module><name>'+pkg.name+'</name><description/><type/><alias>'+pkg.name+'</alias></module>\n';

    return file('info.xml', infoXml, {src: true})
        .pipe(gulp.dest('src/'));
});
gulp.task('init-php-input', function () {
    return file(pkg.name+'_input.php', '// '+pkg.name+'_input\n?>\n<?php', {src: true})
        .pipe(gulp.dest('src/php/'));
});
gulp.task('init-php-output', function () {
    return file(pkg.name+'_output.php', '<?php\n// '+pkg.name+'_output\n?>', {src: true})
        .pipe(gulp.dest('src/php/'));
});
gulp.task('init-js', function () {
    return file(pkg.name+'.js', '/* '+pkg.name+' */', {src: true})
        .pipe(gulp.dest('src/js/'));
});
gulp.task('init-sass', function () {
    return file(pkg.name+'.scss', '/* '+pkg.name+' */', {src: true})
        .pipe(gulp.dest('src/scss/'));
});
gulp.task('init', ['init-info-xml', 'init-php-input', 'init-php-output', 'init-js', 'init-sass']);

/* todo
    - bower dependencies in dist
    - vendor?
 */

// BUILD
gulp.task('clean-build', function () {
    return gulp.src('dist/**/*', {read: false})
        .pipe(clean());
});
gulp.task('zip-build', function () {
    return gulp.src(['dist/**/*','!dist/**/*.zip'])
        .pipe(zip(pkg.name+'.zip'))
        .pipe(gulp.dest('dist'));
});
gulp.task('build-js', function(){
    return gulp.src(['src/js/*'])
        .pipe(gulp.dest('dist/js/'));
});
gulp.task('build-css', ['sass'], function(){
    return gulp.src(['src/css/**/*.css'])
        .pipe(gulp.dest('dist/css/'));
});
gulp.task('build-php', function(){
    return gulp.src(['src/php/**/*.php'])
        .pipe(gulp.dest('dist/php/'));
});
gulp.task('build-template', function(){
    return gulp.src(['src/template/*'])
        .pipe(gulp.dest('dist/template/'));
});
gulp.task('build-info', function(){
    return gulp.src(['src/info.xml'])
        .pipe(gulp.dest('dist/'));
});
// todo async, etc...
gulp.task('build', ['clean-build', 'build-info', 'build-js','build-css','build-php','build-template'], function(){
    return gulp.src(['dist/**/*','!dist/**/*.zip'])
        .pipe(zip(pkg.name+'.zip'))
        .pipe(gulp.dest('dist'));
});
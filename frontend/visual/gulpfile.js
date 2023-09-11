const gulp = require('gulp');
const rename = require('gulp-rename');
const imagemin = require('gulp-imagemin');
const svgo = require('gulp-svgo');

gulp.task('images', () => {
    return gulp.src('./src/images/*.{png,jpg,webp,jpeg}')
        .pipe(imagemin())
        .pipe(rename((path) => {
            // для винды вот такой вид path.dirname.split('\\')[0]
            path.dirname = path.dirname.split('/')[0];
        }))
        .pipe(gulp.dest('../../public/images/visual'));
});

gulp.task('icons', () => {
    return gulp.src('./src/icons/*.svg')
        .pipe(svgo({
            plugins: [{
                removeStyleElement: false
            }]
        }))
        .pipe(rename((path) => {
            // для винды вот такой вид path.dirname.split('\\')[0]
            path.dirname = path.dirname.split('/')[0];
        }))
        .pipe(gulp.dest('../../public/webicons/visual'));
});

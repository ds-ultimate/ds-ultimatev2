const mix = require('laravel-mix');
require('laravel-mix-purgecss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('resources/sass/plugins/flag-icon-css/flags', 'public/images/flags', false)
    .options({
        processCssUrls: false
    })
    .purgeCss({
        whitelistPatterns: [/(flag-icon.*|col-.*|.*pagination.*|.*page.*)/],
    });

mix.copyDirectory('node_modules/tinymce/plugins', 'public/plugin/tinymce/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/plugin/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/plugin/tinymce/themes');
mix.copy('node_modules/tinymce/jquery.tinymce.min.js', 'public/plugin/tinymce/jquery.tinymce.min.js');
mix.copy('node_modules/tinymce/tinymce.min.js', 'public/plugin/tinymce/tinymce.min.js');

mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/css/base/jquery.fonticonpicker.min.css', 'public/plugin/fontIconPicker/jquery.fonticonpicker.min.css');
mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/css/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css', 'public/plugin/fontIconPicker/jquery.fonticonpicker.bootstrap.min.css');
mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/js/jquery.fonticonpicker.min.js', 'public/plugin/fontIconPicker/jquery.fonticonpicker.min.js');
mix.copyDirectory('node_modules/@fonticonpicker/fonticonpicker/dist/fonts', 'public/fonts');
mix.copyDirectory('node_modules/bootstrap-colorpicker/dist', 'public/plugin/bootstrap-colorpicker/');

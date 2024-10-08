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

let safelist =
[
    /flag-icon.*/,
    /col-.*/,
    /.*pagination.*/,
    /.*page.*/,
    /.*popover.*/,
    /.*datepicker.*/,
    /.*tooltip.*/,
    /.*cookie-consent.*/,
    /.*select2.*/,
    "arrow",
    "fade",
    "row",
    "font-weight-bold",
    "px-3",
    "pr-3",
    "fa-volume-mute",
    "mx-1",
    /fa-.*/,
    "pull-left",
    "dataTables_wrapper",
    "selectAll",
    /.*modal.*/,
];
mix.js('resources/js/app.js', 'public/js');
mix.js('resources/js/customCode.js', 'public/js');
mix.sass('resources/sass/app.scss', 'public/css')
    .options({
        processCssUrls: false
    })
    .purgeCss({
        safelist: safelist,
    });
    
mix.sass('resources/sass/dark.scss', 'public/css')
    .options({
        processCssUrls: false
    })
    .purgeCss({
        safelist: safelist,
    });

mix.sass('resources/sass/admin_sidebar.scss', 'public/css')
    .options({
        processCssUrls: false
    })
    .purgeCss({
        safelist: safelist,
    });

mix.copy('resources/sass/plugins/flag-icon-css/flags', 'public/images/flags', false);


/*
 * jqueryUi
 */
mix.copy('resources/plugins/jquery-ui/jquery-ui.min.js', 'public/plugin/jquery-ui/jquery-ui.min.js');
mix.copy('resources/plugins/jquery-ui/light/jquery-ui.min.css', 'public/plugin/jquery-ui/light/jquery-ui.min.css');
mix.copyDirectory('resources/plugins/jquery-ui/light/images', 'public/plugin/jquery-ui/light/images');
mix.copy('resources/plugins/jquery-ui/dark/jquery-ui.min.css', 'public/plugin/jquery-ui/dark/jquery-ui.min.css');
mix.copyDirectory('resources/plugins/jquery-ui/dark/images', 'public/plugin/jquery-ui/dark/images');


/*
 * Tinymce (wysiwyg editor for backend)
 */
mix.copyDirectory('node_modules/tinymce/icons', 'public/plugin/tinymce/icons');
mix.copyDirectory('node_modules/tinymce/plugins', 'public/plugin/tinymce/plugins');
mix.copyDirectory('node_modules/tinymce/skins', 'public/plugin/tinymce/skins');
mix.copyDirectory('node_modules/tinymce/themes', 'public/plugin/tinymce/themes');
mix.copy('node_modules/tinymce/jquery.tinymce.min.js', 'public/plugin/tinymce/jquery.tinymce.min.js');
mix.copy('node_modules/tinymce/tinymce.min.js', 'public/plugin/tinymce/tinymce.min.js');


/*
 * Fonticonpicker
 */
mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/css/base/jquery.fonticonpicker.min.css', 'public/plugin/fontIconPicker/jquery.fonticonpicker.min.css');
mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/css/themes/bootstrap-theme/jquery.fonticonpicker.bootstrap.min.css', 'public/plugin/fontIconPicker/jquery.fonticonpicker.bootstrap.min.css');
mix.copy('node_modules/@fonticonpicker/fonticonpicker/dist/js/jquery.fonticonpicker.min.js', 'public/plugin/fontIconPicker/jquery.fonticonpicker.min.js');
mix.copyDirectory('node_modules/@fonticonpicker/fonticonpicker/dist/fonts', 'public/fonts');


/*
 * Colour Picker
 */
mix.copy('node_modules/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css', 'public/plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.css');
mix.copy('node_modules/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js', 'public/plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.js');


/*
 * Date Picker
 */
mix.copy('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js', 'public/plugin/bootstrap-datepicker/bootstrap-datepicker.min.js');


/*
 * Select2 + theme
 * Theme / css is integrated into the main css now
 */
mix.copy('node_modules/select2/dist/js/select2.full.min.js', 'public/plugin/select2/select2.full.min.js');


/*
 * Datatables
 */

mix.scripts([
    'node_modules/datatables.net/js/jquery.dataTables.js',
    'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js',
    'node_modules/datatables.net-responsive/js/dataTables.responsive.js',
    'node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.js',
    'node_modules/datatables.net-buttons/js/dataTables.buttons.js',
    'node_modules/datatables.net-buttons/js/buttons.colVis.js',
    'node_modules/datatables.net-buttons/js/buttons.html5.js',
    'node_modules/datatables.net-buttons/js/buttons.print.js',
    'node_modules/datatables.net-buttons-bs4/js/buttons.bootstrap4.js',
    'node_modules/datatables.net-select/js/dataTables.select.js',
    'node_modules/datatables.net-select-bs4/js/select.bootstrap4.js',
], 'public/js/datatables.min.js');

mix.styles([
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
    'node_modules/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
    'node_modules/datatables.net-select-bs4/css/select.bootstrap4.min.css',
], 'public/css/datatables.min.css');


/*
 * DrawerJS
 */

mix.copy([
    'node_modules/@ds-ultimate/drawerjs/dist/drawerJs.min.css',
    'node_modules/@ds-ultimate/drawerjs/dist/drawerJs.standalone.min.js'
], 'public/plugin/drawerJS/');

mix.copyDirectory('node_modules/@ds-ultimate/drawerjs/dist/assets/', 'public/plugin/drawerJS/assets');


/**
 * Font awesome
 */

mix.sass('resources/sass/fontawesome.scss', 'public/css');

mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/plugin/fontawesome/');


/**
 * bootstrap confirmation
 */

mix.scripts([
    'node_modules/bootstrap-confirmation2/dist/bootstrap-confirmation.js'
], 'public/plugin/bootstrap-confirmation/bootstrap-confirmation.min.js');

/**
 * JQuery Flip
 */
mix.copy('node_modules/flip/dist/jquery.flip.min.js', 'public/plugin/flip/jquery.flip.min.js');
mix.copy('node_modules/flip/dist/jquery.flip.min.js.map', 'public/plugin/flip/jquery.flip.min.js.map');

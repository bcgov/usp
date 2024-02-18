const mix = require('laravel-mix');
const fs = require('fs');
const path = require('path');

// Define the path to your Laravel Modules directory
const modulesPath = path.join(__dirname, 'Modules');

// Loop through module directories
fs.readdirSync(modulesPath).forEach(moduleName => {
    const modulePath = path.join(modulesPath, moduleName);

    // Check if the module has a 'Resources/css/app.css' file
    const cssPath = path.join(modulePath, 'resources/assets/css/app.css');
    if (fs.existsSync(cssPath)) {
        // Compile the CSS file into a module-specific output directory
        mix.postCss(cssPath, 'public/css');
    }
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .copyDirectory('resources/images', 'public/images')

    .vue()
    .postCss('resources/css/app.css', 'public/css', [require('tailwindcss'), require('autoprefixer')])
        //
    .alias({
        '@': 'resources/js',
    });

if (mix.inProduction()) {
    mix.version();
}

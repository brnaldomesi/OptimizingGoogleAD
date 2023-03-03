const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
let LiveReloadPlugin = require('webpack-livereload-plugin');
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
mix.webpackConfig({
  plugins: [
      new LiveReloadPlugin()
  ],
  resolve: {
    extensions: ['.vue'],
    alias: {
      '@': __dirname + '/resources/assets/js'
    },
  }
});

mix.browserSync('localadevolver.com')

mix.js('resources/assets/js/app.js', 'public/assets/js').sourceMaps()
    .sass('resources/assets/sass/app.scss', 'public/assets/css')
    .sass('resources/sass/app.scss', 'public/css')
    .copyDirectory('resources/assets/image', 'public/assets/img')
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    });

mix.version();
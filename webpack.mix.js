const mix = require('laravel-mix');

mix.react('resources/js/app.jsx', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .version();

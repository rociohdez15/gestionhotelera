const mix = require('laravel-mix');

// Compila el archivo CSS desde resources/css a public/css
mix.postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('autoprefixer'),
]);

// Compila el archivo JS desde resources/js a public/js
mix
.js('resources/js/app.js', 'public/js')
.js('resources/js/vue/main.js','public/js')
.vue();

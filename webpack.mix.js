let mix = require('laravel-mix');
let { env } = require('minimist')(process.argv.slice(2));


require(`./webpack-mix/webpack.${env}.js`);

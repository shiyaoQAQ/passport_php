/*
 * @Author: GXY 
 * @Date: 2019-08-30 14:21:24 
 * @Describe: 珊瑚家-CP后台配置文件
 */

let mix = require('laravel-mix');
let VueLoaderPlugin = require('vue-loader/lib/plugin');
let CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');
let glob = require('glob')

// 自动获取文件并编译
let globPath = glob.sync('resources/assets/cp/**/app.js');
globPath.forEach((v) => {
    let pathText = v.match(/cp\/(\S*)\/js/)[1];
    mix.js(`resources/assets/cp/${pathText}/js/app.js`, `public/build/cp/view/${pathText}/js`)
        .less(`resources/assets/cp/${pathText}/less/app.less`, `public/build/cp/view/${pathText}/css`)
})

// mix配置
mix.setPublicPath('public/build/cp/')
    .extract(['vue', 'jquery', 'iview'], 'vendor')
    .version()
    .setResourceRoot('/build/cp/')
    .autoload({
        jquery: ['$', 'window.jQuery']
    })
    .webpackConfig({
        externals: {
            'AMap': 'AMap',
            'AMapUI': 'AMapUI'
        },
        plugins: [
            new CommonsChunkPlugin({
               name: 'manifest',
               minChunks: Infinity
            }),
            new VueLoaderPlugin()
        ]
    })
    
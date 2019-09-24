/*
 * @Author: GXY 
 * @Date: 2019-08-30 14:21:24 
 * @Describe: 珊瑚家-CP后台配置初始化文件
 */

let mix = require('laravel-mix');
let HtmlWebpackPlugin = require('html-webpack-plugin');
let viewName = process.env.npm_config_viewer;

console.log(process.env.npm_config_viewer);

let htmlTemplate = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>掌上辅材passport</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('view/${viewName}/css/app.css', 'cp/') }}"">
</head>
<body>
    <div id="app">
        <!-- 菜单 -->
        <cp-menu></cp-menu>
        <router-view />
    </div>
</body>
<script src="{{ mix('/manifest.js', 'cp/') }}"></script>
<script src="{{ mix('/vendor.js', 'cp/') }}"></script>
<script src="{{ mix('view/${viewName}/js/app.js', 'cp/') }}"></script>
</html>`

mix.copy('resources/assets/cpTemplate/', `resources/assets/cp/${viewName}/`)
    .webpackConfig({
        plugins: [
            new HtmlWebpackPlugin({
                // template: 'resources/views/template.html',
                templateContent: function() {
                    return htmlTemplate
                },
                filename: path.resolve(`resources/views/cp/${viewName}/index.blade.php`),
                inject: false,
            }),
        ]
    });

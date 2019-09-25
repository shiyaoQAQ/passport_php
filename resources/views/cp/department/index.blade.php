<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>掌上辅材passport</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('view/department/css/app.css', 'build/cp/') }}"">
</head>
<body>
    <div id="app">
        <!-- 菜单 -->
        <cp-menu></cp-menu>
        <router-view />
    </div>
</body>
<script src="{{ mix('/manifest.js', 'build/cp/') }}"></script>
<script src="{{ mix('/vendor.js', 'build/cp/') }}"></script>
<script src="{{ mix('view/department/js/app.js', 'build/cp/') }}"></script>
</html>

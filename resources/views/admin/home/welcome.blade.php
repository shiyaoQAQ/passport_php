@extends('admin.layout')
@section('title', '欢迎页')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    </style>
</head>
<body>
    您好，{{$cp_base_user_name}}
</body>
<script>
</script>
</html>
@endsection

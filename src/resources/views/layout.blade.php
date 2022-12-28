<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('scriptologia/chat/css/app.css')}}">
</head>
<body>
<div class="container">
    @php $user = Auth::user() @endphp
    <h2>{{$user?->name}} <span>{{$user?->id}}</span></h2>
</div>
<div id="app">
    @yield('content')
</div>
<script src="{{asset('scriptologia/chat/js/app.js')}}"></script>
</body>
</html>
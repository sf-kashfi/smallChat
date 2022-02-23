
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatbox</title>
    <link type="text/css" rel="stylesheet" href="{{asset('css/app.css')}}">
    <link type="text/css" rel="stylesheet" href="{{asset('css/icons.css')}}">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        var pv = "{{$user['id']}}";
        var pvName = "{{$user['name']}}";
        var pvUsername = "{{$user['username']}}"
        var myid = "{{Auth::user()->id}}"
        var token = "{{csrf_token()}}";
        var notRead = [];
        var topList = 'i1';
    </script>
</head>
<body translate="no">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('content')
<script src="{{asset('js/app.js')}}"></script>
</body>

</html>

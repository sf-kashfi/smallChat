<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="{{asset('css/icons.css')}}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Settings</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/base.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light nav shadow-sm">
            <div class="container">
                


                <div class="navbar-collapse ">
                    

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto topnav" id="myTopnav">
                        
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <div class="nav-link ">
                                    <a class=" dropdown-item" href="{{ url('/') }}">
                                      {{__('Chat')}}
                                    </a>
                                </div>
                              
                            </li>
                            <li class="nav-item dropdown"><div class="nav-link " aria-labelledby="navbarDropdown"><a class="dropdown-item" href="{{ route('profile.show') }}">User Profile</a></div></li>
                            <li class="nav-item dropdown">


                                <div class="nav-link " aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            <li>
                              <a href="#">
                                <img src="/uploads/avatars/{{ Auth::user()->avatar }}" style="width:32px; height:32px; position:absolute; top:15px; left:25vw; border-radius:50%">
                              </a>
                            </li>
                            
                        @endguest
                            <li href="javascript:void(0);" class="myresnav" onclick="resFunction()">
                               <i class="icon icon--search">menue</i>
                            </li>
                            
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>




    <script>
      function resFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
      }
    </script>

</body>
</html>

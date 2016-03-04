<html style='width: 100%; height: 100%; background: white; text-align: center;'>
    <head>
        <title>City R Us - @yield('title')</title>
    </head>
    <body style='width: 100%; height: 100%; background: white; text-align: center; padding: 100px 0;'>
        <a href="http://city-r-us.scify.org/" style='text-decoration: none; display: block; width: 40%; min-width: 400px; height: auto; padding: 0 40px 0 0; text-align: left; margin: auto;'>
            <img src="{{asset('img/logo.png')}}" alt='City R Us' style='width: 25%; height: auto; display: inline-block;'>
            <p style='font-size: 2.6em; color: rgba(54,106,169,0.9); display: inline-block; position: relative; bottom: 30px; left: 15px; margin: auto'>City R Us</p>
        </a>
        <div style='width: 40%; min-width: 400px; height: auto; padding: 20px; background: rgba(54,106,169,0.9); text-align: left; margin: auto; color: white; font-size: 1.25em;'>
            @yield('content')
        </div>
    </body>
</html>
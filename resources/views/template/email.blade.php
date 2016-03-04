<html style='width: 100%; height: 100%; background: white; text-align: center;'>
    <head>
        <title>City R Us - @yield('title')</title>
    </head>
    <body style='width: 100%; height: 100%; background: white; text-align: center; padding: 100px 0;'>
        <div style='width: 40%; min-width: 400px; height: auto; padding: 20px; text-align: left; margin: auto;'>
            <img src="../../../public/img/logo.png" alt="City R Us" style='width: 25%; height: auto; display: inline-block'/>
            <p style='font-size: 1.5em; color: rgba(54,106,169,0.9)'>City R Us</p>
        </div>
        <div style='width: 40%; min-width: 400px; height: auto; padding: 20px; background: rgba(54,106,169,0.9); text-align: left; margin: auto; color: white; font-size: 1.25em;'>
            @yield('content')
        </div>
    </body>
</html>
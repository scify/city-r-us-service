# City-R-US Service 

### Starting the project

For installing Laravel, please refer to [Official Laravel installation
guide](http://laravel.com/docs/5.0).

### Installing dependencies (assuming apache as web server and mysql as db):

In a nutchell (assuming debian-based OS), first install the dependencies needed:

Note: php5 package installs apache2 as a dependency so we have no need to add
it manually.

`% sudo aptitude install php5 php5-cli mcrypt php5-mcrypt mysql-server php5-mysql`

Install composer according to official instructions (link above) and move binary to ~/bin:

`% curl -sS https://getcomposer.org/installer | php5 && mv composer.phar ~/bin`

Download Laravel installer via composer:

`% composer global require "laravel/installer=~1.1"`

And add ~/.composer/vendor/bin to your $PATH. Example:

```
% cat ~/.profile
[..snip..]
LARAVEL=/home/username/.composer/vendor
PATH=$PATH:$LARAVEL/bin
```

And source your .profile with `% source ~/.profile`

After cloning the project with a simple `git clone https://github.com/scify/city-r-us-service.git`, type `composer install` to install all dependencies.

### Apache configuration:

```
% cat /etc/apache2/sites-available/city-r-us-service.conf
<VirtualHost *:80>
	ServerName myapp.localhost.com
	DocumentRoot "/path/to/city-r-us-service/public"
	<Directory "/path/to/city-r-us-service/public">
		AllowOverride all
	</Directory>
</VirtualHost>
```

Make the symbolic link:

`% cd /etc/apache2/sites-enabled && sudo ln -s ../sites-available/city-r-us-service.conf`

Enable mod_rewrite and restart apache:

`% sudo a2enmod rewrite && sudo service apache2 restart`

Fix permissions for storage directory:

`% chmod -R 775 path/to/city-r-us-service/storage && chown -R www-data:www-data /path/to/city-r-us-service/storage`

Test your setup with:

`% php artisan serve`

and navigate to localhost:8000.


### Nginx configuration:

Add additional the additional dependencies needed:

`% sudo aptitude install nginx php5-fpm`

Disable cgi.fix_pathinfo at /etc/php5/fpm/php.ini: `cgi.fix_pathinfo=0`

`% sudo php5enmod mcrypt && sudo service php5-fpm restart`

Nginx server block:

```
server {
    listen 80 default_server;
    listen [::]:80 default_server ipv6only=on;

    root /var/www/laravel/public;
    index index.php index.html index.htm;

    server_name server_domain_or_IP;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

`% sudo service nginx restart && sudo chmod -R 775 path/to/project/storage`

And finally, set the group appropriately:

`% sudo chown -R www-data:www-data storage`

### database instructions placeholder###

Initialize the database with `php artisan migrate` and test the installation with `php artisan serve` and hit `localhost:8000/auth/register` at your browser of choice to create the first user.


### env file ###
Create an .env file. Since City-R-Us integrates with the http://www.radical-project.eu/ the staging urls should be provided also
 
 ```
 APP_ENV=local
APP_DEBUG=true
APP_KEY=rIXzIHrNd5keauakhH8Hf0YHMTK5kAMq

DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME= ...
DB_PASSWORD=...

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MAIL_DRIVER=smtp
MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDR=...
MAIL_FROM_NAME=...

RADICAL_CONFIGURATION_API=http://vm.radical-project.eu:8080/ConfigurationAPI/
RADICAL_DATA_API=http://vm.radical-project.eu:8080/Radical/rest/
RADICAL_REPOSITORY_API=http://vm.radical-project.eu:3000/
RADICAL_CITYNAME=ATHENS

JWT_SECRET=...
```
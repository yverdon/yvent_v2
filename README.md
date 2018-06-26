# 📅 YVENT

## Requirements

  * Postgresql 9.6
  * Docker 18 or higher

## Getting started

Clone the repository:

```
git clone https://github.com/maltaesousa/yvent_v2.git yvent
```

## DOCKER

*Note for windows 10 users*

After windows 10 restart, you might need to restart docker: https://github.com/docker/for-win/issues/1038 

*To setup the app inside a docker container*

```
docker-compose build .
docker-compose up
```

*Calling php artisan/composer inside the composer can be done the following way*

```
docker-compose run yvent composer install
docker-compose run yvent php artisan serve
```

*build the app:*
docker-compose build

* install php composer dependecies *
```
docker-compose run php-fpm composer install
```

* grant right on storage folder to www-data user*
```
chmod -R a+rw storage/
```


*start the app:*
docker-compose up

*Update tntsearch index*


```
docker-compose run yvent php artisan scout:import App\\Event
```

## NON-DOCKER
Install the app:

```
cd yvent
composer install
```

## Populate the database (Only for dev environement)

```
createdb -U postgres test
psql -U postgres -d test -f prepare.sql
pg_restore -U postgres -d test lv_yvent.dump
```

## Last steps

The app needs a key in order to work properly:

```
copy .env.example .env
php artisan key:generate
```

If your app will run in another endpoint than /yvent, you need to change your custom endpoint in the `/public/.htaccess` file:
for example: www.host.com/custom_yvent

```
RewriteBase /custom_yvent/
```

## Run the app (dev)

```
php artisan serve
```

Logon with:

> User: masrad
> Pwd: masrad

## Troubleshooting

Be sure Composer is using the right PHP (at least PHP 7.0)

In Windows, if you get PDO exception on attempt to login, you're probably not using postgres driver for PHP.
You should uncomment the line containing `php_pdo_pgsql.dll` in the php.ini file.
To find where is php.ini file you can type `where php` on cmd or `which php` on powershell.

Writing permissions are needed in `/storage` for logger. Without proper permission, the app won't work.

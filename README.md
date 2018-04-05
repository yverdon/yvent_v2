# Yvent application - a shared event manager

## Install

### Requirements

  * PHP 7
  * Composer (when installing it, choose PHP 7.0 or higher when prompted for executable path!)
  * Postgresql 9.6

  Clone the repository:

  ```
  git clone https://github.com/yverdon/yvent_v2.git yvent
  ```

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

  ## Troubleshooting

  Be sure Composer is using the right PHP (at least PHP 7.0)

  In Windows, if you get PDO exception on attempt to login, you're probably not using postgres driver for PHP.
  You should uncomment the line containing `php_pdo_pgsql.dll` in the php.ini file.
  To find where is php.ini file you can type `where php` on cmd or `which php` on powershell.

  Writing permissions are needed in `/storage` for logger. Without proper permission, the app won't work.

### Setting up EVENT fulltext search:
- Install laravel/scout https://laravel.com/docs/5.6/scout and (numerous) dependencies, including redis and tntsearch. Please follow these [instructions](https://github.com/teamtnt/tntsearch)
  and also these [instructions](https://github.com/teamtnt/laravel-scout-tntsearch-driver)

Don't forget to:
  - enable extension=pdo_sqlite in php.ini
  - enable extension=sqlite3 in php.ini
  - enable curl extension in php.ini

# Laravel Mailog

This package allows you to redirect all your application outgoing email to a database and view them using the integrated Web UI.
## Installation

Install the package using composer:

``` bash
composer require filippo-toso/laravel-mailog
```

Then, in your `config/mail.php` file add the mailog mailer as shown below.

```php
return [

    // ...

    'mailers' => [

        // ...

        'mailog' => [
            'transport' => 'mailog',
        ],

    ],

];
```

If you want/need to customize the configuration (ie. use a different database for the logging), publish the configuration file and edit it accordingly.

```
php artisan vendor:publish --tag="mailog-config"
```

If you want/need to customize the migrations (ie. to add foreign keys for your tenats), publish the migrations and edit them accordingly.

```
php artisan vendor:publish --tag="mailog-migrations"
```

Then, execute the migrations:

```
php artisan migrate
```

If you want to use the built-in web UI, you can register the routes in your `web.php` file.
Then you'll be able to access the UI using the `/mailog` url.

```php
Route::middleware('auth')->group(function () {
    
    // ...
    
    FilippoToso\LaravelMailog\Support\Routes::register;
});
```
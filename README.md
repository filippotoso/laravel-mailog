# Laravel Mailog

This package allows you to store all your application outgoing email to a database and view them using the integrated Web UI.
You can use it as a mail transport (no email is delivered) or as and event listener (all emails are delivered and logged into the database).

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

If you want to use the `mailog` transport, edit your .env file as follows:

```
MAIL_MAILER=mailog
```

If you want/need to customize the configuration (ie. use a different database for the logging, etc.), publish the configuration file and change it accordingly:

```
php artisan vendor:publish --tag="mailog-config"
```

If you want/need to customize the migrations (ie. to add a foreign keys for your tenants), publish the migrations and change them accordingly:

```
php artisan vendor:publish --tag="mailog-migrations"
```

Then, execute the migrations:

```
php artisan migrate
```

If you want to use the built-in web UI, you can register the routes in your `web.php` file.
Then you'll be able to access the UI using the `/mailog/messages` url.

```php
Route::middleware('auth')->group(function () {
    
    // ...
    
    \FilippoToso\LaravelMailog\Support\MailogRoutes::register();
});
```

If you want to customize the UI, you can publish the views as follows: 

```
php artisan vendor:publish --tag="mailog-views"
```

You can also override the `FilippoToso\LaravelMailog\Http\Controllers\MessageController` class and change its behaviour. For instance you could add a filter for your tenant:

```php
use FilippoToso\LaravelMailog\Http\Controllers\Concerns\IsMailogController;

class MailogMessageController extends Controller
{
    use IsMailogController {
        query as mailogQuery;
    }

    /**
     * The query that filters the messages
     *
     * @param array $filters
     * @return Builder
     */
    protected function query(array $filters) {
        return parent::mailogQuery()
            ->where('tenant_id','=', tenant('id'));
    }
}
```

Then you have to update your routes file to point to the right controller. 
You can take inspiration from the `FilippoToso\LaravelMailog\Support\Routes::register()` method.

If you want to use the event listening mode instead the mail transport mode, restore the `MAIL_MAILER` variable `.env` (ie. set it to `smtp`) and edit the `config/mailog.php` file as follows:

```
'listen' => true,
```

The package includes a `mailog:purge-messages` command. You can schedule it in the scheduler and configure its behaviour in the `purge` section of the `config/mailog.php` file 

Finally, in the `config/mailog.php` file you can specify a class to use as a transport. For instance, you can override the provided `MailogTransport` class and add the support for tenancy as follows:

```php
use FilippoToso\LaravelMailog\Transport\MailogTransport;
use Symfony\Component\Mime\Email;

class MailogTransportWithTenant extends MailogTransport 
{
    /**
     * Build the message data to be stored in the database
     * You can override this method to add more fields to the message
     *
     * @param $messageId
     * @param Email $converdedEmail
     * @return array
     */
    protected function messageData(string $messageId, Email $converdedEmail)
    {
        return array_merge(parent::messageData($messageId, $converdedEmail),[
            'tenant_id' => tenant('id'),
        ]);
    }
}
```

Please beware, the use of `tenant('id')` for identifing the tenant foreign key is just an example taken from the `stancl/tenancy` package. Depending on how you are implementing the tenancy, you should adapt the code as needed.

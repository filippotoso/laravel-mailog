<?php

namespace FilippoToso\LaravelMailog;

use Illuminate\Support\Facades\Event;
use FilippoToso\LaravelMailog\Commands\PurgeMessages;
use FilippoToso\LaravelMailog\Listeners\LogMessageSent;
use FilippoToso\LaravelMailog\Transport\MailogTransport;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-mailog')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(PurgeMessages::class)
            ->hasMigration('create_mailog_tables');
    }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function packageBooted()
    {
        // This allows to use the models defined in the configuration file within the web ui controllers
        class_alias(Config::get('mailog.models.messages'), 'FilippoToso\LaravelMailog\Models\Message');
        class_alias(Config::get('mailog.models.message_addresses'), 'FilippoToso\LaravelMailog\Models\MessageAddress');
        class_alias(Config::get('mailog.models.message_attachments'), 'FilippoToso\LaravelMailog\Models\MessageAttachment');

        // Register the transport driver
        Mail::extend('mailog', function (array $config = []) {
            return new MailogTransport();
        });

        // Register the event listener
        if (Config::get('mailog.listen')) {
            Event::listen(
                MessageSent::class,
                LogMessageSent::class,
            );
        }
    }
}

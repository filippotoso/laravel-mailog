<?php

// config for FilippoToso/LaravelMailog

return [
    /**
     * The models used by the package
     * These values are used in FilippoToso\LaravelMailog\ServiceProvider to create class aliases
     */
    'models' => [
        'messages' => \FilippoToso\LaravelMailog\Support\Models\Message::class,
        'message_addresses' => \FilippoToso\LaravelMailog\Support\Models\MessageAddress::class,
        'message_attachments' => \FilippoToso\LaravelMailog\Support\Models\MessageAttachment::class,
    ],

    /**
     * The names of the tables used by the package
     */
    'tables' => [
        'messages' => 'mailog_messages',
        'message_addresses' => 'mailog_message_addresses',
        'message_attachments' => 'mailog_message_attachments',
    ],

    /**
     * The connection used by the package
     */
    'connection' => env('MAILOG_CONNECTION', env('DB_CONNECTION', 'mysql')),

    /**
     * Where to store the messages and attachments 
     */
    'filesystem' => [
        'disk' => env('MAILOG_DISK', env('FILESYSTEM_DISK', 'local')),
        'path' => env('MAILOG_PATH', 'mailog'),
    ],

    /**
     * Purge old emails, disabled by default
     */
    'purge' => [
        'enabled' => env('MAILOG_PURGE_ENABLED', false),
        'older_than_days' => env('MAILOG_PURGE_OLDER_THAN_DAYS', 30),
    ],

    /**
     * The class used to log the emails
     * You can override it and, for instance, add a tenant_id to the logged message
     */
    'transport' => \FilippoToso\LaravelMailog\Transport\MailogTransport::class,

    /**
     * The emails with the subject that matches any of the following regular expressions will be excluded from the logging process
     */
    'excluded' => [
        // '#New exception#si',
    ],

    /**
     * If set to true, the package will listen for the MessageSent event and log the emails
     * You should enable this only if you are not using mailog as transport in your mail configuration
     */
    'listen' => env('MAILOG_LISTEN', false),
];

<?php

namespace FilippoToso\LaravelMailog\Support;

use FilippoToso\LaravelMailog\Http\Controllers;
use Illuminate\Support\Facades\Route;

class MailogRoutes
{
    /**
     * Register the routes
     *
     * @return void
     */
    public static function register(string $namePrefix = '')
    {
        Route::get('mailog/messages', [Controllers\MessageController::class, 'index'])->name($namePrefix . 'mailog.messages.index');
        Route::get('mailog/messages/{message}/show', [Controllers\MessageController::class, 'show'])->name($namePrefix . 'mailog.messages.show');
        Route::get('mailog/messages/{message}/html', [Controllers\MessageController::class, 'html'])->name($namePrefix . 'mailog.messages.html');
        Route::get('mailog/messages/{message}/download', [Controllers\MessageController::class, 'downloadMessage'])->name($namePrefix . 'mailog.messages.download-message');
        Route::get('mailog/messages/{attachment}/attachment', [Controllers\MessageController::class, 'downloadAttachment'])->name($namePrefix . 'mailog.messages.download-attachment');
    }
}

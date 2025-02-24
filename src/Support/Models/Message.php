<?php

namespace FilippoToso\LaravelMailog\Support\Models;

use FilippoToso\LaravelMailog\Models\MessageAttachment;
use FilippoToso\LaravelMailog\Support\Models\MessageAttachment as ModelsMessageAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'date' => null,
        'subject' => null,
        'text' => null,
        'html' => null,
        'path' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['date', 'subject', 'text', 'html', 'path'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * The addresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(MessageAddress::class);
    }

    /**
     * The attachments relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(ModelsMessageAttachment::class);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return Config::get('mailog.tables.messages', parent::getTable());
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return Config::get('mailog.connection', parent::getConnectionName());
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Message $message) {
            Storage::disk(Config::get('mailog.filesystem.disk'))->delete($message->path);

            /** @disregard P1009 Undefined type */
            $message->attachments->each(function (MessageAttachment $attachment) {
                $attachment->delete();
            });
        });
    }
}

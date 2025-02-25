<?php

namespace FilippoToso\LaravelMailog\Support\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Message Attachment Model
 * 
 * @property int $id
 * @property int $message_id
 * @property string $filename
 * @property int $size
 * @property string $path
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

class MessageAttachment extends Model
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'message_id' => null,
        'filename' => null,
        'size' => null,
        'path' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['message_id', 'filename', 'size', 'path'];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return Config::get('mailog.tables.message_attachments', parent::getTable());
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
        });
    }
}

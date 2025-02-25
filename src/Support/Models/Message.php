<?php

namespace FilippoToso\LaravelMailog\Support\Models;

use FilippoToso\LaravelMailog\Enums\MessageAddressType;
use FilippoToso\LaravelMailog\Models\MessageAttachment;
use FilippoToso\LaravelMailog\Support\Models\MessageAttachment as ModelsMessageAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Message Model
 * 
 * @property int $id
 * @property \Carbon\Carbon $date
 * @property string $path
 * @property string $subject
 * @property string $text
 * @property string $html
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
     * The fromAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fromAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::From);
    }

    /**
     * The toAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function toAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::To);
    }

    /**
     * The ccAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ccAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::Cc);
    }

    /**
     * The bccAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bccAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::Bcc);
    }

    /**
     * The returnPathAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnPathAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::ReturnPath);
    }

    /**
     * The replyToAddresses relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replyToAddresses()
    {
        return $this->addresses()->where('type', '=', MessageAddressType::ReplyTo);
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
            Storage::disk(Config::get('mailog.filesystem.disk'))->deleteDirectory(dirname($message->path));
        });
    }
}

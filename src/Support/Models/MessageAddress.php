<?php

namespace FilippoToso\LaravelMailog\Support\Models;

use Illuminate\Support\Facades\Config;
use FilippoToso\LaravelMailog\Enums\MessageAddressType;
use Illuminate\Database\Eloquent\Model;

/**
 * Message Address Model
 * 
 * @property int $id
 * @property int $message_id
 * @property MessageAddressType $type
 * @property string $address
 * @property mixed $name
 * @property string $domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class MessageAddress extends Model
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'message_id' => null,
        'type' => null,
        'address' => null,
        'domain' => null,
        'name' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['message_id', 'type', 'address', 'domain', 'name'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => MessageAddressType::class,
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return Config::get('mailog.tables.message_addresses', parent::getTable());
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
}

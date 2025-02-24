<?php

namespace FilippoToso\LaravelMailog\Enums\Concerns;

use Illuminate\Support\Arr;

trait HasValues
{
    public static function values(): array
    {
        return Arr::map(static::cases(), function ($case) {
            return $case->value;
        });
    }
}

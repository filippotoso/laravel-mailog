<?php

namespace FilippoToso\LaravelMailog\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

trait HasStorage
{
    /**
     * Get the pre-configured storage
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function Storage()
    {
        return Storage::disk(Config::get('mailog.filesystem.disk'));
    }
}

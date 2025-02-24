<?php

namespace FilippoToso\LaravelMailog\Http\Controllers;

use Illuminate\Http\Request;

class MessageController
{
    public function index(Request $request)
    {
        return view('laravel-mailog::messages.index');
    }
}

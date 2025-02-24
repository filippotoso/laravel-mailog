<?php

namespace FilippoToso\LaravelMailog\Enums;

use FilippoToso\LaravelMailog\Enums\Concerns\HasValues;

enum MessageAddressType: string
{
    use HasValues;

    case From = 'from';
    case To = 'to';
    case Cc = 'cc';
    case Bcc = 'bcc';
    case ReturnPath = 'return-path';
    case ReplyTo = 'reply-to';
}

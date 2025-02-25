<?php

namespace FilippoToso\LaravelMailog\Commands;

use Carbon\Carbon;
use FilippoToso\LaravelMailog\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class PurgeMessages extends Command
{
    public $signature = 'mailog:purge-messages';

    public $description = 'Purge old messages';

    public function handle(): int
    {
        if (!Config::get('mailog.purge.enabled')) {
            $this->verbose('Purge is disabled');
        }

        $olderThan = Config::get('mailog.purge.older_than_days');
        $offset = Carbon::now()->subDays($olderThan);

        /** @disregard P1009 Undefined type */
        Message::where('date', '<', $offset)
            ->get()
            ->each(function (Message $message) {
                $this->verbose("Deleting message {$message->id}");

                // Slower but triggers events to delete attachments and original messsages from the filesystem 
                $message->delete();
            });

        return Command::SUCCESS;
    }

    protected function verbose($message)
    {
        if ($this->option('verbose')) {
            $this->info($message);
        }
    }
}

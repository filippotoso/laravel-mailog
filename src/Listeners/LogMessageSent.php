<?php

namespace FilippoToso\LaravelMailog\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Config;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LogMessageSent implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    private ?EventDispatcherInterface $dispatcher;
    private LoggerInterface $logger;

    public function __construct(?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Handle the event.
     *
     * @param Illuminate\Mail\Events\MessageSent $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        $class = Config::get('mailog.transport');

        (new $class($this->dispatcher, $this->logger))->listen($event->message);
    }
}

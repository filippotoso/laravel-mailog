<?php

namespace FilippoToso\LaravelMailog\Http\Controllers;

use Carbon\Carbon;
use FilippoToso\LaravelMailog\Http\Controllers\Concerns\HasStorage;
use FilippoToso\LaravelMailog\Models\Message;
use FilippoToso\LaravelMailog\Models\MessageAttachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class MessageController
{
    use HasStorage;

    protected const FILTER_DATE_FORMAT = 'Y-m-d\TH:i';
    protected const DATABASE_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Show the messages
     *
     * @param Request $request
     * @param Route $route
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Route $route)
    {
        $filters = $this->filters($request);
        $query = $this->query($filters);

        $messages = $query->paginate();

        return view('mailog::messages.index', [
            'messages' => $messages,
            'filters' => $filters,
            'route' => $this->route($route),
        ]);
    }

    /**
     * The query that filters the messages
     *
     * @param array $filters
     * @return Builder
     */
    protected function query(array $filters)
    {
        /** @disregard P1009 Undefined type */
        return Message::query()
            ->when($filters['address'], function (Builder $query, $address) {
                return $query->whereHas('addresses', function (Builder $query) use ($address) {
                    $query->where('address', '=', $address);
                });
            })
            ->when($filters['domain'], function (Builder $query, $domain) {
                return $query->whereHas('addresses', function (Builder $query) use ($domain) {
                    $query->where('domain', '=', $domain);
                });
            })
            ->when($filters['search'], function (Builder $query, $search) {
                return $query->where('subject', 'LIKE', "%{$search}%")
                    ->orWhere('text', 'LIKE', "%{$search}%")
                    ->orWhere('html', 'LIKE', "%{$search}%")
                    ->whereHas('addresses', function (Builder $query) use ($search) {
                        $query->where('address',  'LIKE', "%{$search}%");
                    });
            })
            ->when(Carbon::hasFormat($filters['from'], static::FILTER_DATE_FORMAT), function (Builder $query, $filters) {
                $date = Carbon::createFromFormat(static::FILTER_DATE_FORMAT, $filters['from'])->format(static::DATABASE_DATE_FORMAT);
                return $query->where('date', '>=', $date);
            })
            ->when(Carbon::hasFormat($filters['to'], static::FILTER_DATE_FORMAT), function (Builder $query, $filters) {
                $date = Carbon::createFromFormat(static::FILTER_DATE_FORMAT, $filters['to'])->format(static::DATABASE_DATE_FORMAT);
                return $query->where('to', '>=', $date);
            })
            ->withCount('attachments')
            ->orderBy('date', 'desc');
    }

    /**
     * Return the input filters
     *
     * @param Request $request
     * @return array
     */
    protected function filters(Request $request)
    {
        $keys = [
            'address',
            'domain',
            'search',
            'from',
            'to'
        ];

        return array_merge(
            array_combine($keys, array_fill(0, count($keys), null)),
            $request->only($keys)
        );
    }

    /**
     * Show the message
     *
     * @param Message $message
     * @param Route $route
     * @return \Illuminate\View\View
     */
    /** @disregard P1009 Undefined type */
    public function show(Message $message, Route $route)
    {
        $message->load([
            'fromAddresses',
            'toAddresses',
            'ccAddresses',
            'bccAddresses',
            'returnPathAddresses',
            'replyToAddresses',
            'attachments',
        ]);

        return view('mailog::messages.show', [
            'message' => $message,
            'route' => $this->route($route),
        ]);
    }

    /**
     * Show the message HTML
     *
     * @param Message $message
     * @return \Illuminate\Http\Response
     */
    /** @disregard P1009 Undefined type */
    public function html(Message $message)
    {
        return response($message->html)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Download the message
     *
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    /** @disregard P1009 Undefined type */
    public function downloadMessage(Message $message)
    {
        /** @var FilesystemAdapter $storage */
        $storage = $this->storage();

        if ($storage->exists($message->path)) {
            return $storage->download($message->path, sprintf('message-%d.eml', $message->id));
        }

        abort(404);
    }

    /**
     * Download the attacment
     *
     * @param MessageAttachment $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    /** @disregard P1009 Undefined type */
    public function downloadAttachment(MessageAttachment $attachment)
    {
        /** @var FilesystemAdapter $storage */
        $storage = $this->storage();

        if ($storage->exists($attachment->path)) {
            return $storage->download($attachment->path, $attachment->filename);
        }

        abort(404);
    }

    /**
     * Get the base route name
     *
     * @param Route $route
     * @return string
     */
    protected function route(Route $route)
    {
        return substr($route->getName(), 0, strrpos($route->getName(), '.'));
    }
}

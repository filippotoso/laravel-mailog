<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Laravel Mailog">
    <meta name="author" content="Filippo Toso">
    <title>Mailog Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <main class="col-12 p-4">

                <h2>Messages</h2>

                @include('mailog::messages.partials.search')

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Date</th>
                                <th scope="col">From</th>
                                <th scope="col">To</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Attachments</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($messages->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">No mail logged yet.</td>
                                </tr>
                            @else
                                @foreach ($messages as $message)
                                    <tr>
                                        <td>{{ $message->id }}</td>
                                        <td>{{ $message->date->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $message->fromAddresses->pluck('address')->join(', ') }}</td>
                                        <td>{{ $message->toAddresses->pluck('address')->join(', ') }}</td>
                                        <td>{{ $message->subject }}</td>
                                        <td>{{ $message->attachments_count ?: 'None' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route($route . '.show', ['message' => $message]) }}"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route($route . '.download-message', ['message' => $message]) }}"><i class="bi bi-download"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    @if ($messages->hasPages())
                        {{ $messages->links() }}
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

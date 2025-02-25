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

                <p><a href="{{ route($route . '.index') }}" class="btn btn-primary"> &laquo; Back</a></p>

                <h2>Message {{ $message->id }} of {{ $message->date->format('Y-m-d') }}</h2>

                <div class="row">
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'From addresses:', 'addresses' => $message->fromAddresses])
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'To addresses:', 'addresses' => $message->toAddresses])
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'Cc addresses:', 'addresses' => $message->ccAddresses])
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'Bcc addresses:', 'addresses' => $message->bccAddresses])
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'Reply To addresses:', 'addresses' => $message->replyToAddresses])
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        @include('mailog::messages.partials.addresses', ['label' => 'Return Path addresses:', 'addresses' => $message->returnPathAddresses])
                    </div>
                    <div class="col-12 pt-3">
                        <label class="form-label fw-bold">Subject</label>
                        <div class="border rounded p-2">{{ $message->subject }}</div>
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        <label class="form-label fw-bold">Text Body</label>
                        <pre class="border rounded p-2" style="white-space: pre-wrap;">{{ $message->text }}</pre>
                    </div>
                    <div class="col-12 col-xl-6 pt-3">
                        <label class="form-label fw-bold">Html Body</label>
                        <iframe class="border rounded p-2" style="pointer-events: none; width: 100%; height: 400px;" src="{{ route($route . '.html', ['message' => $message]) }}" style="width: 100%; height: 400px;"></iframe>
                    </div>

                    <div class="col-12 pt-3">
                        <label class="form-label fw-bold">Attachments</label>

                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Filename</th>
                                        <th scope="col">Size</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($message->attachments->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center">No attachments present.</td>
                                        </tr>
                                    @else
                                        @foreach ($message->attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->id }}</td>
                                                <td>{{ $attachment->filename }}</td>
                                                <td>{{ round($attachment->size / 1024) }} kB</td>
                                                <td class="text-center">
                                                    <a href="{{ route($route . '.download-attachment', ['attachment' => $attachment]) }}"><i class="bi bi-download"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<label class="form-label fw-bold">{{ $label }}</label>
<div class="border rounded p-2">
    @if ($addresses->isEmpty())
        <em>None</em>
    @else
        @foreach ($addresses as $address)
            @if ($address->name == '')
                {{ $address->address }}{{ $loop->last ? '' : ',' }}
            @else
                {{ $address->name }} &lt;{{ $address->address }}&gt;{{ $loop->last ? '' : ',' }}
            @endif
        @endforeach
    @endif
</div>

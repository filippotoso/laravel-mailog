<form method="GET" action="{{ route($route . '.index') }}">
    <div class="row">
        <div class="col-12 col-xl-6 pt-3">
            <label for="address" class="form-label">Email address (exact)</label>
            <input type="email" class="form-control" id="address" name="address" placeholder="" value="{{ $filters['address'] }}">
        </div>
        <div class="col-12 col-xl-6 pt-3">
            <label for="domain" class="form-label">Email domain (exact)</label>
            <input type="text" class="form-control" id="domain" name="domain" placeholder="" value="{{ $filters['domain'] }}">
        </div>
        <div class="col-12 col-xl-6 pt-3">
            <label for="from" class="form-label">From date (included)</label>
            <input type="datetime-local" class="form-control" id="from" name="from" placeholder="" value="{{ $filters['from'] }}">
        </div>
        <div class="col-12 col-xl-6 pt-3">
            <label for="to" class="form-label">To date (included)</label>
            <input type="datetime-local" class="form-control" id="to" name="to" placeholder="" value="{{ $filters['to'] }}">
        </div>
        <div class="col-12 col-xl-7 pt-3">
            <label for="search" class="form-label">Generic search (very slow)</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="" value="{{ $filters['search'] }}">
        </div>
        <div class="col-12 col-md-6 col-xl-2 pt-5">
            <a href="{{ route($route . '.index') }}" class="btn btn-secondary w-100">Clear</a>
        </div>
        <div class="col-12 col-md-6 col-xl-3 pt-5">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </div>
</form>

@if (Session::has('errors') && Session::get('errors')->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach (Session::get('errors')->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (Session::has('error'))
    <div class="alert alert-danger" role="alert">
        {{ Session::get('error') }}
    </div>
@endif

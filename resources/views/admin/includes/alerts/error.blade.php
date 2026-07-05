@if (Session::has('errors'))
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach (Session::get('errors')->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- resources/views/dir/demandeAutorisations/partials/rejected-by.blade.php --}}
@if(!empty($demande->rejected_by))
    <div class="alert alert-danger">
        <h6 class="mb-1"><i class="fas fa-ban"></i> @lang('trans.rejected_by')</h6>
        <ul class="mb-0">
            @foreach($demande->rejected_by as $rejection)
                <li>
                    <strong>{{ $rejection['dept'] }}</strong>@if(!empty($rejection['motif'])) : {{ $rejection['motif'] }}@endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

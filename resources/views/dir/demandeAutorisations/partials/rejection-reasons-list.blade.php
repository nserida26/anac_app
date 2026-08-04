{{-- resources/views/dir/demandeAutorisations/partials/rejection-reasons-list.blade.php --}}
@if (!empty($demande->rejection_reasons_list))
    <div class="alert alert-danger">
        <h6 class="mb-1"><i class="fas fa-ban"></i> @lang('trans.rejection_reasons')</h6>
        <ul class="mb-0">
            @foreach ($demande->rejection_reasons_list as $reason)
                <li>{{ $reason }}</li>
            @endforeach
        </ul>
    </div>
@endif

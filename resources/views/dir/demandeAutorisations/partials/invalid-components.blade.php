{{-- resources/views/dir/demandeAutorisations/partials/invalid-components.blade.php --}}
@if(!empty($demande->invalid_reasons))
    <div class="alert alert-warning">
        <h6 class="mb-1"><i class="fas fa-exclamation-triangle"></i> @lang('trans.invalid_components')</h6>
        <ul class="mb-0">
            @foreach($demande->invalid_reasons as $reason)
                <li>
                    <strong>{{ ucfirst(str_replace('_', ' ', $reason['type'] ?? '')) }}</strong>
                    @if(!empty($reason['identifier']))
                        ({{ $reason['identifier'] }})
                    @endif
                    :
                    {{ $reason['motif'] ?? 'N/A' }}
                    @if(!empty($reason['role']))
                        <span class="badge badge-secondary">{{ $reason['role'] }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

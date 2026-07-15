{{-- resources/views/admin/partials/validation-section.blade.php --}}
<div class="card card-primary">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title">{{ $title }}</h3>
        <span class="badge badge-light">
            {{ $items->count() }} @lang('trans.items')
        </span>
    </div>
    <div class="card-body">
        @if($items->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            @foreach($columns as $key => $label)
                                <th>{{ $label }}</th>
                            @endforeach
                            <th>@lang('trans.status')</th>
                            <th>@lang('trans.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr id="{{ $type }}-{{ $item->id }}">
                                @foreach($columns as $key => $label)
                                    <td>
                                        @switch($key)
                                            @case('type')
                                                @if($type === 'avions')
                                                    {{ $item->type->code ?? 'N/A' }}
                                                @elseif($type === 'document_autorisations')
                                                    {{ LaravelLocalization::getCurrentLocale() == 'fr' ? optional($item->typeDocument)->nom_fr : optional($item->typeDocument)->nom_en }}
                                                @else
                                                    {{ $item->$key ?? 'N/A' }}
                                                @endif
                                                @break
                                                
                                            @case('operateur')
                                                {{ $item->compagnie->nom_entreprise ?? 'N/A' }}
                                                @break
                                                
                                            @case('depart')
                                                {{ $item->aeroportDepart->codeICAO ?? 'N/A' }}
                                                @break

                                            @case('piste_depart')
                                                {{ $item->numero_piste_depart ?? 'N/A' }}
                                                @break
                                                
                                            @case('arrivee')
                                                {{ $item->aeroportArrivee->codeICAO ?? 'N/A' }}
                                                @break

                                            @case('piste_arrivee')
                                                {{ $item->numero_piste_arrivee ?? 'N/A' }}
                                                @break
                                                
                                            @case('itineraire')
                                                @php
                                                    $escales = $item->escales()->orderBy('ordre')->get();
                                                    $route = $item->aeroportDepart->codeICAO ?? 'N/A';
                                                    foreach($escales as $escale) {
                                                        $route .= ' → ' . $escale->aeroport->codeICAO;
                                                    }
                                                    $route .= ' → ' . ($item->aeroportArrivee->codeICAO ?? 'N/A');
                                                @endphp
                                                <small>{{ $route }}</small>
                                                @break
                                                
                                            @case('licence')
                                                @if($item->licence_numero)
                                                    {{ $item->licence_numero }}
                                                    ({{ $item->licence_expiration ? $item->licence_expiration->format('d/m/Y') : 'N/A' }})
                                                @else
                                                    N/A
                                                @endif
                                                @break
                                                
                                            @case('justificatif')
                                            @case('piece_identite')
                                            @case('document')
                                                @php
                                                    $path = $key === 'document' ? $item->url : ($key === 'piece_identite' ? $item->piece_identite_path : $item->justificatif);
                                                @endphp
                                                @if($path)
                                                    <a href="{{ asset('/uploads/' . $path) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                                @break
                                                
                                            @case('poids')
                                                {{ $item->poids }} kg
                                                @break
                                                
                                            @case('description')
                                                {{ $item->instructions_speciales ?? 'N/A' }}
                                                @break
                                                
                                            @case('telephone')
                                                {{ $item->telephone_contact ?? 'N/A' }}
                                                @break
                                                
                                            @case('email')
                                                {{ $item->email_contact ?? 'N/A' }}
                                                @break
                                                
                                            @case('fonction')
                                                {{ $item->fonction_contact ?? $item->fonction ?? 'N/A' }}
                                                @break
                                                
                                            @default
                                                {{ $item->$key ?? 'N/A' }}
                                        @endswitch
                                    </td>
                                @endforeach
                                
                                <td>
                                    @if($item->valider)
                                        <span class="validation-badge validated">
                                            <i class="fas fa-check-circle"></i> @lang('trans.validated')
                                        </span>
                                    @else
                                        <span class="validation-badge not-validated">
                                            <i class="fas fa-times-circle"></i> @lang('trans.not_validated')
                                        </span>
                                    @endif
                                    @if($item->motif)
                                        <div class="validation-comments">
                                            <i class="fas fa-comment"></i> {{ $item->motif }}
                                        </div>
                                    @endif
                                </td>
                                
                                <td>
                                    @if(is_null($item->valider))
                                        <button class="btn btn-success btn-sm me-1"
                                                onclick="openDecisionModal('{{ $type }}', '{{ $item->id }}', '{{ $demandeId }}', 'approve')"
                                                title="@lang('trans.approve')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                                onclick="openDecisionModal('{{ $type }}', '{{ $item->id }}', '{{ $demandeId }}', 'reject')"
                                                title="@lang('trans.reject')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @elseif($item->valider == 1)
                                        <span class="badge bg-success p-2">
                                            <i class="fas fa-check-circle"></i> @lang('trans.approved')
                                        </span>
                                        <button class="btn btn-danger btn-sm ms-2"
                                                onclick="openDecisionModal('{{ $type }}', '{{ $item->id }}', '{{ $demandeId }}', 'reject')"
                                                title="@lang('trans.reject')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-danger p-2">
                                            <i class="fas fa-times-circle"></i> @lang('trans.rejected')
                                        </span>
                                        <button class="btn btn-success btn-sm ms-2"
                                                onclick="openDecisionModal('{{ $type }}', '{{ $item->id }}', '{{ $demandeId }}', 'approve')"
                                                title="@lang('trans.approve')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> @lang('trans.no_items_found')
            </div>
        @endif
    </div>
</div>

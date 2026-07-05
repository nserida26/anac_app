{{-- resources/views/dir/demandeAutorisations/modals/issues.blade.php --}}
<div class="modal fade" id="issuesModal-{{ $demande->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-bug"></i> @lang('trans.issues_for') : {{ $demande->code }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                {{-- Motifs d'invalidité (Technique) --}}
                @if (!empty($demande->invalid_reasons))
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> @lang('trans.invalid_components')</h6>
                        <ul class="mb-0">
                            @foreach ($demande->invalid_reasons as $reason)
                                <li>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $reason['type'] ?? '')) }}</strong> : 
                                    {{ $reason['motif'] ?? 'N/A' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Motifs de rejet (Administratif) --}}
                @if (!empty($demande->rejection_reasons_list))
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-ban"></i> @lang('trans.rejection_reasons')</h6>
                        <ul class="mb-0">
                            @foreach ($demande->rejection_reasons_list as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('trans.close')</button>
                @if (auth()->user()->can('edit-demandes'))
                    <a href="{{ route('user.autorisations.edit', $demande->id) }}" class="btn btn-primary">
                        <i class="fas fa-tools"></i> @lang('trans.correct_issues')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
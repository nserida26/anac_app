{{-- resources/views/dir/demandeLicences/modals/issues.blade.php --}}
<div class="modal fade" id="issuesModal-{{ $demande->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    @lang('trans.issues_for')
                    {{ $demande->typeDemande->nom_fr ?? '' }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if (count($demande->invalid_reasons) > 0)
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i>
                            @lang('trans.invalid_components')</h6>
                        <ul>
                            @foreach ($demande->invalid_reasons as $component)
                                <li>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $component['type'])) }}:</strong>
                                    {{ $component['identifier'] }}
                                    @if (!empty($component['motif']))
                                        -
                                        <em>{{ $component['motif'] }}</em>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($demande->rejection_reasons_list) > 0)
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-ban"></i>
                            @lang('trans.rejection_reasons')</h6>
                        <ul>
                            @foreach ($demande->rejection_reasons_list as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-dismiss="modal">
                    @lang('trans.close')
                </button>
                @if (auth()->user()->can('edit-demandes'))
                    <a href="{{ route('demandes.edit', $demande->id) }}"
                        class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        @lang('trans.correct_issues')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

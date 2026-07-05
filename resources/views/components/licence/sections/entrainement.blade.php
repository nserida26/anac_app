@props(['entrainementDemandeurs' => null, 'centreFormations' => [], 'simulateurs' => [], 'demandeId' => null, 'demande' => null])

@if ($demande->showEntrainementSection())
    <x-licence.card :title="__('trans.periodic_control')" id="entrainement-section">
            <form id="entrainementForm" action="{{ route('user.store_entrainements') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" id="ent_demande_id" name="demande_id">
                <input type="hidden" id="entrainement_edit_id" name="id" value="">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="ent_type" :value="__('trans.type')" />
                            <select class="form-select select2" id="ent_type" name="type">
                                @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 32]))
                                    <option value="Hors Ligne (SIMU)">@lang('trans.ocp')</option>
                                @endif
                                @if (in_array($demande->typeDemande->id, [1, 3]))
                                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 32, 39]))
                                        <option value="Contrôle en ligne">Contrôle en ligne</option>
                                        <option value="Rafraîchissement au sol">Rafraîchissement au sol</option>
                                        <option value="CRM">CRM</option>
                                        <option value="Sécurité sauvetage">Sécurité sauvetage</option>
                                        <option value="Surete">Surete</option>
                                        <option value="Matière dangereuse">Matière dangereuse</option>
                                    @endif
                                @endif
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="ent_date" :value="__('trans.date')" />
                            <x-form.input type="date" name="date" id="ent_date" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="ent_validite" :value="__('trans.validity')" />
                            <x-form.input type="number" name="validite" id="ent_validite" min="0" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2" id="simulateur_col" style="display: none;">
                        <x-form.group>
                            <x-form.label for="ent_simulateur_id" :value="__('trans.simulator')" />
                            <select class="form-select" id="ent_simulateur_id" name="simulateur_id">
                                <option value="">Sélectionner un simulateur</option>
                                @foreach ($simulateurs as $simulateur)
                                    <option value="{{ $simulateur->id }}">{{ $simulateur->libelle }}</option>
                                @endforeach
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="ent_centre_formation_id" :value="__('trans.training_center')" />
                            <div class="flex gap-2">
                                <select class="form-select select2 flex-1 min-w-0" id="ent_centre_formation_id" name="centre_formation_id">
                                    @foreach ($centreFormations as $centre_formation)
                                        <option value="{{ $centre_formation->id }}">{{ $centre_formation->libelle }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-navy flex-shrink-0 inline-flex items-center justify-center w-[42px] h-[42px] rounded-lg border-0" id="addCenterBtn2" title="@lang('trans.add')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </x-form.group>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="ent_document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="ent_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="entrainementSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang('trans.send')
                        </button>
                        <button type="button" id="entrainementCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($entrainementDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.type')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.validity')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.training_center')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.simulator')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($entrainementDemandeurs as $entrainement_demandeur)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $entrainement_demandeur->type }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $entrainement_demandeur->date }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $entrainement_demandeur->validite }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $entrainement_demandeur->centre_formation }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ optional($entrainement_demandeur->simulateur)->libelle }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($entrainement_demandeur->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $entrainement_demandeur->document) }}')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if (!$entrainement_demandeur->valider)
                                            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-entrainement" data-id="{{ $entrainement_demandeur->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endif
                                        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-entrainement" data-id="{{ $entrainement_demandeur->id }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endisset
    </x-licence.card>
@endif

@push('custom')
<script>
$(document).ready(function() {
    // ===== Entrainement: Simulateur field toggle =====
    function toggleSimulateurField() {
        var typeField = $("#ent_type").val();
        var simulateurOptions = [
            "Hors Ligne (SIMU)", "SIMU", "Simulator",
            "ATE Refresher", "AME Refresher", "RPA Refresher"
        ];
        if (simulateurOptions.includes(typeField)) {
            $("#simulateur_col").show();
        } else {
            $("#simulateur_col").hide();
        }
    }

    if ($('#ent_type').length) {
        toggleSimulateurField();
        $('#ent_type').on('change', toggleSimulateurField);
    }

    // ===== Entrainement: Edit button (AJAX) =====
    $(document).on('click', '.edit-entrainement', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        editSection({ formId: 'entrainementForm', editField: 'entrainement_edit_id', submitBtn: 'entrainementSubmitBtn', cancelBtn: 'entrainementCancelBtn', id: id });
        $.get("{{ route('user.get_entrainement', '') }}/" + id, function(data) {
            $('#entrainementForm select[name="type"]').val(data.type).trigger('change');
            $('#entrainementForm input[name="date"]').val(data.date);
            $('#entrainementForm input[name="validite"]').val(data.validite);
            $('#entrainementForm select[name="centre_formation_id"]').val(data.centre_formation_id).trigger('change');
            if (data.simulateur_id) {
                $('#entrainementForm select[name="simulateur_id"]').val(data.simulateur_id).trigger('change');
            }
        });
    });

    // ===== Entrainement: Cancel button =====
    $(document).on('click', '#entrainementCancelBtn', function() {
        cancelSection({ formId: 'entrainementForm', editField: 'entrainement_edit_id', submitBtn: 'entrainementSubmitBtn', cancelBtn: 'entrainementCancelBtn' });
    });

    // ===== Entrainement: Delete button =====
    $(document).on('click', '.delete-entrainement', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_entrainements', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Entraînement supprimé avec succès.');
    });
});
</script>
@endpush

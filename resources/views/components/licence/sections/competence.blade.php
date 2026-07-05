@props(['competenceDemandeurs' => null, 'centreFormations' => [], 'demandeId' => null, 'demande' => null])

@if ($demande->showCompetenceSection())
    <x-licence.card :title="__('trans.control')">
            <form id="competenceForm" action="{{ route('user.store_competences') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" id="comp_demande_id" name="demande_id">
                <input type="hidden" id="competence_edit_id" name="id" value="">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="comp_type" :value="__('trans.type')" />
                            <select class="form-select select2" name="type" id="comp_type">
                                <option value="Contrôle de compétence linguistique">Contrôle de compétence linguistique</option>
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="comp_niveau" :value="__('trans.level')" />
                            <select class="form-select select2" id="comp_niveau" name="niveau">
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="comp_date" :value="__('trans.date')" />
                            <x-form.input type="date" name="date" id="comp_date" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="validite_lang" :value="__('trans.validity')" />
                            <x-form.input type="number" name="validite" id="validite_lang" min="1" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="comp_centre_formation_id" :value="__('trans.location')" />
                            <select class="form-select select2" id="comp_centre_formation_id" name="centre_formation_id">
                                @foreach ($centreFormations as $centre_formation)
                                    <option value="{{ $centre_formation->id }}">{{ $centre_formation->libelle }}</option>
                                @endforeach
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="comp_document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="comp_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="competenceSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            @lang('trans.send')
                        </button>
                        <button type="button" id="competenceCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($competenceDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.type')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.level')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.validity')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.location')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($competenceDemandeurs as $competence_demandeur)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $competence_demandeur->type }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $competence_demandeur->niveau }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $competence_demandeur->date }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $competence_demandeur->validite === 0 ? '' : $competence_demandeur->validite }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $competence_demandeur->centre_formation }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($competence_demandeur->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $competence_demandeur->document) }}')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if (!$competence_demandeur->valider)
                                            <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-competence" data-id="{{ $competence_demandeur->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endif
                                        <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-competence" data-id="{{ $competence_demandeur->id }}">
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
    // ===== Competence: Validite toggle =====
    function toggleValiditeField() {
        if ($('#validite_lang').val() == '') {
            $('#validite_lang').val('').prop('disabled', true);
        } else {
            $('#validite_lang').prop('disabled', false);
        }
    }

    if ($('#validite_lang').length) {
        toggleValiditeField();
        $('#validite_lang').on('change', toggleValiditeField);
    }

    // ===== Competence: Edit button (AJAX) =====
    $(document).on('click', '.edit-competence', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        editSection({ formId: 'competenceForm', editField: 'competence_edit_id', submitBtn: 'competenceSubmitBtn', cancelBtn: 'competenceCancelBtn', id: id });
        $.get("{{ route('user.get_competence', '') }}/" + id, function(data) {
            $('#competenceForm select[name="type"]').val(data.type).trigger('change');
            $('#competenceForm select[name="niveau"]').val(data.niveau).trigger('change');
            $('#competenceForm input[name="date"]').val(data.date);
            $('#competenceForm input[name="validite"]').val(data.validite);
            $('#competenceForm select[name="centre_formation_id"]').val(data.centre_formation_id).trigger('change');
        });
    });

    // ===== Competence: Cancel button =====
    $(document).on('click', '#competenceCancelBtn', function() {
        cancelSection({ formId: 'competenceForm', editField: 'competence_edit_id', submitBtn: 'competenceSubmitBtn', cancelBtn: 'competenceCancelBtn' });
    });

    // ===== Competence: Delete button =====
    $(document).on('click', '.delete-competence', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_competences', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Compétence supprimée avec succès.');
    });
});
</script>
@endpush

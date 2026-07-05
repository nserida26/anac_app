@props(['experienceDemandeurs' => null, 'demandeId' => null, 'demande' => null])

@if ($demande->showExperienceSection())
    <x-licence.card :title="__('trans.flights')">
            <form id="experienceForm" action="{{ route('user.store_experiences') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" name="demande_id">
                <input type="hidden" id="experience_edit_id" name="id" value="">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="nature" :value="__('trans.flights_type')" />
                            <select class="form-select select2" id="nature" name="nature">
                                <option value="Sur tous types d'aéronefs">@lang('trans.all_types')</option>
                                <option value="Sur les types d'aéronefs exploités par l'employeur">@lang('trans.employer_operated_types')</option>
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="total" :value="__('trans.total')" />
                            <x-form.input type="number" name="total" id="total" min="0" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="six_mois" :value="__('trans.six')" />
                            <x-form.input type="number" name="six_mois" id="six_mois" min="0" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="trois_mois" :value="__('trans.three')" />
                            <x-form.input type="number" name="trois_mois" id="trois_mois" min="0" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="exp_document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="exp_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="experienceSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            @lang('trans.send')</button>
                        <button type="button" id="experienceCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($experienceDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.flights_type')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.total')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.six')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.three')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($experienceDemandeurs as $experience)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $experience->nature }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $experience->total }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $experience->six_mois }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $experience->trois_mois }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($experience->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $experience->document) }}')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if (!$experience->valider)
                                            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-experience" data-id="{{ $experience->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endif
                                        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-experience" data-id="{{ $experience->id }}">
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
    // ===== Experience: Edit button (AJAX) =====
    $(document).on('click', '.edit-experience', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        editSection({ formId: 'experienceForm', editField: 'experience_edit_id', submitBtn: 'experienceSubmitBtn', cancelBtn: 'experienceCancelBtn', id: id });
        $.get("{{ route('user.get_experience', '') }}/" + id, function(data) {
            $('#experienceForm select[name="nature"]').val(data.nature).trigger('change');
            $('#experienceForm input[name="total"]').val(data.total);
            $('#experienceForm input[name="six_mois"]').val(data.six_mois);
            $('#experienceForm input[name="trois_mois"]').val(data.trois_mois);
        });
    });

    // ===== Experience: Cancel button =====
    $(document).on('click', '#experienceCancelBtn', function() {
        cancelSection({ formId: 'experienceForm', editField: 'experience_edit_id', submitBtn: 'experienceSubmitBtn', cancelBtn: 'experienceCancelBtn' });
    });

    // ===== Experience: Delete button =====
    $(document).on('click', '.delete-experience', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_experiences', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Expérience supprimée avec succès.');
    });
});
</script>
@endpush

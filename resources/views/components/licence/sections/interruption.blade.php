@props(['interruptionDemandeurs' => null, 'demandeId' => null, 'demande' => null])

@if ($demande->showInterruptionSection())
    <x-licence.card :title="__('trans.interruptions')">
            <form id="interruptionForm" action="{{ route('user.store_interruptions') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" id="inter_demande_id" name="demande_id">
                <input type="hidden" id="interruption_edit_id" name="id" value="">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="inter_date_debut" :value="__('trans.start_date')" />
                            <x-form.input type="date" name="date_debut" id="inter_date_debut" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="inter_date_fin" :value="__('trans.end_date')" />
                            <x-form.input type="date" name="date_fin" id="inter_date_fin" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="inter_raison" :value="__('trans.reason')" />
                            <x-form.input type="text" name="raison" id="inter_raison" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="inter_document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="inter_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="interruptionSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang('trans.send')
                        </button>
                        <button type="button" id="interruptionCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($interruptionDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.start_date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.end_date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.reason')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($interruptionDemandeurs as $interruption_demandeur)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $interruption_demandeur->date_debut }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $interruption_demandeur->date_fin }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $interruption_demandeur->raison }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($interruption_demandeur->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $interruption_demandeur->document) }}')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if (!$interruption_demandeur->valider)
                                            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-interruption" data-id="{{ $interruption_demandeur->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endif
                                        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-interruption" data-id="{{ $interruption_demandeur->id }}">
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
    // ===== Interruption: Edit button (AJAX) =====
    $(document).on('click', '.edit-interruption', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        editSection({ formId: 'interruptionForm', editField: 'interruption_edit_id', submitBtn: 'interruptionSubmitBtn', cancelBtn: 'interruptionCancelBtn', id: id });
        $.get("{{ route('user.get_interruption', '') }}/" + id, function(data) {
            $('#interruptionForm input[name="date_debut"]').val(data.date_debut);
            $('#interruptionForm input[name="date_fin"]').val(data.date_fin);
            $('#interruptionForm input[name="raison"]').val(data.raison);
        });
    });

    // ===== Interruption: Cancel button =====
    $(document).on('click', '#interruptionCancelBtn', function() {
        cancelSection({ formId: 'interruptionForm', editField: 'interruption_edit_id', submitBtn: 'interruptionSubmitBtn', cancelBtn: 'interruptionCancelBtn' });
    });

    // ===== Interruption: Delete button =====
    $(document).on('click', '.delete-interruption', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_interruptions', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Interruption supprimée avec succès.');
    });
});
</script>
@endpush

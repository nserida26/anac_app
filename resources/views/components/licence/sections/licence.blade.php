@props(['licenceDemandeurs' => null, 'autorites' => [], 'demandeId' => null, 'demande' => null])

@if ($demande->showLicenceSection())
    <x-licence.card :title="__('trans.license')" id="licence-section">
            <form id="licenceForm" action="{{ route('user.store_licences') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" id="demande_id" name="demande_id">
                <input type="hidden" id="licence_edit_id" name="id" value="">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="num_licence" :value="__('trans.license_number')" />
                            <x-form.input type="text" name="num_licence" id="num_licence" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="date_licence" :value="__('trans.license_date')" />
                            <x-form.input type="date" name="date_licence" id="date_licence" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="autorite_id" :value="__('trans.authority')" />
                            <select class="form-select select2" id="autorite_id" name="autorite_id">
                                @foreach ($autorites as $autorite)
                                    <option value="{{ $autorite->id }}">{{ $autorite->libelle }}</option>
                                @endforeach
                            </select>
                        </x-form.group>
                    </div>
                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="lieu_delivrance" :value="__('trans.location')" />
                            <x-form.input type="text" name="lieu_delivrance" id="lieu_delivrance" />
                        </x-form.group>
                    </div>
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="licence_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="licenceSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang('trans.send')
                        </button>
                        <button type="button" id="licenceCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($licenceDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.license_date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.license_number')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.authority')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.location')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($licenceDemandeurs as $licence_demandeur)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $licence_demandeur->date_licence }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $licence_demandeur->num_licence }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $licence_demandeur->autorite->libelle }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $licence_demandeur->lieu_delivrance }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($licence_demandeur->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $licence_demandeur->document) }}')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if (!$licence_demandeur->valider)
                                            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-licence"
                                                data-id="{{ $licence_demandeur->id }}"
                                                data-num-licence="{{ $licence_demandeur->num_licence }}"
                                                data-date-licence="{{ $licence_demandeur->date_licence }}"
                                                data-autorite-id="{{ $licence_demandeur->autorite_id }}"
                                                data-lieu-delivrance="{{ $licence_demandeur->lieu_delivrance }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                        @endif
                                        <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-licence" data-id="{{ $licence_demandeur->id }}">
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
    // ===== Licence: Edit button =====
    $(document).on('click', '.edit-licence', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        editSection({ formId: 'licenceForm', editField: 'licence_edit_id', submitBtn: 'licenceSubmitBtn', cancelBtn: 'licenceCancelBtn', id: id });
        $('#num_licence').val($(this).data('num-licence'));
        $('#date_licence').val($(this).data('date-licence'));
        $('#autorite_id').val($(this).data('autorite-id')).trigger('change');
        $('#lieu_delivrance').val($(this).data('lieu-delivrance'));
    });

    // ===== Licence: Cancel button =====
    $(document).on('click', '#licenceCancelBtn', function() {
        cancelSection({ formId: 'licenceForm', editField: 'licence_edit_id', submitBtn: 'licenceSubmitBtn', cancelBtn: 'licenceCancelBtn' });
    });

    // ===== Licence: Delete button =====
    $(document).on('click', '.delete-licence', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_licences', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Licence supprimée avec succès.');
    });
});
</script>
@endpush

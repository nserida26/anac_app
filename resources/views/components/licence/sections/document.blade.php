@props(['documents' => null, 'typeDocuments' => [], 'demandeId' => null, 'demande' => null])

<x-licence.card :title="__('trans.documents')">
        <form id="documentForm" action="{{ route('user.store_documents') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" value="{{ $demandeId }}" name="demande_id">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <x-form.group>
                        <x-form.label for="doc_type" :value="__('trans.document_type')" />
                        <select class="form-select select2" id="doc_type" name="type_document_id[]">
                            @foreach ($typeDocuments as $type_document)
                                <option value="{{ $type_document->id }}">{{ LaravelLocalization::getCurrentLocale() == 'fr' ? $type_document->nom_fr : $type_document->nom_en }}</option>
                            @endforeach
                        </select>
                    </x-form.group>
                </div>
                <div class="md:col-span-4">
                    <x-form.group>
                        <x-form.label for="doc_piece" :value="__('trans.file')" />
                        <input type="file" class="form-input" id="doc_piece" name="pieces[]" accept="application/pdf" multiple>
                    </x-form.group>
                </div>
                <div class="md:col-span-4">
                    <x-form.group>
                        <x-form.label>&nbsp;</x-form.label>
                        <button type="submit" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang('trans.add')
                        </button>
                    </x-form.group>
                </div>
            </div>
        </form>

        @isset($documents)
            <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.document_type')</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.file')</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($documents as $document)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-4 py-3 text-sm text-slate-700">{{ LaravelLocalization::getCurrentLocale() == 'fr' ? $document->nom_fr : $document->nom_en }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <a href="{{ asset('/uploads/' . $document->url) }}" target="_blank" class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-document" data-id="{{ $document->id }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endisset    </x-licence.card>

@push('custom')
<script>
$(document).ready(function() {
    // ===== Document: Delete button =====
    $(document).on('click', '.delete-document', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_documents', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Document supprimé avec succès.');
    });
});
</script>
@endpush

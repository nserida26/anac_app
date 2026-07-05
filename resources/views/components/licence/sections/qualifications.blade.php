@props(['qualificationDemandeurs' => null, 'qualifications' => [], 'centreFormations' => [], 'typeAvions' => [], 'demandeId' => null, 'demande' => null])

@if ($demande->showQualificationSection())
    <x-licence.card title="{{ __('QUALIFICATIONS ') }}" id="qualification-section" class="my-2">
        <x-slot name="badge">
            @if (in_array($demande->typeDemande->id, [4]))
                <span class="px-2.5 py-0.5 text-xs font-semibold text-amber-700 bg-amber-50 rounded-full border border-amber-200">@lang('trans.lcp')</span>
            @endif
        </x-slot:badge>
            <form id="qualificationForm" action="{{ route('user.store_qualifications') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" value="{{ $demandeId }}" id="demande_id" name="demande_id">
                <input type="hidden" id="qualification_edit_id" name="id" value="">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label>@lang('trans.ratings')</x-form.label>
                            <x-form.select id="qualification_id" name="qualification_id">
                                @foreach ($qualifications as $qualification)
                                    <option value="{{ $qualification->id }}" data-type="{{ $qualification->libelle }}">{{ $qualification->libelle }}</option>
                                @endforeach
                            </x-form.select>
                        </x-form.group>
                    </div>

                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="date_examen" :value="__('trans.exam_date')" />
                            <x-form.input type="date" name="date_examen" id="qualif_date_examen" />
                        </x-form.group>
                    </div>

                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="centre_formation_id" :value="__('trans.training_center')" />
                            <div class="flex gap-2">
                                <x-form.select class="flex-1 min-w-0" id="qualif_centre_formation_id" name="centre_formation_id">
                                    @foreach ($centreFormations as $centre_formation)
                                        <option value="{{ $centre_formation->id }}">{{ $centre_formation->libelle }}</option>
                                    @endforeach
                                </x-form.select>
                                <button type="button" class="btn-navy flex-shrink-0 inline-flex items-center justify-center w-[42px] h-[42px] rounded-lg border-0" id="addCenterBtn1" title="@lang('trans.add')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </x-form.group>
                    </div>

                    <div class="md:col-span-2">
                        <x-form.group>
                            <x-form.label for="lieu" :value="__('trans.location')" />
                            <x-form.input type="text" name="lieu" id="qualif_lieu" />
                        </x-form.group>
                    </div>

                    <div class="md:col-span-3">
                        <x-form.group>
                            <x-form.label for="document" :value="__('trans.proof')" />
                            <input type="file" class="form-input" id="qualif_document" name="document" accept="application/pdf">
                        </x-form.group>
                    </div>
                </div>

                <div class="md:col-span-3" id="type_avion_col" style="display: none;">
                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 36, 39]))
                        <x-form.group>
                            <x-form.label for="type_avion_id" :value="__('trans.plane_type')" />
                            <x-form.select id="qualif_type_avion_id" name="type_avion_id">
                                @foreach ($typeAvions as $type_avion)
                                    <option value="{{ $type_avion->id }}">
                                        {{ $type_avion->code }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        </x-form.group>
                    @endif
                    @if ($demande->typeLicence->id === 34)
                        <x-form.group>
                            <x-form.label for="rpa" :value="__('trans.rpa')" />
                            <x-form.select class="select2" id="qualif_rpa" name="rpa">
                                <option value="type1">RPA type 1</option>
                                <option value="type2">RPA type 2</option>
                                <option value="type3">RPA type 3</option>
                            </x-form.select>
                        </x-form.group>
                    @endif
                </div>
                <div class="md:col-span-3" id="type_engine_col" style="display: none;">
                    @if ($demande->typeLicence->id === 33)
                        <x-form.group>
                            <x-form.label for="ulm" :value="__('trans.ulm')" />
                            <x-form.select class="select2" id="qualif_ulm" name="ulm">
                                <option value="Paramotor">Paramotor</option>
                                <option value="Glider type aircraft">Glider type aircraft</option>
                                <option value="Multi Axes">Multi Axes</option>
                                <option value="Ultra light airplane">Ultra light airplane</option>
                                <option value="Ultralight oetostats">Ultralight oetostats</option>
                                <option value="Ultra light helicopter">Ultra light helicopter</option>
                            </x-form.select>
                        </x-form.group>
                    @endif
                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32]))
                        <x-form.group>
                            <x-form.label for="type_moteur" :value="__('trans.engine_type')" />
                            <x-form.select class="select2" id="qualif_type_moteur" name="type_moteur">
                                <option value="SE">SE</option>
                                <option value="ME">ME</option>
                            </x-form.select>
                        </x-form.group>
                    @endif
                </div>
                <div class="md:col-span-3" id="instructeur_privilege_col" style="display: none;">
                    <x-form.group>
                        <x-form.label for="type_privilege" :value="__('trans.privilege')" />
                        <x-form.select class="select2" id="qualif_type_privilege" name="type_privilege">
                            @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 33]))
                                <option value="TRI">TRI</option>
                                <option value="IRI">IRI</option>
                                <option value="FI">FI</option>
                                <option value="CRI">CRI</option>
                                <option value="SFI">SFI</option>
                                <option value="GI">GI</option>
                            @endif
                            @if ($demande->typeLicence->id === 35)
                                <option value="ICQ">ICQ</option>
                            @endif
                            @if (in_array($demande->typeLicence->id, [37, 38]))
                                <option value="AMT Instructor">AMT Instructor</option>
                            @endif
                            @if ($demande->typeLicence->id === 39)
                                <option value="PNC Instructor">PNC Instructor</option>
                            @endif
                            @if ($demande->typeLicence->id === 36)
                                <option value="ATE Instructor">ATE Instructor</option>
                            @endif
                            @if ($demande->typeLicence->id === 34)
                                <option value="RPA Instructor">RPA Instructor</option>
                            @endif
                        </x-form.select>
                    </x-form.group>
                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 33]))
                        <x-form.group>
                            <x-form.label for="machine" :value="__('trans.machine')" />
                            <x-form.select class="select2" id="qualif_machine" name="machine">
                                <option value="A">A</option>
                                <option value="H">H</option>
                            </x-form.select>
                        </x-form.group>
                        <x-form.group>
                            <x-form.label for="type_avion_id" :value="__('trans.plane_type')" />
                            <x-form.select class="select2" id="qualif_type_avion_id_2" name="type_avion_id">
                                @foreach ($typeAvions as $type_avion)
                                    <option value="{{ $type_avion->id }}">{{ $type_avion->code }}</option>
                                @endforeach
                            </x-form.select>
                        </x-form.group>
                    @endif
                </div>

                <div class="md:col-span-3" id="examinateur_privilege_col" style="display: none;">
                    <x-form.group>
                        <x-form.label for="type_privilege" :value="__('trans.privilege')" />
                        <x-form.select class="form-select select2" id="exam_type_privilege" name="type_privilege">
                            @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 33]))
                                <option value="TRE">TRE</option>
                                <option value="IRE">IRE</option>
                                <option value="FE">FE</option>
                                <option value="CRE">CRE</option>
                                <option value="SFE">SFE</option>
                                <option value="FIE">FIE</option>
                            @endif
                            @if ($demande->typeLicence->id === 35)
                                <option value="ATC Examiner">ATC Examiner</option>
                            @endif
                            @if (in_array($demande->typeLicence->id, [37, 38]))
                                <option value="AMT Examiner">AMT Examiner</option>
                            @endif
                            @if ($demande->typeLicence->id === 39)
                                <option value="PNC Examiner">PNC Examiner</option>
                            @endif
                            @if ($demande->typeLicence->id === 36)
                                <option value="ATE Examiner">ATE Examiner</option>
                            @endif
                            @if ($demande->typeLicence->id === 34)
                                <option value="RPA Examiner">RPA Examiner</option>
                            @endif
                        </x-form.select>
                    </x-form.group>
                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 33]))
                        <x-form.group>
                            <x-form.label for="machine" :value="__('trans.machine')" />
                            <x-form.select class="select2" id="exam_machine" name="machine">
                                <option value="A">A</option>
                                <option value="H">H</option>
                            </x-form.select>
                        </x-form.group>
                        <x-form.group>
                            <x-form.label for="type_avion_id" :value="__('trans.plane_type')" />
                            <x-form.select class="select2" id="exam_type_avion_id" name="type_avion_id">
                                @foreach ($typeAvions as $type_avion)
                                    <option value="{{ $type_avion->id }}">{{ $type_avion->code }}</option>
                                @endforeach
                            </x-form.select>
                        </x-form.group>
                    @endif
                </div>

                <div class="md:col-span-3" id="atc_qualifications_col" style="display: none;">
                    <x-form.group>
                        <x-form.label for="atc" :value="__('trans.atc')" />
                        <x-form.select class="select2" id="qualif_atc" name="atc[]" multiple>
                            <option value="ADC">ADC</option>
                            <option value="APP">APP</option>
                            <option value="APS">APS</option>
                            <option value="APRC">APRC</option>
                            <option value="ACP">ACP</option>
                            <option value="ACS">ACS</option>
                        </x-form.select>
                    </x-form.group>
                </div>

                <div class="md:col-span-3" id="amt_qualifications_col" style="display: none;">
                    <x-form.group>
                        <x-form.label for="amt" :value="__('trans.amt')" />
                        <x-form.select class="select2" id="qualif_amt" name="amt[]" multiple>
                            <option value="A(A)">A(A)</option>
                            <option value="A(H)">A(H)</option>
                            <option value="B1(A)">B1(A)</option>
                            <option value="B1(H)">B1(H)</option>
                            <option value="B2(A)">B2(A)</option>
                            <option value="B2(H)">B2(H)</option>
                            <option value="B3(A)">B3(A)</option>
                            <option value="B3(H)">B3(H)</option>
                            <option value="C(A)">C(A)</option>
                            <option value="C(H)">C(H)</option>
                        </x-form.select>
                    </x-form.group>
                    @if (in_array($demande->typeLicence->id, [37, 38]))
                        <x-form.group>
                            <x-form.label for="machine" :value="__('trans.machine')" />
                            <x-form.select class="form-select select2" id="amt_machine" name="machine">
                                <option value="A">A</option>
                                <option value="H">H</option>
                            </x-form.select>
                        </x-form.group>
                        <x-form.group>
                            <x-form.label for="type_avion_id" :value="__('trans.plane_type')" />
                            <select class="form-select select2" id="amt_type_avion_id" name="type_avion_id">
                                @foreach ($typeAvions as $type_avion)
                                    <option value="{{ $type_avion->id }}">{{ $type_avion->code }}</option>
                                @endforeach
                            </select>
                        </x-form.group>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-12 text-right">
                        <button type="submit" id="qualificationSubmitBtn" class="btn-gold inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border-0 cursor-pointer transition-all duration-200 hover:shadow-lg hover:shadow-amber-200/30 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> @lang('trans.send')
                        </button>
                        <button type="button" id="qualificationCancelBtn" class="btn-gray inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer transition-all duration-200 ml-2" style="display:none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Annuler
                        </button>
                    </div>
                </div>
            </form>

            @isset($qualificationDemandeurs)
                <div class="overflow-x-auto rounded-xl border border-slate-200 mt-3">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.ratings')</th>
                                @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 36, 37, 38, 39]))
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.plane_type')</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.machine')</th>
                                @endif
                                @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32]))
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.engine_type')</th>
                                @endif
                                @if ($demande->typeLicence->id !== 33)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.privilege')</th>
                                @endif
                                @if (in_array($demande->typeLicence->id, [37, 38]))
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.amt')</th>
                                @endif
                                @if ($demande->typeLicence->id === 35)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.atc')</th>
                                @endif
                                @if ($demande->typeLicence->id === 34)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.rpa')</th>
                                @endif
                                @if ($demande->typeLicence->id === 33)
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.ulm')</th>
                                @endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.exam_date')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.training_center')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.location')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.proof')</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($qualificationDemandeurs as $qualification_demandeur)
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->qualification }}</td>
                                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32, 36, 37, 38, 39]))
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ optional($qualification_demandeur->typeAvion)->code }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->machine }}</td>
                                    @endif
                                    @if (in_array($demande->typeLicence->id, [27, 28, 29, 30, 31, 32]))
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->type_moteur }}</td>
                                    @endif
                                    @if ($demande->typeLicence->id !== 33)
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->type_privilege }}</td>
                                    @endif
                                    @if (in_array($demande->typeLicence->id, [37, 38]))
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->amt_display }}</td>
                                    @endif
                                    @if ($demande->typeLicence->id === 35)
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->atc_display }}</td>
                                    @endif
                                    @if ($demande->typeLicence->id === 34)
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->rpa }}</td>
                                    @endif
                                    @if ($demande->typeLicence->id === 33)
                                        <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->ulm }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->date_examen }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->centre_formation }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $qualification_demandeur->lieu }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        @if ($qualification_demandeur->document)
                                            <button class="btn-navy inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium" onclick="openPdfModal('{{ asset('/uploads/' . $qualification_demandeur->document) }}')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-700">
                                        <div class="flex items-center gap-1.5">
                                            @if (!$qualification_demandeur->valider)
                                                <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors no-underline edit-qualification" data-id="{{ $qualification_demandeur->id }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                            @endif
                                            <button class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer delete-qualification" data-id="{{ $qualification_demandeur->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
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
    // ===== Qualification: Dynamic field toggle =====
    function toggleQualificationFields() {
        var selectedText = $('#qualification_id option:selected').data('type');
        $('#type_avion_col, #type_engine_col, #instructeur_privilege_col, #examinateur_privilege_col, #atc_qualifications_col, #amt_qualifications_col').hide();
        if (selectedText === 'Qualification Type Machine' || selectedText === 'Qualification instructeur' || selectedText === 'Autorisation examinateur') {
            $('#type_avion_col').show();
        }
        if (selectedText === 'Qualification de Class') {
            $('#type_engine_col').show();
        }
        if (selectedText === 'Qualification instructeur') {
            $('#instructeur_privilege_col').show();
        }
        if (selectedText === 'Autorisation examinateur') {
            $('#examinateur_privilege_col').show();
        }
        if (selectedText === 'Qualifications ATC') {
            $('#atc_qualifications_col').show();
        }
        if (selectedText === 'Qualifications AMT') {
            $('#amt_qualifications_col').show();
        }
    }

    if ($('#qualification_id').length) {
        toggleQualificationFields();
        $('#qualification_id').on('change', toggleQualificationFields);
    }

    // ===== Qualification: Edit button =====
    $(document).on('click', '.edit-qualification', function() {
        var qualificationId = $(this).data('id');
        editSection({ formId: 'qualificationForm', editField: 'qualification_edit_id', submitBtn: 'qualificationSubmitBtn', cancelBtn: 'qualificationCancelBtn', id: qualificationId });
        $.get("{{ route('user.get_qualification', '') }}/" + qualificationId, function(data) {
            $('#qualification_id').val(data.qualification_id).trigger('change');
            $('#qualificationForm input[name="date_examen"]').val(data.date_examen);
            $('#qualificationForm input[name="lieu"]').val(data.lieu);
            $('#qualificationForm select[name="centre_formation_id"]').val(data.centre_formation_id).trigger('change');
        });
    });

    // ===== Qualification: Cancel button =====
    $(document).on('click', '#qualificationCancelBtn', function() {
        cancelSection({ formId: 'qualificationForm', editField: 'qualification_edit_id', submitBtn: 'qualificationSubmitBtn', cancelBtn: 'qualificationCancelBtn' });
    });

    // ===== Qualification: Delete button =====
    $(document).on('click', '.delete-qualification', function() {
        var id = $(this).data('id');
        deleteSection("{{ route('user.destroy_qualifications', ':id') }}".replace(':id', id), $(this).closest('tr'), 'Qualification supprimée avec succès.');
    });
});
</script>
@endpush

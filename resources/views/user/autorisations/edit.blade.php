<x-app-user-layout :title="__('trans.edit_application')">

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush


        <div class="row justify-content-center">
            <div class="col-md-12">
                <!-- En-tête du dossier -->
                <div class="card anac-hero">
                    <div class="card-body">
                        <span class="anac-hero__eyebrow">
                            <i class="fas fa-shield-alt"></i> @lang('trans.type_demande')
                        </span>

                        <h1 class="anac-hero__title">{{ $demandeAutorisation->type->libelle }}</h1>
                        <p class="anac-hero__subtitle">
                            @if ($demandeAutorisation->type_demande_autorisation_id == 3)
                                {{ $demandeAutorisation->type_vol_names }}
                            @else
                                {{ $demandeAutorisation->typeVol->nom ?? __('trans.not_available') }}
                            @endif
                        </p>

                        <div class="anac-hero__meta">
                            <span class="anac-hero__chip">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="anac-hero__label">@lang('trans.start_date')</span>
                                {{ $demandeAutorisation->date_debut }}
                                <i class="fas fa-arrow-right"></i>
                                <span class="anac-hero__label">@lang('trans.end_date')</span>
                                {{ $demandeAutorisation->date_fin }}
                            </span>

                            <span class="anac-hero__chip">
                                <i class="fas fa-user-tie"></i>
                                <span class="anac-hero__label">@lang('trans.applicant')</span>
                                {{ Auth::user()->demandeur->np }}
                            </span>

                            <span class="anac-hero__chip">
                                <i class="fas fa-tag"></i>
                                <span class="anac-hero__label">@lang('trans.object')</span>
                                {{ $demandeAutorisation->objet ? strtoupper($demandeAutorisation->objet) : __('trans.not_available') }}
                            </span>

                            @if (!empty($demandeAutorisation->sous_validite))
                                <span class="anac-hero__chip">
                                    <i class="fas fa-clock"></i>
                                    <span class="anac-hero__label">@lang('trans.validity')</span>
                                    +{{ $demandeAutorisation->sous_validite }} H
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Transport de dépouille mortelle (type 4) : ni avion/immatriculation, ni équipage --}}
                @php
                    $isDepouilleMortelle = $demandeAutorisation->type_demande_autorisation_id == 4;
                    // Une fois la demande soumise (compagnie_cree_demande), elle n'est plus modifiable
                    // par le demandeur : la page reste accessible mais en lecture seule ("Voir").
                    $readonly = (bool) optional($demandeAutorisation->etatDemande)->compagnie_cree_demande;
                @endphp

                @if ($readonly)
                    <div class="auth-alert auth-alert--info">
                        <i class="fas fa-lock"></i>
                        @lang('trans.demande_readonly_notice')
                    </div>
                    <style>
                        /* Lecture seule : masquer tous les contrôles de création/modification/suppression */
                        #showAvionFormBtn,
                        #showVolFormBtn,
                        #avionForm,
                        #volForm,
                        #crewForm,
                        #mdnForm,
                        #fretForm,
                        #receivingPartyForm,
                        #deceasedPersonForm,
                        .upload-document,
                        #addAeroportBtn,
                        #addEscaleBtn,
                        #addTypeAvionBtn,
                        #addCompanyBtn,
                        #addCompanyBtnDemande,
                        #saveOperateurBtn,
                        .edit-avion,
                        .delete-avion,
                        .edit-vol,
                        .delete-vol,
                        .edit-membre,
                        .delete-membre,
                        .edit-mdn,
                        .delete-mdn,
                        .cancel-edit-mdn,
                        .edit-fret,
                        .delete-fret,
                        .edit-party,
                        .delete-party,
                        .edit-personne,
                        .delete-personne,
                        .replace-document,
                        .delete-document {
                            display: none !important;
                        }
                    </style>
                @endif


                @include('user.autorisations.partials.operator')
                @include('user.autorisations.partials.aircraft')
                @include('user.autorisations.partials.flights')
                @include('user.autorisations.partials.crew')
                @include('user.autorisations.partials.mdn')
                @include('user.autorisations.partials.freight')
                @include('user.autorisations.partials.receiving-party')
                @include('user.autorisations.partials.deceased-persons')

                <!-- Assistance Section -->
                {{-- <div class="card card-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Assistance escale et PEA</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="assistanceForm" action="{{ url('/user/assistance') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" value="{{ $demandeAutorisation->id }}" id="demande_autorisation_id"
                                name="demande_autorisation_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="structure_assistance">Structure d'assistance en escale</label>
                                        <input id="structure_assistance" name="structure_assistance"
                                            value="{{ $assistance->structure_assistance ?? old('structure_assistance') }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="etat_pea">Etat de délivrance PEA</label>
                                        <input id="etat_pea" name="etat_pea"
                                            value="{{ $assistance->etat_pea ?? old('etat_pea') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="renseignements_divers">Renseignements divers</label>
                                        <textarea class="form-control" id="renseignements_divers" name="renseignements_divers" rows="3">{{ $assistance->renseignements_divers ?? old('renseignements_divers') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success float-right">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                </div>
                            </div>
                        </form>
                        @if (isset($demandeAutorisation->assistance))
                            <div class="row mt-4">
                                <div class="col-lg-12">
                                    <div class="callout callout-info">
                                        <h5>Dernière mise à jour</h5>
                                        <p>
                                            <strong>Structure d'assistance:</strong>
                                            {{ $vol->assistance->structure_assistance ?? 'Non renseigné' }}<br>
                                            <strong>Etat PEA:</strong>
                                            {{ $vol->assistance->etat_pea ?? 'Non renseigné' }}<br>
                                            <strong>Informations:</strong>
                                            {{ $vol->assistance->renseignements_divers ?? 'Aucune information supplémentaire' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div> --}}

                @include('user.autorisations.partials.documents')
                @include('user.autorisations.partials.document-replace-modal')
                @include('user.autorisations.partials.submit')

            </div>
        </div>

    @include('user.autorisations.partials.modals')

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- dropzonejs -->
    <!-- Page specific script -->
@endpush

    @include('user.autorisations.partials.scripts-common')

</x-app-user-layout>

{{--
    Formulaire d'un rapport médical, partagé par l'examinateur individuel et le centre d'expertise médicale.
    Paramètres :
      $action        URL d'envoi
      $examen        (optionnel) rapport modifié ; les fichiers deviennent alors facultatifs
      $demandeur     (optionnel) demandeur imposé (parcours examinateur après recherche par licence)
      $demandeurs    (optionnel) liste de choix du demandeur (parcours centre)
      $examinateurs  (optionnel) examinateurs validés du centre, parmi lesquels choisir
      $annulation    URL du bouton Annuler
--}}
@php
    $examen = $examen ?? null;
    $creation = $examen === null;
@endphp
<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if ($creation)
        @if (!empty($demandeur))
            <input type="hidden" name="demandeur_id" value="{{ $demandeur->id }}">
        @elseif (isset($demandeurs))
            <div class="form-group">
                <label for="demandeur_id">@lang('trans.applicant') <span class="text-danger">*</span></label>
                <select name="demandeur_id" id="demandeur_id" class="form-control select2" required style="width: 100%">
                    <option value="">--</option>
                    @foreach ($demandeurs as $choix)
                        <option value="{{ $choix->id }}" {{ old('demandeur_id') == $choix->id ? 'selected' : '' }}>
                            {{ $choix->np }}{{ $choix->date_naissance ? ' (' . $choix->date_naissance . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    @endif

    @isset($examinateurs)
        <div class="form-group">
            <label for="examinateur_id">@lang('trans.medical_examiner') <span class="text-danger">*</span></label>
            <select name="examinateur_id" id="examinateur_id" class="form-control" required>
                <option value="">--</option>
                @foreach ($examinateurs as $choix)
                    <option value="{{ $choix->id }}" {{ old('examinateur_id', optional($examen)->examinateur_id) == $choix->id ? 'selected' : '' }}>
                        {{ $choix->nom_complet }} — @lang('trans.approval_number') {{ $choix->numero_licence_examinateur }}
                    </option>
                @endforeach
            </select>
            @if ($examinateurs->isEmpty())
                <small class="form-text text-danger">@lang('trans.aucun_examinateur_valide')</small>
            @endif
        </div>
    @endisset

    <div class="form-group">
        <label for="date_examen">@lang('trans.exam_date') <span class="text-danger">*</span></label>
        <input type="date" name="date_examen" id="date_examen" class="form-control" required max="{{ date('Y-m-d') }}"
            value="{{ old('date_examen', optional($examen)->date_examen ?? date('Y-m-d')) }}">
    </div>

    <div class="form-group">
        <label for="validite">@lang('trans.validite_mois') <span class="text-danger">*</span></label>
        <input type="number" name="validite" id="validite" class="form-control" required min="1"
            value="{{ old('validite', optional($examen)->validite) }}">
    </div>

    <div class="form-group">
        <label for="aptitude">@lang('trans.medical_fitness') <span class="text-danger">*</span></label>
        <select name="aptitude" id="aptitude" class="form-control" required>
            <option value="">--</option>
            @foreach (['Apte' => 'trans.fit', 'Inapte' => 'trans.unfit'] as $valeur => $libelle)
                <option value="{{ $valeur }}" {{ old('aptitude', optional($examen)->aptitude) === $valeur ? 'selected' : '' }}>@lang($libelle)</option>
            @endforeach
        </select>
    </div>

    @foreach (['rapport' => 'trans.report', 'attestation' => 'trans.certificate'] as $champ => $libelle)
        <div class="form-group">
            <label for="{{ $champ }}">@lang($libelle) (PDF, JPG, PNG)
                @if ($creation)<span class="text-danger">*</span>@endif
            </label>
            <input type="file" name="{{ $champ }}" id="{{ $champ }}" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png"
                {{ $creation ? 'required' : '' }}>
            @unless ($creation)
                <small class="form-text text-muted">@lang('trans.fichier_conserve_si_vide')</small>
            @endunless
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> @lang('trans.save')</button>
    <a href="{{ $annulation }}" class="btn btn-secondary">@lang('trans.cancel')</a>
</form>

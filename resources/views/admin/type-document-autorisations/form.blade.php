<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('type_vol_id', 'Type Vol') }}
            {{ Form::select('type_vol_id', $typeVols->pluck('nom', 'id'), $typeDocumentAutorisation->type_vol_id, ['class' => 'form-control' . ($errors->has('type_vol_id') ? ' is-invalid' : ''), 'placeholder' => 'Type Vol']) }}
            {!! $errors->first('type_vol_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('type_demande_autorisation_id', 'Type Demande') }}
            {{ Form::select('type_demande_autorisation_id', $typeDemandes->pluck('libelle', 'id'), $typeDocumentAutorisation->type_demande_autorisation_id, ['class' => 'form-control' . ($errors->has('type_demande_autorisation_id') ? ' is-invalid' : ''), 'placeholder' => 'Type Demande']) }}
            {!! $errors->first('type_demande_autorisation_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('nom_fr') }}
            {{ Form::text('nom_fr', $typeDocumentAutorisation->nom_fr, ['class' => 'form-control' . ($errors->has('nom_fr') ? ' is-invalid' : ''), 'placeholder' => 'Nom Fr']) }}
            {!! $errors->first('nom_fr', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('nom_en') }}
            {{ Form::text('nom_en', $typeDocumentAutorisation->nom_en, ['class' => 'form-control' . ($errors->has('nom_en') ? ' is-invalid' : ''), 'placeholder' => 'Nom En']) }}
            {!! $errors->first('nom_en', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

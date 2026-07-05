<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('type_licence_id') }}
            {{ Form::select('type_licence_id', $typeLicences->pluck('nom', 'id'), $typeDocument->type_licence_id, ['class' => 'form-control' . ($errors->has('type_licence_id') ? ' is-invalid' : ''), 'placeholder' => 'Type Licence']) }}
            {!! $errors->first('type_licence_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('type_demande_id') }}
            {{ Form::select('type_demande_id', $typeDemandes->pluck('nom_fr', 'id'), $typeDocument->type_demande_id, ['class' => 'form-control' . ($errors->has('type_demande_id') ? ' is-invalid' : ''), 'placeholder' => 'Type Demande']) }}
            {!! $errors->first('type_demande_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('nom_fr') }}
            {{ Form::text('nom_fr', $typeDocument->nom_fr, ['class' => 'form-control' . ($errors->has('nom_fr') ? ' is-invalid' : ''), 'placeholder' => 'Nom Fr']) }}
            {!! $errors->first('nom_fr', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('nom_en') }}
            {{ Form::text('nom_en', $typeDocument->nom_en, ['class' => 'form-control' . ($errors->has('nom_en') ? ' is-invalid' : ''), 'placeholder' => 'Nom En']) }}
            {!! $errors->first('nom_en', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

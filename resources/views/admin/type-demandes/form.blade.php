<div class="box box-info padding-1">
    <div class="box-body">
        
        <div class="form-group">
            {{ Form::label('nom_en') }}
            {{ Form::text('nom_en', $typeDemande->nom_en, ['class' => 'form-control' . ($errors->has('nom_en') ? ' is-invalid' : ''), 'placeholder' => 'Nom En']) }}
            {!! $errors->first('nom_en', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('nom_fr') }}
            {{ Form::text('nom_fr', $typeDemande->nom_fr, ['class' => 'form-control' . ($errors->has('nom_fr') ? ' is-invalid' : ''), 'placeholder' => 'Nom Fr']) }}
            {!! $errors->first('nom_fr', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>
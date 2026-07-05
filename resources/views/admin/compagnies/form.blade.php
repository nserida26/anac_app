<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('nom_entreprise') }}
            {{ Form::text('nom_entreprise', $compagny->nom_entreprise, ['class' => 'form-control' . ($errors->has('nom_entreprise') ? ' is-invalid' : ''), 'placeholder' => 'Nom Entreprise']) }}
            {!! $errors->first('nom_entreprise', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('adresse') }}
            {{ Form::text('adresse', $compagny->adresse, ['class' => 'form-control' . ($errors->has('adresse') ? ' is-invalid' : ''), 'placeholder' => 'Adresse']) }}
            {!! $errors->first('adresse', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('panier') }}
            {{ Form::text('panier', $compagny->panier, ['class' => 'form-control' . ($errors->has('panier') ? ' is-invalid' : ''), 'placeholder' => 'Panier']) }}
            {!! $errors->first('panier', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('plafond') }}
            {{ Form::text('plafond', $compagny->plafond, ['class' => 'form-control' . ($errors->has('plafond') ? ' is-invalid' : ''), 'placeholder' => 'Plafond']) }}
            {!! $errors->first('plafond', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('user_id', 'User') }}
            {{ Form::select('user_id', $users, $compagny->user_id, ['class' => 'form-control' . ($errors->has('user_id') ? ' is-invalid' : ''), 'placeholder' => 'Select User']) }}
            {!! $errors->first('user_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

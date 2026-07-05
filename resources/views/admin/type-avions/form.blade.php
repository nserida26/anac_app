<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('code') }}
            {{ Form::text('code', $typeAvion->code, ['class' => 'form-control' . ($errors->has('code') ? ' is-invalid' : ''), 'placeholder' => 'Code']) }}
            {!! $errors->first('code', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('capacite') }}
            {{ Form::text('capacite', $typeAvion->capacite, ['class' => 'form-control' . ($errors->has('capacite') ? ' is-invalid' : ''), 'placeholder' => 'Capacite']) }}
            {!! $errors->first('capacite', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('charge_max') }}
            {{ Form::text('charge_max', $typeAvion->charge_max, ['class' => 'form-control' . ($errors->has('charge_max') ? ' is-invalid' : ''), 'placeholder' => 'Charge']) }}
            {!! $errors->first('charge_max', '<div class="invalid-feedback">:message</div>') !!}
        </div>
    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

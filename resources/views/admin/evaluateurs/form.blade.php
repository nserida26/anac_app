<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            {{ Form::label('np') }}
            {{ Form::text('np', $evaluateur->np, ['class' => 'form-control' . ($errors->has('np') ? ' is-invalid' : ''), 'placeholder' => 'Np']) }}
            {!! $errors->first('np', '<div class="invalid-feedback">:message</div>') !!}
        </div>
        <div class="form-group">
            {{ Form::label('user_id', 'User') }}
            {{ Form::select('user_id', $users, $evaluateur->user_id, ['class' => 'form-control' . ($errors->has('user_id') ? ' is-invalid' : ''), 'placeholder' => 'Select User']) }}
            {!! $errors->first('user_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>


    </div>
    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

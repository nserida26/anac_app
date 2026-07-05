<div class="box box-info padding-1">
    <div class="box-body">

        <div class="form-group">
            <label for="libelle">Libellé</label>
            <input type="text" class="form-control" id="libelle" name="libelle" required>
        </div>

        <div class="form-group">
            <label for="type_avion_id">Type d'avion</label>
            <select class="form-control" id="type_avion_id" name="type_avion_id" required>

                <option value=""></option>
                @foreach ($typeAvions as $type)
                    <option value="{{ $type->id }}">{{ $type->code }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="compagnie">Compagnie</label>
            <select class="form-control" id="compagnie" name="compagnie" required>
                @foreach ($compagnies as $compagnie)
                    <option value="{{ $compagnie }}">{{ $compagnie }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="date_delivrance_initiale">Date de délivrance initiale</label>
            <input type="date" class="form-control" id="date_delivrance_initiale" name="date_delivrance_initiale">
        </div>

        <div class="form-group">
            <label for="date_renouvellement">Date de renouvellement</label>
            <input type="date" class="form-control" id="date_renouvellement" name="date_renouvellement">
        </div>

        <div class="form-group">
            <label for="date_expiration">Date d'expiration</label>
            <input type="date" class="form-control" id="date_expiration" name="date_expiration">
        </div>
        <div class="form-group">
            {{ Form::label('centre_formation_id', 'Centres de Formation') }}
            {{ Form::select('centre_formation_id[]', $centres, $simulateur->centres->pluck('id')->toArray(), [
                'class' => 'form-control' . ($errors->has('centre_formation_id') ? ' is-invalid' : ''),
                'multiple' => 'multiple',
                'placeholder' => 'Select Centre',
            ]) }}
            {!! $errors->first('centre_formation_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>


    </div>

    <div class="box-footer mt20">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

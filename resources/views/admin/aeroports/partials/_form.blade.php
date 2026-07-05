<div class="row">
    <div class="col-md-12">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de l'aéroport *</label>
            <input type="text" name="nom" id="nom" 
                   class="form-control @error('nom') is-invalid @enderror" 
                   value="{{ old('nom', $aeroport->nom ?? '') }}" 
                   required maxlength="100">
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="mb-3">
            <label for="codeIATA" class="form-label">Code IATA *</label>
            <input type="text" name="codeIATA" id="codeIATA" 
                   class="form-control @error('codeIATA') is-invalid @enderror" 
                   value="{{ old('codeIATA', $aeroport->codeIATA ?? '') }}" 
                   required maxlength="3" pattern="[A-Z]{3}" 
                   title="3 lettres majuscules">
            @error('codeIATA')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">ex: CDG, ORY, JFK</small>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="mb-3">
            <label for="codeICAO" class="form-label">Code ICAO *</label>
            <input type="text" name="codeICAO" id="codeICAO" 
                   class="form-control @error('codeICAO') is-invalid @enderror" 
                   value="{{ old('codeICAO', $aeroport->codeICAO ?? '') }}" 
                   required maxlength="4" pattern="[A-Z]{4}" 
                   title="4 lettres majuscules">
            @error('codeICAO')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">ex: LFPG, LFPO, KJFK</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="pays_id" class="form-label">Pays *</label>
            <select name="pays_id" id="pays_id" 
                    class="form-control select2 @error('pays_id') is-invalid @enderror" required>
                <option value="">Sélectionner un pays</option>
                @foreach($pays as $pay)
                    <option value="{{ $pay->id }}" 
                        {{ old('pays_id', $aeroport->pays_id ?? '') == $pay->id ? 'selected' : '' }}>
                        {{ $pay->nom }}
                    </option>
                @endforeach
            </select>
            @error('pays_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="mb-3">
            <label for="ville" class="form-label">Ville *</label>
            <input type="text" name="ville" id="ville" 
                   class="form-control @error('ville') is-invalid @enderror" 
                   value="{{ old('ville', $aeroport->ville ?? '') }}" 
                   required maxlength="100">
            @error('ville')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="latitude" class="form-label">Latitude</label>
            <div class="input-group">
                <input type="number" step="any" name="latitude" id="latitude" 
                       class="form-control @error('latitude') is-invalid @enderror" 
                       value="{{ old('latitude', $aeroport->latitude ?? '') }}" 
                       min="-90" max="90" placeholder="ex: 48.853410">
                <span class="input-group-text">°</span>
            </div>
            @error('latitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Entre -90 et 90</small>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="mb-3">
            <label for="longitude" class="form-label">Longitude</label>
            <div class="input-group">
                <input type="number" step="any" name="longitude" id="longitude" 
                       class="form-control @error('longitude') is-invalid @enderror" 
                       value="{{ old('longitude', $aeroport->longitude ?? '') }}" 
                       min="-180" max="180" placeholder="ex: 2.348800">
                <span class="input-group-text">°</span>
            </div>
            @error('longitude')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Entre -180 et 180</small>
        </div>
    </div>
</div>

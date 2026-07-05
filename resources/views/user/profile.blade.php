@extends('user.layouts.app')
@section('title')
    @lang('trans.dashboard')
@endsection
@section('contentheader')
    @lang('trans.dashboard')
@endsection
@section('contentheaderlink')
    <a href="{{ route('user') }}">
        @lang('trans.dashboard') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <!-- Bloc "profile" -->
        @if (isset($demandeur))
            <div class="col-lg-12 order-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">User Profile Information</h6>
                        <button class="btn btn-primary btn-sm" id="editProfileBtn">Modifier le profil</button>
                    </div>
                    @if (Auth::user()->user_type === 'licence')
                        <div class="card-profile-image mt-4">
                            <div class="text-center">
                                <img src="{{ asset('/uploads/' . $demandeur->photo) }}"
                                    class="rounded-circle avatar avatar font-weight-bold" alt="User Image" />
                            </div>
                        </div>
                    @endif

                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="mt-4">
                                    <h6 class="heading-small text-muted mb-4">User Profile Information</h6>
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>                                                
                                                @if (Auth::user()->user_type === 'licence') 
                                                    @lang('trans.np')
                                                    @else
                                                        @lang('trans.n_societe')
                                                    
                                                @endif</th>
                                            <td>{{ $demandeur->np ?? '-' }}</td>
                                        </tr>
                                        @if (Auth::user()->user_type === 'licence')
                                            <tr>
                                                <th>@lang('trans.date_naissance')</th>
                                                <td>{{ $demandeur->date_naissance ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>@lang('trans.lieu_naissance')</th>
                                                <td>{{ $demandeur->lieu_naissance ?? '-' }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>@lang('trans.nationality')</th>
                                            <td>{{ strtoupper($demandeur->nationalite) ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.adresse')</th>
                                            <td>{{ $demandeur->adresse ?? '-' }}</td>
                                        </tr>
                                        @if (Auth::user()->user_type === 'licence')
                                        <tr>
                                            <th>@lang('trans.compagny')</th>
                                            <td>{{ $demandeur->compagnie->nom_entreprise ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.adresse_employeur')</th>
                                            <td>{{ $demandeur->compagnie->adresse ?? '-' }}</td>
                                        </tr>
                                        
                                            <tr>
                                                <th>@lang('trans.photo')</th>
                                                <td>
                                                    @if (isset($demandeur->photo))
                                                        <img src="{{ asset('/uploads/' . $demandeur->photo) }}"
                                                            alt="User Photo" width="100">
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                            </tr>
                                        @endif
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Formulaire de modification (masqué par défaut) -->
            <div class="col-lg-12 order-lg-1" id="editProfileForm" style="display: none;">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Modifier le profil</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.profile.update') }}" autocomplete="off"
                            enctype="multipart/form-data" id="profileUpdateForm">
                            @csrf
                            @method('PUT')

                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="edit_np">
                                                @if (Auth::user()->user_type === 'licence') 
                                                    @lang('trans.np')
                                                    @else
                                                        @lang('trans.n_societe')
                                                    
                                                @endif
                                                <span
                                                    class="small text-danger">*</span></label>
                                            <input type="text" id="edit_np" class="form-control" name="np"
                                                value="{{ $demandeur->np ?? '' }}">
                                        </div>
                                    </div>
                                    @if (Auth::user()->user_type === 'licence')
                                        <div class="col-lg-4">
                                            <div class="form-group focused">
                                                <label class="form-control-label"
                                                    for="edit_photo">@lang('trans.photo')</label>
                                                <input type="file" class="form-control" id="edit_photo" name="photo"
                                                    accept="image/*">
                                                <p id="edit_error-message" style="color:red;"></p>
                                                <img id="edit_preview" src="{{ asset('/uploads/' . $demandeur->photo) }}"
                                                    style="max-width:200px;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group focused">
                                                <label class="form-control-label"
                                                    for="edit_date_naissance">@lang('trans.date_naissance')</label>
                                                <input type="date" id="edit_date_naissance" class="form-control"
                                                    name="date_naissance" value="{{ $demandeur->date_naissance ?? '' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @if (Auth::user()->user_type === 'licence')
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                    for="edit_lieu_naissance">@lang('trans.lieu_naissance')<span
                                                        class="small text-danger">*</span></label>
                                                <input type="text" id="edit_lieu_naissance" class="form-control"
                                                    name="lieu_naissance" value="{{ $demandeur->lieu_naissance ?? '' }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="edit_adresse">@lang('trans.adresse')</label>
                                            <input type="text" id="edit_adresse" class="form-control" name="adresse"
                                                value="{{ $demandeur->adresse ?? '' }}">
                                        </div>
                                    </div>
                                    @if (Auth::user()->user_type === 'licence')
                                        <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label"
                                                for="edit_compagnie_id">@lang('trans.compagny')</label>
                                            <select class="form-control select2" id="edit_compagnie_id" name="compagnie_id">
                                                <option value=""></option>
                                                @foreach ($compagnies as $compagnie)
                                                    <option value="{{ $compagnie->id }}"
                                                        {{ $demandeur->compagnie_id == $compagnie->id ? 'selected' : '' }}>
                                                        {{ $compagnie->nom_entreprise }} {{ $compagnie->adresse }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label"
                                                for="edit_nationalite">@lang('trans.nationality')</label>
                                            <select name="nationalite" class="form-control select2"
                                                id="edit_nationalite">
                                                <option value="mauritanian">Mauritanian</option>
                                                <option value="afghan">Afghan</option>
                                                <option value="albanian">Albanian</option>
                                                <option value="algerian">Algerian</option>
                                                <option value="american">American</option>
                                                <option value="andorran">Andorran</option>
                                                <option value="angolan">Angolan</option>
                                                <option value="antiguans">Antiguans</option>
                                                <option value="argentinean">Argentinean</option>
                                                <option value="armenian">Armenian</option>
                                                <option value="australian">Australian</option>
                                                <option value="austrian">Austrian</option>
                                                <option value="azerbaijani">Azerbaijani</option>
                                                <option value="bahamian">Bahamian</option>
                                                <option value="bahraini">Bahraini</option>
                                                <option value="bangladeshi">Bangladeshi</option>
                                                <option value="barbadian">Barbadian</option>
                                                <option value="barbudans">Barbudans</option>
                                                <option value="batswana">Batswana</option>
                                                <option value="belarusian">Belarusian</option>
                                                <option value="belgian">Belgian</option>
                                                <option value="belizean">Belizean</option>
                                                <option value="beninese">Beninese</option>
                                                <option value="bhutanese">Bhutanese</option>
                                                <option value="bolivian">Bolivian</option>
                                                <option value="bosnian">Bosnian</option>
                                                <option value="brazilian">Brazilian</option>
                                                <option value="british">British</option>
                                                <option value="bruneian">Bruneian</option>
                                                <option value="bulgarian">Bulgarian</option>
                                                <option value="burkinabe">Burkinabe</option>
                                                <option value="burmese">Burmese</option>
                                                <option value="burundian">Burundian</option>
                                                <option value="cambodian">Cambodian</option>
                                                <option value="cameroonian">Cameroonian</option>
                                                <option value="canadian">Canadian</option>
                                                <option value="cape verdean">Cape Verdean</option>
                                                <option value="central african">Central African</option>
                                                <option value="chadian">Chadian</option>
                                                <option value="chilean">Chilean</option>
                                                <option value="chinese">Chinese</option>
                                                <option value="colombian">Colombian</option>
                                                <option value="comoran">Comoran</option>
                                                <option value="congolese">Congolese</option>
                                                <option value="costa rican">Costa Rican</option>
                                                <option value="croatian">Croatian</option>
                                                <option value="cuban">Cuban</option>
                                                <option value="cypriot">Cypriot</option>
                                                <option value="czech">Czech</option>
                                                <option value="danish">Danish</option>
                                                <option value="djibouti">Djibouti</option>
                                                <option value="dominican">Dominican</option>
                                                <option value="dutch">Dutch</option>
                                                <option value="east timorese">East Timorese</option>
                                                <option value="ecuadorean">Ecuadorean</option>
                                                <option value="egyptian">Egyptian</option>
                                                <option value="emirian">Emirian</option>
                                                <option value="equatorial guinean">Equatorial Guinean</option>
                                                <option value="eritrean">Eritrean</option>
                                                <option value="estonian">Estonian</option>
                                                <option value="ethiopian">Ethiopian</option>
                                                <option value="fijian">Fijian</option>
                                                <option value="filipino">Filipino</option>
                                                <option value="finnish">Finnish</option>
                                                <option value="french">French</option>
                                                <option value="gabonese">Gabonese</option>
                                                <option value="gambian">Gambian</option>
                                                <option value="georgian">Georgian</option>
                                                <option value="german">German</option>
                                                <option value="ghanaian">Ghanaian</option>
                                                <option value="greek">Greek</option>
                                                <option value="grenadian">Grenadian</option>
                                                <option value="guatemalan">Guatemalan</option>
                                                <option value="guinea-bissauan">Guinea-Bissauan</option>
                                                <option value="guinean">Guinean</option>
                                                <option value="guyanese">Guyanese</option>
                                                <option value="haitian">Haitian</option>
                                                <option value="herzegovinian">Herzegovinian</option>
                                                <option value="honduran">Honduran</option>
                                                <option value="hungarian">Hungarian</option>
                                                <option value="icelander">Icelander</option>
                                                <option value="indian">Indian</option>
                                                <option value="indonesian">Indonesian</option>
                                                <option value="iranian">Iranian</option>
                                                <option value="iraqi">Iraqi</option>
                                                <option value="irish">Irish</option>
                                                <option value="israeli">Israeli</option>
                                                <option value="italian">Italian</option>
                                                <option value="ivorian">Ivorian</option>
                                                <option value="jamaican">Jamaican</option>
                                                <option value="japanese">Japanese</option>
                                                <option value="jordanian">Jordanian</option>
                                                <option value="kazakhstani">Kazakhstani</option>
                                                <option value="kenyan">Kenyan</option>
                                                <option value="kittian and nevisian">Kittian and Nevisian</option>
                                                <option value="kuwaiti">Kuwaiti</option>
                                                <option value="kyrgyz">Kyrgyz</option>
                                                <option value="laotian">Laotian</option>
                                                <option value="latvian">Latvian</option>
                                                <option value="lebanese">Lebanese</option>
                                                <option value="liberian">Liberian</option>
                                                <option value="libyan">Libyan</option>
                                                <option value="liechtensteiner">Liechtensteiner</option>
                                                <option value="lithuanian">Lithuanian</option>
                                                <option value="luxembourger">Luxembourger</option>
                                                <option value="macedonian">Macedonian</option>
                                                <option value="malagasy">Malagasy</option>
                                                <option value="malawian">Malawian</option>
                                                <option value="malaysian">Malaysian</option>
                                                <option value="maldivan">Maldivan</option>
                                                <option value="malian">Malian</option>
                                                <option value="maltese">Maltese</option>
                                                <option value="marshallese">Marshallese</option>

                                                <option value="mauritian">Mauritian</option>
                                                <option value="mexican">Mexican</option>
                                                <option value="micronesian">Micronesian</option>
                                                <option value="moldovan">Moldovan</option>
                                                <option value="monacan">Monacan</option>
                                                <option value="mongolian">Mongolian</option>
                                                <option value="moroccan">Moroccan</option>
                                                <option value="mosotho">Mosotho</option>
                                                <option value="motswana">Motswana</option>
                                                <option value="mozambican">Mozambican</option>
                                                <option value="namibian">Namibian</option>
                                                <option value="nauruan">Nauruan</option>
                                                <option value="nepalese">Nepalese</option>
                                                <option value="new zealander">New Zealander</option>
                                                <option value="ni-vanuatu">Ni-Vanuatu</option>
                                                <option value="nicaraguan">Nicaraguan</option>
                                                <option value="nigerien">Nigerien</option>
                                                <option value="north korean">North Korean</option>
                                                <option value="northern irish">Northern Irish</option>
                                                <option value="norwegian">Norwegian</option>
                                                <option value="omani">Omani</option>
                                                <option value="pakistani">Pakistani</option>
                                                <option value="palauan">Palauan</option>
                                                <option value="panamanian">Panamanian</option>
                                                <option value="papua new guinean">Papua New Guinean</option>
                                                <option value="paraguayan">Paraguayan</option>
                                                <option value="peruvian">Peruvian</option>
                                                <option value="polish">Polish</option>
                                                <option value="portuguese">Portuguese</option>
                                                <option value="qatari">Qatari</option>
                                                <option value="romanian">Romanian</option>
                                                <option value="russian">Russian</option>
                                                <option value="rwandan">Rwandan</option>
                                                <option value="saint lucian">Saint Lucian</option>
                                                <option value="salvadoran">Salvadoran</option>
                                                <option value="samoan">Samoan</option>
                                                <option value="san marinese">San Marinese</option>
                                                <option value="sao tomean">Sao Tomean</option>
                                                <option value="saudi">Saudi</option>
                                                <option value="scottish">Scottish</option>
                                                <option value="senegalese">Senegalese</option>
                                                <option value="serbian">Serbian</option>
                                                <option value="seychellois">Seychellois</option>
                                                <option value="sierra leonean">Sierra Leonean</option>
                                                <option value="singaporean">Singaporean</option>
                                                <option value="slovakian">Slovakian</option>
                                                <option value="slovenian">Slovenian</option>
                                                <option value="solomon islander">Solomon Islander</option>
                                                <option value="somali">Somali</option>
                                                <option value="south african">South African</option>
                                                <option value="south korean">South Korean</option>
                                                <option value="spanish">Spanish</option>
                                                <option value="sri lankan">Sri Lankan</option>
                                                <option value="sudanese">Sudanese</option>
                                                <option value="surinamer">Surinamer</option>
                                                <option value="swazi">Swazi</option>
                                                <option value="swedish">Swedish</option>
                                                <option value="swiss">Swiss</option>
                                                <option value="syrian">Syrian</option>
                                                <option value="taiwanese">Taiwanese</option>
                                                <option value="tajik">Tajik</option>
                                                <option value="tanzanian">Tanzanian</option>
                                                <option value="thai">Thai</option>
                                                <option value="togolese">Togolese</option>
                                                <option value="tongan">Tongan</option>
                                                <option value="trinidadian or tobagonian">Trinidadian or Tobagonian
                                                </option>
                                                <option value="tunisian">Tunisian</option>
                                                <option value="turkish">Turkish</option>
                                                <option value="tuvaluan">Tuvaluan</option>
                                                <option value="ugandan">Ugandan</option>
                                                <option value="ukrainian">Ukrainian</option>
                                                <option value="uruguayan">Uruguayan</option>
                                                <option value="uzbekistani">Uzbekistani</option>
                                                <option value="venezuelan">Venezuelan</option>
                                                <option value="vietnamese">Vietnamese</option>
                                                <option value="welsh">Welsh</option>
                                                <option value="yemenite">Yemenite</option>
                                                <option value="zambian">Zambian</option>
                                                <option value="zimbabwean">Zimbabwean</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col text-center">
                                            <button type="submit" class="btn btn-primary"
                                                id="updateProfileBtn">Enregistrer les
                                                modifications</button>
                                            <button type="button" class="btn btn-secondary"
                                                id="cancelEditBtn">Annuler</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <!-- Bloc pour le formulaire "My Account" -->
        @if (!isset($demandeur))
            <div class="col-lg-12 order-lg-1">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.profile.store') }}" autocomplete="off"
                            enctype="multipart/form-data" id="imageUploadForm">
                            @csrf

                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="name">                                                
                                                @if (Auth::user()->user_type === 'licence') 
                                                    @lang('trans.np')
                                                    @else
                                                        @lang('trans.n_societe')
                                                    
                                                @endif<span
                                                    class="small text-danger">*</span></label>
                                            <input type="text" id="np" class="form-control" name="np">
                                        </div>
                                    </div>
                                    @if (Auth::user()->user_type === 'licence')
                                        <div class="col-lg-4">
                                            <div class="form-group focused">
                                                <label class="form-control-label"
                                                    for="photoUpload">@lang('trans.photo')</label>
                                                <input type="file" class="form-control" id="photo" name="photo"
                                                    accept="image/*">
                                                <p id="error-message" style="color:red;"></p>
                                                <img id="preview" style="max-width:200px; display:none;">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group focused">
                                                <label class="form-control-label"
                                                    for="date_naissance">@lang('trans.date_naissance')</label>
                                                <input type="date" id="date_naissance" class="form-control"
                                                    name="date_naissance">
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="row">
                                    @if (Auth::user()->user_type === 'licence')
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                    for="lieu_naissance">@lang('trans.lieu_naissance')<span
                                                        class="small text-danger">*</span></label>
                                                <input type="text" id="lieu_naissance" class="form-control"
                                                    name="lieu_naissance">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="adresse">@lang('trans.adresse')</label>
                                            <input type="text" id="adresse" class="form-control" name="adresse">
                                        </div>
                                    </div>
                                    @if (Auth::user()->user_type === 'licence')
                                    <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label"
                                                for="compagnie_id">@lang('trans.compagny')</label>
                                            <select class="form-control select2" id="compagnie_id" name="compagnie_id">
                                                <option value="">

                                                </option>
                                                @foreach ($compagnies as $compagnie)
                                                    <option value="{{ $compagnie->id }}">
                                                        {{ $compagnie->nom_entreprise }} {{ $compagnie->adresse }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-lg-3">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="nationalite">@lang('trans.nationality')</label>
                                            <select name="nationalite" class="form-control select2">
                                                <option value="mauritanian">Mauritanian</option>
                                                <option value="afghan">Afghan</option>
                                                <option value="albanian">Albanian</option>
                                                <option value="algerian">Algerian</option>
                                                <option value="american">American</option>
                                                <option value="andorran">Andorran</option>
                                                <option value="angolan">Angolan</option>
                                                <option value="antiguans">Antiguans</option>
                                                <option value="argentinean">Argentinean</option>
                                                <option value="armenian">Armenian</option>
                                                <option value="australian">Australian</option>
                                                <option value="austrian">Austrian</option>
                                                <option value="azerbaijani">Azerbaijani</option>
                                                <option value="bahamian">Bahamian</option>
                                                <option value="bahraini">Bahraini</option>
                                                <option value="bangladeshi">Bangladeshi</option>
                                                <option value="barbadian">Barbadian</option>
                                                <option value="barbudans">Barbudans</option>
                                                <option value="batswana">Batswana</option>
                                                <option value="belarusian">Belarusian</option>
                                                <option value="belgian">Belgian</option>
                                                <option value="belizean">Belizean</option>
                                                <option value="beninese">Beninese</option>
                                                <option value="bhutanese">Bhutanese</option>
                                                <option value="bolivian">Bolivian</option>
                                                <option value="bosnian">Bosnian</option>
                                                <option value="brazilian">Brazilian</option>
                                                <option value="british">British</option>
                                                <option value="bruneian">Bruneian</option>
                                                <option value="bulgarian">Bulgarian</option>
                                                <option value="burkinabe">Burkinabe</option>
                                                <option value="burmese">Burmese</option>
                                                <option value="burundian">Burundian</option>
                                                <option value="cambodian">Cambodian</option>
                                                <option value="cameroonian">Cameroonian</option>
                                                <option value="canadian">Canadian</option>
                                                <option value="cape verdean">Cape Verdean</option>
                                                <option value="central african">Central African</option>
                                                <option value="chadian">Chadian</option>
                                                <option value="chilean">Chilean</option>
                                                <option value="chinese">Chinese</option>
                                                <option value="colombian">Colombian</option>
                                                <option value="comoran">Comoran</option>
                                                <option value="congolese">Congolese</option>
                                                <option value="costa rican">Costa Rican</option>
                                                <option value="croatian">Croatian</option>
                                                <option value="cuban">Cuban</option>
                                                <option value="cypriot">Cypriot</option>
                                                <option value="czech">Czech</option>
                                                <option value="danish">Danish</option>
                                                <option value="djibouti">Djibouti</option>
                                                <option value="dominican">Dominican</option>
                                                <option value="dutch">Dutch</option>
                                                <option value="east timorese">East Timorese</option>
                                                <option value="ecuadorean">Ecuadorean</option>
                                                <option value="egyptian">Egyptian</option>
                                                <option value="emirian">Emirian</option>
                                                <option value="equatorial guinean">Equatorial Guinean</option>
                                                <option value="eritrean">Eritrean</option>
                                                <option value="estonian">Estonian</option>
                                                <option value="ethiopian">Ethiopian</option>
                                                <option value="fijian">Fijian</option>
                                                <option value="filipino">Filipino</option>
                                                <option value="finnish">Finnish</option>
                                                <option value="french">French</option>
                                                <option value="gabonese">Gabonese</option>
                                                <option value="gambian">Gambian</option>
                                                <option value="georgian">Georgian</option>
                                                <option value="german">German</option>
                                                <option value="ghanaian">Ghanaian</option>
                                                <option value="greek">Greek</option>
                                                <option value="grenadian">Grenadian</option>
                                                <option value="guatemalan">Guatemalan</option>
                                                <option value="guinea-bissauan">Guinea-Bissauan</option>
                                                <option value="guinean">Guinean</option>
                                                <option value="guyanese">Guyanese</option>
                                                <option value="haitian">Haitian</option>
                                                <option value="herzegovinian">Herzegovinian</option>
                                                <option value="honduran">Honduran</option>
                                                <option value="hungarian">Hungarian</option>
                                                <option value="icelander">Icelander</option>
                                                <option value="indian">Indian</option>
                                                <option value="indonesian">Indonesian</option>
                                                <option value="iranian">Iranian</option>
                                                <option value="iraqi">Iraqi</option>
                                                <option value="irish">Irish</option>
                                                <option value="israeli">Israeli</option>
                                                <option value="italian">Italian</option>
                                                <option value="ivorian">Ivorian</option>
                                                <option value="jamaican">Jamaican</option>
                                                <option value="japanese">Japanese</option>
                                                <option value="jordanian">Jordanian</option>
                                                <option value="kazakhstani">Kazakhstani</option>
                                                <option value="kenyan">Kenyan</option>
                                                <option value="kittian and nevisian">Kittian and Nevisian</option>
                                                <option value="kuwaiti">Kuwaiti</option>
                                                <option value="kyrgyz">Kyrgyz</option>
                                                <option value="laotian">Laotian</option>
                                                <option value="latvian">Latvian</option>
                                                <option value="lebanese">Lebanese</option>
                                                <option value="liberian">Liberian</option>
                                                <option value="libyan">Libyan</option>
                                                <option value="liechtensteiner">Liechtensteiner</option>
                                                <option value="lithuanian">Lithuanian</option>
                                                <option value="luxembourger">Luxembourger</option>
                                                <option value="macedonian">Macedonian</option>
                                                <option value="malagasy">Malagasy</option>
                                                <option value="malawian">Malawian</option>
                                                <option value="malaysian">Malaysian</option>
                                                <option value="maldivan">Maldivan</option>
                                                <option value="malian">Malian</option>
                                                <option value="maltese">Maltese</option>
                                                <option value="marshallese">Marshallese</option>

                                                <option value="mauritian">Mauritian</option>
                                                <option value="mexican">Mexican</option>
                                                <option value="micronesian">Micronesian</option>
                                                <option value="moldovan">Moldovan</option>
                                                <option value="monacan">Monacan</option>
                                                <option value="mongolian">Mongolian</option>
                                                <option value="moroccan">Moroccan</option>
                                                <option value="mosotho">Mosotho</option>
                                                <option value="motswana">Motswana</option>
                                                <option value="mozambican">Mozambican</option>
                                                <option value="namibian">Namibian</option>
                                                <option value="nauruan">Nauruan</option>
                                                <option value="nepalese">Nepalese</option>
                                                <option value="new zealander">New Zealander</option>
                                                <option value="ni-vanuatu">Ni-Vanuatu</option>
                                                <option value="nicaraguan">Nicaraguan</option>
                                                <option value="nigerien">Nigerien</option>
                                                <option value="north korean">North Korean</option>
                                                <option value="northern irish">Northern Irish</option>
                                                <option value="norwegian">Norwegian</option>
                                                <option value="omani">Omani</option>
                                                <option value="pakistani">Pakistani</option>
                                                <option value="palauan">Palauan</option>
                                                <option value="panamanian">Panamanian</option>
                                                <option value="papua new guinean">Papua New Guinean</option>
                                                <option value="paraguayan">Paraguayan</option>
                                                <option value="peruvian">Peruvian</option>
                                                <option value="polish">Polish</option>
                                                <option value="portuguese">Portuguese</option>
                                                <option value="qatari">Qatari</option>
                                                <option value="romanian">Romanian</option>
                                                <option value="russian">Russian</option>
                                                <option value="rwandan">Rwandan</option>
                                                <option value="saint lucian">Saint Lucian</option>
                                                <option value="salvadoran">Salvadoran</option>
                                                <option value="samoan">Samoan</option>
                                                <option value="san marinese">San Marinese</option>
                                                <option value="sao tomean">Sao Tomean</option>
                                                <option value="saudi">Saudi</option>
                                                <option value="scottish">Scottish</option>
                                                <option value="senegalese">Senegalese</option>
                                                <option value="serbian">Serbian</option>
                                                <option value="seychellois">Seychellois</option>
                                                <option value="sierra leonean">Sierra Leonean</option>
                                                <option value="singaporean">Singaporean</option>
                                                <option value="slovakian">Slovakian</option>
                                                <option value="slovenian">Slovenian</option>
                                                <option value="solomon islander">Solomon Islander</option>
                                                <option value="somali">Somali</option>
                                                <option value="south african">South African</option>
                                                <option value="south korean">South Korean</option>
                                                <option value="spanish">Spanish</option>
                                                <option value="sri lankan">Sri Lankan</option>
                                                <option value="sudanese">Sudanese</option>
                                                <option value="surinamer">Surinamer</option>
                                                <option value="swazi">Swazi</option>
                                                <option value="swedish">Swedish</option>
                                                <option value="swiss">Swiss</option>
                                                <option value="syrian">Syrian</option>
                                                <option value="taiwanese">Taiwanese</option>
                                                <option value="tajik">Tajik</option>
                                                <option value="tanzanian">Tanzanian</option>
                                                <option value="thai">Thai</option>
                                                <option value="togolese">Togolese</option>
                                                <option value="tongan">Tongan</option>
                                                <option value="trinidadian or tobagonian">Trinidadian or Tobagonian
                                                </option>
                                                <option value="tunisian">Tunisian</option>
                                                <option value="turkish">Turkish</option>
                                                <option value="tuvaluan">Tuvaluan</option>
                                                <option value="ugandan">Ugandan</option>
                                                <option value="ukrainian">Ukrainian</option>
                                                <option value="uruguayan">Uruguayan</option>
                                                <option value="uzbekistani">Uzbekistani</option>
                                                <option value="venezuelan">Venezuelan</option>
                                                <option value="vietnamese">Vietnamese</option>
                                                <option value="welsh">Welsh</option>
                                                <option value="yemenite">Yemenite</option>
                                                <option value="zambian">Zambian</option>
                                                <option value="zimbabwean">Zimbabwean</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col text-center">
                                            <button type="submit" class="btn btn-primary" id="uploadBtn">Save</button>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection


@push('script')
    <script src="{{ asset('assets/admin/plugins/jquery-validation/dist/jquery.validate.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

        });
        $(document).ready(function() {
            // Gestion de l'affichage du formulaire de modification
            $("#editProfileBtn").click(function() {
                $("#editProfileForm").show();
                $(this).hide();
            });

            $("#cancelEditBtn").click(function() {
                $("#editProfileForm").hide();
                $("#editProfileBtn").show();
            });

            // Validation pour le formulaire de modification
            $("#profileUpdateForm").validate({
                rules: {
                    photo: {
                        extension: "jpg|jpeg|png",
                        fileSize: 5 * 1024 * 1024, // 5MB limit
                        imageDimensions: [413, 531] // Width x Height
                    },
                    np: "required",
                    lieu_naissance: "required"
                },
                messages: {
                    photo: {
                        extension: "Seuls les fichiers JPG, JPEG et PNG sont autorisés.",
                        fileSize: "La taille du fichier ne doit pas dépasser 5MB.",
                        imageDimensions: "L'image doit être exactement de 413x531 pixels."
                    },
                    np: "Veuillez entrer votre nom et prénom",
                    lieu_naissance: "Veuillez entrer votre lieu de naissance"
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Aperçu de l'image pour le formulaire de modification
            $("#edit_photo").on("change", function(event) {
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $("#edit_preview").attr("src", e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Validation pour le formulaire initial (si vous en avez besoin)
            $("#imageUploadForm").validate({
                rules: {
                    photo: {
                        required: true,
                        extension: "jpg|jpeg|png",
                        fileSize: 5 * 1024 * 1024,
                        imageDimensions: [413, 531]
                    },
                    np: "required",
                    lieu_naissance: "required"
                },
                messages: {
                    photo: {
                        required: "Veuillez sélectionner une image.",
                        extension: "Seuls les fichiers JPG, JPEG et PNG sont autorisés.",
                        fileSize: "La taille du fichier ne doit pas dépasser 5MB.",
                        imageDimensions: "L'image doit être exactement de 413x531 pixels."
                    },
                    np: "Veuillez entrer votre nom et prénom",
                    lieu_naissance: "Veuillez entrer votre lieu de naissance"
                }
            });
        });
    </script>
@endpush

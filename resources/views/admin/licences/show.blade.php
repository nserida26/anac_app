@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('licences') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #dee2e6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .signature-img {
            max-width: 120px;
            max-height: 60px;
        }

        .profile-picture-wrapper {
            position: relative;
            display: inline-block;
        }

        .edit-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            transition: all 0.3s;
        }

        .edit-photo-btn:hover {
            background: #218838;
            transform: scale(1.1);
        }

        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 10px;
            border: 2px solid #dee2e6;
        }

        .calculation-option {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .calculation-option:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .calculation-option.selected {
            border-color: #28a745;
            background-color: #d4edda;
        }

        .calculation-option input[type="radio"] {
            display: none;
        }

        .calculation-option .option-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .calculation-option.selected .option-icon {
            color: #28a745;
        }

        .jours-input-container {
            margin-top: 15px;
            display: none;
        }

        .calculation-option.selected .jours-input-container {
            display: block;
        }

        .example-calculation {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .status-badge {
            font-size: 14px;
            padding: 8px 15px;
            border-radius: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>@lang('trans.license_details')</span>
                        <div>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#editLicenseModal">
                                <i class="fas fa-edit"></i> @lang('trans.edit_license_number')
                            </button>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                data-target="#editPhotoModal">
                                <i class="fas fa-camera"></i> @lang('trans.edit_photo')
                            </button>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#editCalculationModal">
                                <i class="fas fa-calculator"></i> @lang('trans.calculation_settings')
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @isset($licence)
                            <div class="row">
                                <!-- Profile Picture Column -->
                                <div class="col-md-3 text-center mb-4">
                                    <div class="profile-picture-wrapper">
                                        <img src="{{ asset('/uploads/' . ($licence->photo ?? 'default.png')) }}"
                                            alt="Profile Picture" class="profile-picture rounded-circle mb-3" id="profileImage">
                                        <div class="edit-photo-btn" data-toggle="modal" data-target="#editPhotoModal">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                    </div>
                                    <h5 class="font-weight-bold">{{ $licence->np ?? '-' }}</h5>
                                    <p class="text-muted">@lang('trans.license') #{{ $licence->numero_licence ?? '-' }}</p>
                                    
                                    <!-- Statut du calcul -->
                                    <div class="mt-3">
                                        <label class="font-weight-bold">@lang('trans.calculation_type'):</label>
                                        <div class="mt-2">
                                            @if($licence->type_calcul == 'jours')
                                                <span class="badge badge-info status-badge">
                                                    <i class="fas fa-plus-circle"></i> 
                                                    +{{ $licence->jours_supplementaires }} @lang('trans.days')
                                                </span>
                                            @elseif($licence->type_calcul == 'fin_mois')
                                                <span class="badge badge-success status-badge">
                                                    <i class="fas fa-calendar-check"></i> 
                                                    @lang('trans.end_of_month')
                                                </span>
                                            @else
                                                <span class="badge badge-secondary status-badge">
                                                    <i class="fas fa-ban"></i> 
                                                    @lang('trans.no_calculation')
                                                </span>
                                            @endif
                                        </div>
                                        
                                        
                                    </div>
                                </div>

                                <!-- Details Column -->
                                <div class="col-md-9">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">@lang('trans.category')</th>
                                                    <td>{{ $licence->categorie_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.machine')</th>
                                                    <td>{{ $licence->machine_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.type')</th>
                                                    <td>{{ $licence->type_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.license_number')</th>
                                                    <td class="font-weight-bold">{{ $licence->numero_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.fl_name')</th>
                                                    <td>{{ $licence->np ?? '-' }}</td>
                                                </tr>
                                                
                                                <!-- Affichage du certificat médical avec calcul -->
                                                @if (!empty($medical_certificat))
                                                    <tr>
                                                        <th>@lang('trans.medical_certificate')</th>
                                                        <td>
                                                            @php
                                                                $medicalStartDate = Carbon::parse($medical_certificat->date_examen);
                                                                $medicalExpiryDate = $medicalStartDate->copy()
                                                                    ->addMonths($medical_certificat->validite);
                                                                
                                                                // Application du type de calcul choisi
                                                                if ($licence->type_calcul == 'jours') {
                                                                    $medicalExpiryDate->addDays($licence->jours_supplementaires);
                                                                } elseif ($licence->type_calcul == 'fin_mois') {
                                                                    $medicalExpiryDate->endOfMonth();
                                                                }
                                                                
                                                                $medicalExpiryDateFormatted = $medicalExpiryDate->format('d-M-Y');
                                                                
                                                                $class = '';
                                                                $class1 = [27, 28, 29, 30];
                                                                $class2 = [31, 32, 39];
                                                                $class3 = [35, 36, 37, 38];

                                                                if (in_array($licence->demande->typeLicence->id, $class1)) {
                                                                    $class = 'Class 1';
                                                                } elseif (in_array($licence->demande->typeLicence->id, $class2)) {
                                                                    $class = 'Class 2';
                                                                } elseif (in_array($licence->demande->typeLicence->id, $class3)) {
                                                                    $class = 'Class 3';
                                                                }
                                                            @endphp
                                                            <span class="font-weight-bold">{{ $class }}</span> 
                                                            [<span class="text-primary">{{ $medicalExpiryDateFormatted }}</span>]
                                                            
                                                            @if($licence->type_calcul != 'none')
                                                                <br>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    @if($licence->type_calcul == 'jours')
                                                                        @lang('trans.includes') +{{ $licence->jours_supplementaires }} @lang('trans.days')
                                                                    @else
                                                                        @lang('trans.includes_end_of_month')
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                
                                                <tr>
                                                    <th>@lang('trans.dob')</th>
                                                    <td>{{ !empty($licence->date_naissance) ? date('d/m/Y', strtotime($licence->date_naissance)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.address')</th>
                                                    <td>{{ $licence->adresse ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.signature')</th>
                                                    <td class="text-center">
                                                        @if (isset($licence->signature) && $licence->signature != '')
                                                            <img src="{{ asset('/uploads/' . $licence->signature) }}"
                                                                alt="User Signature" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.deliverance_date')</th>
                                                    <td>{{ !empty($licence->date_deliverance) ? date('d/m/Y', strtotime($licence->date_deliverance)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.update_date')</th>
                                                    <td>{{ !empty($licence->date_mise_a_jour) ? date('d/m/Y', strtotime($licence->date_mise_a_jour)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.expiration_date')</th>
                                                    <td
                                                        class="{{ \Carbon\Carbon::parse($licence->date_expiration)->isPast() ? 'text-danger' : 'text-success' }}">
                                                        {{ !empty($licence->date_expiration) ? date('d/m/Y', strtotime($licence->date_expiration)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.stamp')</th>
                                                    <td class="text-center">
                                                        @if (isset($licence->cachet) && $licence->cachet != '')
                                                            <img src="{{ asset('/uploads/' . $licence->cachet) }}"
                                                                alt="Stamp" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.signature_dg')</th>
                                                    <td class="text-center">
                                                        @if (isset($licence->signature_dg) && $licence->signature_dg != '')
                                                            <img src="{{ asset('/uploads/' . $licence->signature_dg) }}"
                                                                alt="DG Signature" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.signature_dsv')</th>
                                                    <td class="text-center">
                                                        @if (isset($licence->signature_dsv) && $licence->signature_dsv != '')
                                                            <img src="{{ asset('/uploads/' . $licence->signature_dsv) }}"
                                                                alt="DSV Signature" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                @lang('trans.no_license_found')
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Paramètres de Calcul -->
    <div class="modal fade" id="editCalculationModal" tabindex="-1" role="dialog" 
        aria-labelledby="editCalculationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editCalculationModalLabel">
                        <i class="fas fa-calculator mr-2"></i>@lang('trans.calculation_settings')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="calculationForm" action="{{ route('licences.update.calculation', $licence->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            @lang('trans.calculation_choice_info')
                        </div>
                        
                        <div class="row">
                            <!-- Option 1: Aucun calcul -->
                            <div class="col-md-4">
                                <div class="calculation-option {{ $licence->type_calcul == 'none' ? 'selected' : '' }}" 
                                    data-option="none">
                                    <input type="radio" name="type_calcul" value="none" 
                                        {{ $licence->type_calcul == 'none' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="option-icon">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                        <h5>@lang('trans.no_calculation')</h5>
                                        <p class="text-muted">@lang('trans.no_calculation_desc')</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Option 2: Jours supplémentaires -->
                            <div class="col-md-4">
                                <div class="calculation-option {{ $licence->type_calcul == 'jours' ? 'selected' : '' }}" 
                                    data-option="jours">
                                    <input type="radio" name="type_calcul" value="jours" 
                                        {{ $licence->type_calcul == 'jours' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="option-icon">
                                            <i class="fas fa-plus-circle"></i>
                                        </div>
                                        <h5>@lang('trans.add_days')</h5>
                                        <p class="text-muted">@lang('trans.add_days_desc')</p>
                                    </div>
                                    <div class="jours-input-container">
                                        <label for="jours_supplementaires">@lang('trans.number_of_days')</label>
                                        <input type="number" 
                                            class="form-control" 
                                            id="jours_supplementaires" 
                                            name="jours_supplementaires" 
                                            min="1" 
                                            max="365"
                                            value="{{ old('jours_supplementaires', $licence->jours_supplementaires ?? '') }}"
                                            {{ $licence->type_calcul == 'jours' ? '' : 'disabled' }}>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Option 3: Fin de mois -->
                            <div class="col-md-4">
                                <div class="calculation-option {{ $licence->type_calcul == 'fin_mois' ? 'selected' : '' }}" 
                                    data-option="fin_mois">
                                    <input type="radio" name="type_calcul" value="fin_mois" 
                                        {{ $licence->type_calcul == 'fin_mois' ? 'checked' : '' }}>
                                    <div class="text-center">
                                        <div class="option-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <h5>@lang('trans.end_of_month')</h5>
                                        <p class="text-muted">@lang('trans.end_of_month_desc')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aperçu du calcul -->
                        <div class="example-calculation">
                            <h6 class="font-weight-bold">
                                <i class="fas fa-eye"></i> @lang('trans.calculation_preview')
                            </h6>
                            
                            <div class="text-muted mt-2" id="calculationDescription"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> @lang('trans.cancel')
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> @lang('trans.save_settings')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit License Number Modal -->
    <div class="modal fade" id="editLicenseModal" tabindex="-1" role="dialog" aria-labelledby="editLicenseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editLicenseModalLabel">@lang('trans.edit_license_number')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="licenseForm" action="{{ route('licences.update', $licence->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="numero_licence">@lang('trans.license_number') *</label>
                            <input type="text" class="form-control @error('numero_licence') is-invalid @enderror"
                                id="numero_licence" name="numero_licence"
                                value="{{ old('numero_licence', $licence->numero_licence ?? '') }}" required>
                            @error('numero_licence')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="date_deliverance">@lang('trans.deliverance_date') *</label>
                            <input type="date" class="form-control @error('date_deliverance') is-invalid @enderror"
                                id="date_deliverance" name="date_deliverance"
                                value="{{ old('date_deliverance', $licence->date_deliverance ?? '') }}" required>
                            @error('date_deliverance')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> @lang('trans.cancel')
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> @lang('trans.save_changes')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Photo Modal -->
    <div class="modal fade" id="editPhotoModal" tabindex="-1" role="dialog" aria-labelledby="editPhotoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="editPhotoModalLabel">
                        <i class="fas fa-camera mr-2"></i>@lang('trans.edit_photo')
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="photoForm" action="{{ route('licences.update.photo', $licence->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img src="{{ asset('/uploads/' . ($licence->photo ?? 'default.png')) }}"
                                alt="Current Profile Picture" class="img-thumbnail photo-preview" id="currentPhotoPreview">
                        </div>
                        
                        <div class="form-group">
                            <label for="photo">@lang('trans.choose_new_photo')</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('photo') is-invalid @enderror" 
                                    id="photo" name="photo" accept="image/*" required onchange="previewImage(this)">
                                <label class="custom-file-label" for="photo">@lang('trans.choose_file')</label>
                            </div>
                            @error('photo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                @lang('trans.allowed_formats'): JPG, JPEG, PNG, GIF. @lang('trans.max_size'): 2MB
                            </small>
                        </div>

                        <div class="text-center" id="newPhotoPreviewContainer" style="display: none;">
                            <label>@lang('trans.new_photo_preview')</label>
                            <img src="" alt="New Photo Preview" class="img-thumbnail photo-preview" id="newPhotoPreview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> @lang('trans.cancel')
                        </button>
                        <button type="submit" class="btn btn-success" id="savePhotoBtn">
                            <i class="fas fa-save"></i> @lang('trans.save_photo')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
    @push('custom')
    <script>
        $(document).ready(function() {
            // Gestion de la sélection des options de calcul
            $('.calculation-option').on('click', function() {
                var option = $(this).data('option');
                
                // Retirer la classe selected de toutes les options
                $('.calculation-option').removeClass('selected');
                
                // Ajouter la classe selected à l'option cliquée
                $(this).addClass('selected');
                
                // Cocher le radio correspondant
                $(this).find('input[type="radio"]').prop('checked', true);
                
                // Activer/désactiver le champ jours supplémentaires
                if (option === 'jours') {
                    $('#jours_supplementaires').prop('disabled', false).focus();
                } else {
                    $('#jours_supplementaires').prop('disabled', true).val('');
                }
                
                updatePreview();
            });
            
            // Mise à jour de l'aperçu en temps réel
            $('#jours_supplementaires').on('keyup change', function() {
                updatePreview();
            });
            
            // Fonction de mise à jour de l'aperçu
            function updatePreview() {
                var selectedOption = $('input[name="type_calcul"]:checked').val();
                var baseDate = moment();
                var calculatedDate = moment(baseDate);
                var description = '';
                
                if (selectedOption === 'jours') {
                    var jours = parseInt($('#jours_supplementaires').val()) || 0;
                    if (jours > 0) {
                        calculatedDate.add(jours, 'days');
                        description = 'Date de base + ' + jours + ' jour(s)';
                    } else {
                        calculatedDate = baseDate;
                        description = 'Veuillez entrer un nombre de jours';
                    }
                } else if (selectedOption === 'fin_mois') {
                    calculatedDate.endOf('month');
                    description = 'Date de base prolongée jusqu\'à la fin du mois';
                } else {
                    calculatedDate = baseDate;
                    description = 'Aucun calcul supplémentaire';
                }
                
                $('#previewDate').text(calculatedDate.format('DD/MM/YYYY'));
                $('#calculationDescription').text(description);
            }
            
            // Validation du formulaire
            $('#calculationForm').on('submit', function(e) {
                e.preventDefault();
                
                var selectedOption = $('input[name="type_calcul"]:checked').val();
                
                if (selectedOption === 'jours') {
                    var jours = parseInt($('#jours_supplementaires').val()) || 0;
                    if (jours <= 0) {
                        toastr.error('@lang("trans.please_enter_valid_days")');
                        return false;
                    }
                }
                
                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.html();
                
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> @lang("trans.saving")...').prop('disabled', true);
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON?.message || '@lang("trans.error_occurred")';
                        toastr.error(errorMessage);
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            
            // Initialisation
            updatePreview();
            
            // Validation du formulaire de licence
            /*$('#licenseForm').validate({
                rules: {
                    numero_licence: {
                        required: true,
                        minlength: 3
                    },
                    date_deliverance: {
                        required: true,
                        date: true
                    }
                },
                messages: {
                    numero_licence: {
                        required: "@lang('trans.license_number_required')",
                        minlength: "@lang('trans.license_number_minlength')"
                    },
                    date_deliverance: {
                        required: "@lang('trans.deliverance_date_required')",
                        date: "@lang('trans.invalid_date')"
                    }
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
            });*/
            
            // Photo upload validation
            $('#photoForm').on('submit', function(e) {
                var fileInput = $('#photo')[0];
                if (fileInput.files.length > 0) {
                    var fileSize = fileInput.files[0].size / 1024 / 1024;
                    var fileType = fileInput.files[0].type;
                    
                    if (fileSize > 2) {
                        e.preventDefault();
                        toastr.error("@lang('trans.file_too_large')");
                        return false;
                    }
                    
                    if (!fileType.match('image.*')) {
                        e.preventDefault();
                        toastr.error("@lang('trans.invalid_file_type')");
                        return false;
                    }
                }
            });
            
            // Custom file input label update
            $('#photo').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
            
            // Success message handling
            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
        
        // Image preview function
        function previewImage(input) {
            var newPreviewContainer = document.getElementById('newPhotoPreviewContainer');
            var newPreview = document.getElementById('newPhotoPreview');
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    newPreview.src = e.target.result;
                    newPreviewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                newPreviewContainer.style.display = 'none';
                newPreview.src = '';
            }
        }
    </script>
@endpush
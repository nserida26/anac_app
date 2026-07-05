@extends('daf.layouts.app')

@section('title')
    @lang('trans.create_invoices')
@endsection

@section('contentheader')
    @lang('trans.create_invoices')
@endsection

@section('contentheaderlink')
    <a href="{{ route('daf') }}">
        @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
    </a>
@endsection

@section('contentheaderactive')
    @lang('trans.create_invoices')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
    <style>
        .invoice-form-card {
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .invoice-form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .order-item {
            border-left: 3px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .total-amount {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .file-upload-zone {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background-color: #fafafa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .file-upload-zone:hover {
            border-color: #007bff;
            background-color: #f0f8ff;
        }
        .file-upload-zone.dragover {
            border-color: #28a745;
            background-color: #f0fff4;
        }
        .file-preview {
            margin-top: 15px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card invoice-form-card">
                    <div class="invoice-form-header">
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice mr-2"></i>
                            @if($isBulkMode ?? false)
                                @lang('trans.create_multiple_invoices')
                            @else
                                @lang('trans.create_new_invoice')
                            @endif
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        {{-- Résumé des ordres de paiement sélectionnés --}}
                        <div class="order-summary">
                            <h5 class="mb-3">
                                <i class="fas fa-list-check mr-2"></i>
                                @lang('trans.selected_payment_orders')
                                <span class="badge badge-primary ml-2">{{ count($selectedOrdres) }}</span>
                            </h5>
                            
                            @foreach($selectedOrdres as $index => $ordre)
                                <div class="order-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <strong>{{ $ordre->reference }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                @if(empty($ordre->demande->demandeur->compagnie))
                                                    {{ $ordre->demande->demandeur->np }}
                                                @else
                                                    {{ $ordre->demande->demandeur->compagnie->nom_entreprise }} - 
                                                    {{ $ordre->demande->demandeur->np }}
                                                @endif
                                            </small>
                                            <br>
                                            <small>
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($ordre->date_ordre)->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <span class="badge badge-success">
                                                {{ number_format($ordre->montant, 0, ',', ' ') }} MRU
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if(count($selectedOrdres) > 1)
                                <div class="text-right mt-3">
                                    <h5 class="total-amount">
                                        @lang('trans.total'): 
                                        {{ number_format($selectedOrdres->sum('montant'), 0, ',', ' ') }} MRU
                                    </h5>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Formulaire principal --}}
                        <form action="{{ route('daf.store') }}" method="POST" enctype="multipart/form-data" id="invoiceForm">
                            @csrf
                            
                            {{-- Champ caché pour les IDs des ordres (mode multiple) --}}
                            @if($isBulkMode ?? false)
                                @foreach($selectedOrdres as $ordre)
                                    <input type="hidden" name="ordre_ids[]" value="{{ $ordre->id }}">
                                @endforeach
                            @else
                                <input type="hidden" name="demande_id" value="{{ $selectedOrdres->first()->demande->id }}">
                                <input type="hidden" name="ordre_id" value="{{ $selectedOrdres->first()->id }}">
                            @endif
                            
                            {{-- Configuration commune de la facture --}}
                            <div class="section-title">
                                <i class="fas fa-cog mr-2"></i>@lang('trans.invoice_settings')
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_facture">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            @lang('trans.invoice_date')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="date_facture" 
                                                   id="date_facture" 
                                                   class="form-control datepicker" 
                                                   value="{{ date('d/m/Y') }}"
                                                   required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_limite">
                                            <i class="far fa-clock mr-1"></i>
                                            @lang('trans.due_date')
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="date_limite" 
                                                   id="date_limite" 
                                                   class="form-control datepicker" 
                                                   value="{{ date('d/m/Y', strtotime('+30 days')) }}"
                                                   required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            @lang('trans.due_date_help')
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mode_paiement">
                                            <i class="fas fa-credit-card mr-1"></i>
                                            @lang('trans.payment_method')
                                        </label>
                                        <select name="mode_paiement" id="mode_paiement" class="form-control select2">
                                            <option value="virement">@lang('trans.bank_transfer')</option>
                                            <option value="cheque">@lang('trans.check')</option>
                                            <option value="especes">@lang('trans.cash')</option>
                                            <option value="carte">@lang('trans.credit_card')</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="conditions_paiement">
                                            <i class="fas fa-file-contract mr-1"></i>
                                            @lang('trans.payment_terms')
                                        </label>
                                        <input type="text" 
                                               name="conditions_paiement" 
                                               id="conditions_paiement" 
                                               class="form-control" 
                                               value="@lang('trans.standard_terms')"
                                               placeholder="@lang('trans.payment_terms_placeholder')">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Notes et remarques --}}
                            <div class="form-group">
                                <label for="notes">
                                    <i class="fas fa-pencil-alt mr-1"></i>
                                    @lang('trans.notes')
                                </label>
                                <textarea name="notes" 
                                          id="notes" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="@lang('trans.notes_placeholder')"></textarea>
                            </div>
                            
                            {{-- Section : Téléchargement des fichiers --}}
                            <div class="section-title mt-4">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                @lang('trans.upload_documents')
                            </div>
                            
                            @if($isBulkMode ?? false)
                                {{-- Mode multiple : Un fichier par ordre --}}
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    @lang('trans.bulk_upload_info')
                                </div>
                                
                                @foreach($selectedOrdres as $ordre)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <strong>{{ $ordre->reference }}</strong>
                                            <span class="badge badge-primary float-right">
                                                {{ number_format($ordre->montant, 0, ',', ' ') }} MRU
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="file-upload-item" data-order="{{ $ordre->id }}">
                                                <div class="file-upload-zone" id="dropZone{{ $ordre->id }}">
                                                    <input type="file" 
                                                           name="factures[{{ $ordre->id }}]" 
                                                           id="file{{ $ordre->id }}" 
                                                           class="d-none" 
                                                           accept=".pdf,.doc,.docx"
                                                           required>
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                    <h6>@lang('trans.drag_drop_file')</h6>
                                                    <p class="text-muted mb-2">@lang('trans.or')</p>
                                                    <button type="button" 
                                                            class="btn btn-outline-primary btn-sm"
                                                            onclick="document.getElementById('file{{ $ordre->id }}').click()">
                                                        <i class="fas fa-folder-open mr-1"></i>
                                                        @lang('trans.browse_files')
                                                    </button>
                                                    <p class="small text-muted mt-2">
                                                        @lang('trans.accepted_formats'): PDF, DOC, DOCX (Max: 10MB)
                                                    </p>
                                                </div>
                                                <div class="file-preview" id="preview{{ $ordre->id }}"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{-- Mode simple : Un seul fichier --}}
                                <div class="file-upload-item">
                                    <div class="file-upload-zone" id="dropZone">
                                        <input type="file" 
                                               name="facture" 
                                               id="file" 
                                               class="d-none" 
                                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                               required>
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6>@lang('trans.drag_drop_file')</h6>
                                        <p class="text-muted mb-2">@lang('trans.or')</p>
                                        <button type="button" 
                                                class="btn btn-outline-primary"
                                                onclick="document.getElementById('file').click()">
                                            <i class="fas fa-folder-open mr-1"></i>
                                            @lang('trans.browse_files')
                                        </button>
                                        <p class="small text-muted mt-2">
                                            @lang('trans.accepted_formats'): PDF, DOC, DOCX, JPG, PNG (Max: 10MB)
                                        </p>
                                    </div>
                                    <div class="file-preview" id="filePreview"></div>
                                </div>
                            @endif
                            
                            {{-- Actions du formulaire --}}
                            <div class="form-group mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success btn-lg btn-block" id="submitBtn">
                                            <i class="fas fa-save mr-2"></i>
                                            @lang('trans.create_invoice')
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="{{ route('daf') }}" class="btn btn-outline-secondary btn-lg btn-block">
                                            <i class="fas fa-times mr-2"></i>
                                            @lang('trans.cancel')
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
@endpush

@push('custom')
    <script>
        $(function() {
            // Initialisation de Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });
            
            // Initialisation du Datepicker
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                language: 'fr',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });
            
            // Gestion du Drag & Drop pour les fichiers
            function initializeFileUpload(dropZoneId, inputId, previewId) {
                const dropZone = document.getElementById(dropZoneId);
                const fileInput = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                
                if (!dropZone) return;
                
                // Empêcher le comportement par défaut
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });
                
                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                
                // Mise en évidence lors du drag
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('dragover');
                    }, false);
                });
                
                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('dragover');
                    }, false);
                });
                
                // Gestion du drop
                dropZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        handleFileSelect(files[0], preview);
                    }
                }, false);
                
                // Clic sur la zone
                dropZone.addEventListener('click', () => {
                    fileInput.click();
                });
                
                // Sélection de fichier
                fileInput.addEventListener('change', (e) => {
                    if (fileInput.files.length > 0) {
                        handleFileSelect(fileInput.files[0], preview);
                    }
                });
            }
            
            function handleFileSelect(file, previewElement) {
                // Vérification de la taille (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('@lang("trans.file_too_large")');
                    return;
                }
                
                // Affichage du fichier sélectionné
                const fileSize = (file.size / 1024).toFixed(2);
                previewElement.innerHTML = `
                    <div class="file-info">
                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                        <strong>${file.name}</strong>
                        <span class="badge badge-secondary ml-2">${fileSize} KB</span>
                        <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="clearFile('${fileInput.id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewElement.style.display = 'block';
            }
            
            // Initialisation pour le mode simple
            @if(!($isBulkMode ?? false))
                initializeFileUpload('dropZone', 'file', 'filePreview');
            @else
                // Initialisation pour le mode multiple
                @foreach($selectedOrdres as $ordre)
                    initializeFileUpload('dropZone{{ $ordre->id }}', 'file{{ $ordre->id }}', 'preview{{ $ordre->id }}');
                @endforeach
            @endif
            
            // Validation du formulaire
            $('#invoiceForm').on('submit', function(e) {
                let hasError = false;
                let errorMessage = '';
                
                @if($isBulkMode ?? false)
                    // Vérifier que chaque ordre a un fichier
                    @foreach($selectedOrdres as $ordre)
                        if (!$('#file{{ $ordre->id }}')[0].files.length) {
                            hasError = true;
                            errorMessage += '@lang("trans.file_required_for") {{ $ordre->reference }}\n';
                        }
                    @endforeach
                @else
                    if (!$('#file')[0].files.length) {
                        hasError = true;
                        errorMessage = '@lang("trans.file_required")';
                    }
                @endif
                
                // Vérification des dates
                const dateFacture = moment($('#date_facture').val(), 'DD/MM/YYYY');
                const dateLimite = moment($('#date_limite').val(), 'DD/MM/YYYY');
                
                if (dateLimite.isBefore(dateFacture)) {
                    hasError = true;
                    errorMessage += '@lang("trans.due_date_error")\n';
                }
                
                if (hasError) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
                
                // Désactiver le bouton pour éviter les doubles soumissions
                $('#submitBtn').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm mr-2" role="status"></span>
                    @lang("trans.processing")
                `);
            });
            
            // Calcul automatique de la date limite
            $('#date_facture').on('change', function() {
                const dateFacture = moment($(this).val(), 'DD/MM/YYYY');
                const dateLimite = dateFacture.add(30, 'days').format('DD/MM/YYYY');
                $('#date_limite').val(dateLimite);
            });
        });
        
        // Fonction pour effacer le fichier sélectionné
        function clearFile(inputId) {
            document.getElementById(inputId).value = '';
            document.getElementById(inputId.replace('file', 'preview')).style.display = 'none';
            document.getElementById(inputId.replace('file', 'preview')).innerHTML = '';
        }
    </script>
@endpush
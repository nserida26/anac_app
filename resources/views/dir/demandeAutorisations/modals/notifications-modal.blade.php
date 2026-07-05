<!-- resources/views/dir/demandeAutorisations/modals/notifications-modal.blade.php -->
<div class="modal fade" id="sendNotificationModal-{{ $demande->id }}" tabindex="-1" role="dialog" aria-labelledby="sendNotificationModalLabel-{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendNotificationModalLabel-{{ $demande->id }}">
                    <i class="fas fa-bell"></i> @lang('trans.send_notifications') - @lang('trans.demande') #{{ $demande->code }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="notificationForm-{{ $demande->id }}" action="{{ route('send.notifications',$demande->id) }}" method="POST">
                @csrf
                <input type="hidden" name="demande_id" value="{{ $demande->id }}">
                
                <div class="modal-body">
                    <!-- Informations de la demande -->
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>@lang('trans.code_demande'):</strong> {{ $demande->code }}</small><br>
                                <small><strong>@lang('trans.type_demande'):</strong> {{ $demande->type->libelle }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>@lang('trans.type_vol'):</strong> {{ $demande->typeVol->nom ?? 'N/A' }}</small><br>
                                <small><strong>@lang('trans.periode'):</strong> {{ $demande->date_debut }} → {{ $demande->date_fin }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sélection des destinataires par email -->
                    <div class="form-group">
                        <label for="email_recipients-{{ $demande->id }}">
                            <i class="fas fa-envelope"></i> @lang('trans.destinataires_email')
                        </label>
                        <select class="form-control email-recipients" 
                                id="email_recipients-{{ $demande->id }}" 
                                name="email_recipients[]" multiple="multiple" 
                                style="width: 100%; min-height: 100px;">
                            <!-- Options pré-définies -->
                            @if(isset($demande->user) && $demande->user->email)
                                <option value="{{ $demande->user->email }}" selected>{{ $demande->user->email }} (@lang('trans.demandeur'))</option>
                            @endif
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> @lang('trans.email_format_info')
                        </small>
                    </div>
                    
                    <!-- Sélection des destinataires par téléphone (WhatsApp) -->
                    <div class="form-group mt-3">
                        <label for="whatsapp_recipients-{{ $demande->id }}">
                            <i class="fab fa-whatsapp"></i> @lang('trans.destinataires_whatsapp')
                        </label>
                        <select class="form-control whatsapp-recipients" 
                                id="whatsapp_recipients-{{ $demande->id }}" 
                                name="whatsapp_recipients[]" multiple="multiple" 
                                style="width: 100%; min-height: 100px;">
                            <!-- Options pré-définies -->
                            @if(isset($demande->user) && $demande->user->demandeur && $demande->user->demandeur->whatsapp)
                                <option value="{{ $demande->user->demandeur->whatsapp }}" selected>{{ $demande->user->demandeur->whatsapp }} (@lang('trans.demandeur'))</option>
                            @endif
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> @lang('trans.whatsapp_format_info')
                        </small>
                    </div>
                    
                    <!-- Aperçu des destinataires -->
                    <div class="row mt-3" id="recipientsPreview-{{ $demande->id }}" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-users"></i> @lang('trans.aperçu_destinataires') :</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>@lang('trans.email_recipients') :</strong> <span id="emailCount-{{ $demande->id }}">0</span> @lang('trans.recipients')</p>
                                        <div id="emailPreviewList-{{ $demande->id }}" class="small"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>@lang('trans.whatsapp_recipients') :</strong> <span id="whatsappCount-{{ $demande->id }}">0</span> @lang('trans.recipients')</p>
                                        <div id="whatsappPreviewList-{{ $demande->id }}" class="small"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.fermer')
                    </button>
                    <button type="submit" class="btn btn-success" id="sendNotificationBtn-{{ $demande->id }}">
                        <i class="fas fa-paper-plane"></i> @lang('trans.envoyer')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
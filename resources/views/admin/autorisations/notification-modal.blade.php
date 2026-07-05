<!-- Modal pour l'envoi de notifications - Autorisation #{{ $autorisation->code_autorisation }} -->
<div class="modal fade" id="sendNotificationModal-{{ $autorisation->id }}" 
     tabindex="-1" role="dialog" 
     aria-labelledby="sendNotificationModalLabel-{{ $autorisation->id }}" 
     aria-hidden="true"
     data-autorisation-id="{{ $autorisation->id }}"
     data-autorisation-code="{{ $autorisation->code_autorisation }}"
     data-demande-id="{{ $autorisation->demande->id ?? '' }}"
     data-demande-type="{{ $autorisation->demande->type->libelle ?? '' }}"
     data-demande-date-debut="{{ $autorisation->demande->date_debut ?? '' }}"
     data-demande-date-fin="{{ $autorisation->demande->date_fin ?? '' }}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendNotificationModalLabel-{{ $autorisation->id }}">
                    <i class="fas fa-bell"></i> @lang('trans.send_notifications') - Autorisation #{{ $autorisation->code_autorisation }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="notificationForm-{{ $autorisation->id }}" action="{{ route('send.notifications',$autorisation->demande->id) }}" method="POST">
                @csrf
                <input type="hidden" name="autorisation_id" value="{{ $autorisation->id }}">
                <input type="hidden" name="demande_id" value="{{ $autorisation->demande->id ?? '' }}">
                
                <div class="modal-body">
                    <!-- Informations de l'autorisation -->
                    @if(isset($autorisation->demande))
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <small><strong>Autorisation:</strong> {{ $autorisation->code_autorisation }}</small><br>
                                <small><strong>Type demande:</strong> {{ $autorisation->demande->type->libelle ?? 'N/A' }}</small>
                            </div>
                            <div class="col-md-6">
                                <small><strong>Période:</strong> {{ $autorisation->demande->date_debut ?? 'N/A' }} → {{ $autorisation->demande->date_fin ?? 'N/A' }}</small><br>
                                <small><strong>Validité:</strong> {{ $autorisation->date_delivrance }} → {{ $autorisation->date_expiration }}</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Sélection des destinataires par email -->
                    <div class="form-group">
                        <label for="email_recipients-{{ $autorisation->id }}">
                            <i class="fas fa-envelope"></i> @lang('trans.email_recipients')
                            <span class="text-muted small">(@lang('trans.email_format_info'))</span>
                        </label>
                        <select class="form-control" id="email_recipients-{{ $autorisation->id }}" 
                                name="email_recipients[]" multiple="multiple" 
                                style="width: 100%; min-height: 100px;">
                            @if(isset($autorisation->demande->user) && $autorisation->demande->user->email)
                            <option value="{{ $autorisation->demande->user->email }}" selected>
                                {{ $autorisation->demande->user->email }} (Demandeur)
                            </option>
                            @endif
                            @if(isset($autorisation->demande->receivingParties))
                                @foreach($autorisation->demande->receivingParties as $party)
                                    @if($party->email_contact)
                                    <option value="{{ $party->email_contact }}" selected>
                                        {{ $party->email_contact }} ({{ $party->nom_contact }})
                                    </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> @lang('trans.enter_email_placeholder')
                        </small>
                    </div>
                    
                    <!-- Sélection des destinataires par téléphone (WhatsApp) -->
                    <div class="form-group mt-3">
                        <label for="whatsapp_recipients-{{ $autorisation->id }}">
                            <i class="fab fa-whatsapp"></i> @lang('trans.whatsapp_recipients')
                            <span class="text-muted small">(@lang('trans.whatsapp_format_info'))</span>
                        </label>
                        <select class="form-control" id="whatsapp_recipients-{{ $autorisation->id }}" 
                                name="whatsapp_recipients[]" multiple="multiple" 
                                style="width: 100%; min-height: 100px;">
                            @if(isset($autorisation->demande->user) && $autorisation->demande->user->demandeur && $autorisation->demande->user->demandeur->telephone)
                            <option value="{{ $autorisation->demande->user->demandeur->telephone }}" selected>
                                {{ $autorisation->demande->user->demandeur->telephone }} (Demandeur)
                            </option>
                            @endif
                            @if(isset($autorisation->demande->receivingParties))
                                @foreach($autorisation->demande->receivingParties as $party)
                                    @if($party->telephone_contact)
                                    <option value="{{ $party->telephone_contact }}" selected>
                                        {{ $party->telephone_contact }} ({{ $party->nom_contact }})
                                    </option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> @lang('trans.whatsapp_format_info')
                        </small>
                    </div>
                    
                
                    
                    <!-- Aperçu des destinataires -->
                    <div class="row mt-3" id="recipientsPreview-{{ $autorisation->id }}" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-users"></i> @lang('trans.recipients_preview') :</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-envelope"></i> @lang('trans.email_recipients') :</strong> 
                                           <span id="emailCount-{{ $autorisation->id }}">0</span> @lang('trans.recipients')</p>
                                        <div id="emailPreviewList-{{ $autorisation->id }}" class="small"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><i class="fab fa-whatsapp"></i> @lang('trans.whatsapp_recipients') :</strong> 
                                           <span id="whatsappCount-{{ $autorisation->id }}">0</span> @lang('trans.recipients')</p>
                                        <div id="whatsappPreviewList-{{ $autorisation->id }}" class="small"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> @lang('trans.close')
                    </button>
                    <button type="submit" class="btn btn-success" id="sendNotificationBtn-{{ $autorisation->id }}">
                        <i class="fas fa-paper-plane"></i> @lang('trans.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
                @if ($demandeAutorisation->type->id === 2)
                    <!-- Receiving Party Section -->
                    <div class="card">
                        <x-anac-card-header icon="fas fa-address-book" :title="__('trans.receiving_party_info')" />
                        <div class="card-body">
                            <form method="POST" id="receivingPartyForm" action="{{ url('/user/receiving-parties/') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nomcontact"><i
                                                    class="fa fa-user mr-2"></i>@lang('trans.full_name')*</label>
                                            <input id="nomcontact" name="nom_contact" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telephonecontact"><i
                                                    class="fa fa-phone mr-2"></i>@lang('trans.phone_whatsapp')*</label>
                                            <input id="telephonecontact" name="telephone_contact" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emailcontact"><i
                                                    class="fa fa-envelope mr-2"></i>@lang('trans.email')</label>
                                            <input id="emailcontact" name="email_contact" type="email"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fonctioncontact"><i
                                                    class="fa fa-certificate mr-2"></i>@lang('trans.function')</label>
                                            <input id="fonctioncontact" name="fonction_contact" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="autrerenseignement">@lang('trans.other_information')</label>
                                            <textarea class="form-control" id="autrerenseignement" name="autres_renseignements" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pieceidentite"><i
                                                    class="fa fa-credit-card mr-2"></i>@lang('trans.identity_document')</label>
                                            <input id="pieceidentite" name="piece_identite" type="file"
                                                class="form-control-file">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="submit" class="anac-btn anac-btn--primary float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add_action')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($receivingParties->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="partyTable">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('trans.contact')</th>
                                                        <th>@lang('trans.phone')</th>
                                                        <th>@lang('trans.email')</th>
                                                        <th>@lang('trans.function')</th>
                                                        <th>@lang('trans.identity_document')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($receivingParties as $party)
                                                        <tr id="party-{{ $party->id }}">
                                                            <td>{{ $party->nom_contact }}</td>
                                                            <td>{{ $party->telephone_contact }}</td>
                                                            <td>{{ $party->email_contact }}</td>
                                                            <td>{{ $party->fonction_contact }}</td>
                                                            <td>
                                                                @if ($party->piece_identite_path)
                                                                    <a href="{{ asset('/uploads/' . $party->piece_identite_path) }}"
                                                                        target="_blank" class="btn btn-sm anac-action anac-action--blue">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    @lang('trans.not_available')
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="anac-actions">
                                                                    <button class="btn btn-sm anac-action anac-action--yellow edit-party"
                                                                        data-id="{{ $party->id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm anac-action anac-action--red delete-party"
                                                                        data-id="{{ $party->id }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-party-{{ $party->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updatePartyForm-{{ $party->id }}"
                                                                    enctype="multipart/form-data">
                                                                    @csrf

                                                                    <input type="hidden" name="party_id"
                                                                        value="{{ $party->id }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.full_name')*</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nom_contact"
                                                                                    value="{{ $party->nom_contact }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.phone_whatsapp')*</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="telephone_contact"
                                                                                    value="{{ $party->telephone_contact }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.email')</label>
                                                                                <input type="email" class="form-control"
                                                                                    name="email_contact"
                                                                                    value="{{ $party->email_contact }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.function')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="fonction_contact"
                                                                                    value="{{ $party->fonction_contact }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.other_information')</label>
                                                                                <textarea class="form-control" name="autres_renseignements" rows="2">{{ $party->autres_renseignements }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.identity_document')
                                                                                    @lang('trans.keep_current_file_hint')</label>
                                                                                <input type="file"
                                                                                    class="form-control-file"
                                                                                    name="piece_identite">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="btn btn-sm anac-action anac-action--blue update-party"
                                                                        data-id="{{ $party->id }}">@lang('trans.update')</button>
                                                                    <button type="button"
                                                                        class="btn btn-sm anac-action anac-action--grey cancel-edit"
                                                                        data-id="{{ $party->id }}"
                                                                        data-type="party">@lang('trans.cancel')</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

@push('custom')
    <script>
        $(document).ready(function() {
            // Handle receiving party operations
            $('#receivingPartyForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', 'form[id^="updatePartyForm-"]', function(e) {
                e.preventDefault();
                const formId = $(this).attr('id');
                const id = formId.split('-')[1];
                let formData = new FormData(this);

                $.ajax({
                    url: `/user/receiving-parties/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.delete-party', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: @json(__('trans.confirm_delete_title')),
                    text: @json(__('trans.confirm_delete_text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: @json(__('trans.confirm_delete_yes')),
                    cancelButtonText: @json(__('trans.cancel'))
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/receiving-parties/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#party-${id}, #edit-form-party-${id}`).remove();
                                toastr.success(@json(__('trans.deleted_success')));
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

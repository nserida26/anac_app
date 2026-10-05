                @if ($demandeAutorisation->type->id === 4)
                    <div class="card card-primary">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">@lang('trans.deceased_persons')</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="deceasedPersonForm" action="{{ url('/user/personnes-deces') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nom_prenom"><span
                                                    class="text-danger">*</span>@lang('trans.full_name')</label>
                                            <input type="text" class="form-control" id="nom_prenom" name="nom_prenom"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="numero_passport">@lang('trans.passport_number')</label>
                                            <input type="text" class="form-control" id="numero_passport"
                                                name="numero_passport">
                                            <small class="form-text text-muted">@lang('trans.passport_number_optional_hint')</small>
                                        </div>
                                    </div>
                                </div>
                                {{-- Le justificatif n'est plus saisi ici : il est déjà déposé dans la section Documents. --}}

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitDeceasedPerson" type="submit"
                                            class="btn btn-success float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($personnesDeces->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="deceasedPersonsTable">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('trans.full_name')</th>
                                                        <th>@lang('trans.passport_number')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($personnesDeces as $personne)
                                                        <tr id="personne-{{ $personne->id }}">
                                                            <td>{{ $personne->nom_prenom }}</td>
                                                            <td>{{ $personne->numero_passport ?? 'N/A' }}</td>
                                                            <td>
                                                                <button class="btn btn-warning btn-sm edit-personne"
                                                                    data-id="{{ $personne->id }}">@lang('trans.update')</button>
                                                                <button class="btn btn-danger btn-sm delete-personne"
                                                                    data-id="{{ $personne->id }}">@lang('trans.delete')</button>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-personne-{{ $personne->id }}"
                                                            style="display: none;">
                                                            <td colspan="3">
                                                                <form id="updateDeceasedPersonForm-{{ $personne->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="personne_id"
                                                                        id="personne_id" value="{{ $personne->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.full_name')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="nom_prenom"
                                                                                    value="{{ $personne->nom_prenom }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.passport_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="numero_passport"
                                                                                    value="{{ $personne->numero_passport }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-primary btn-sm update-personne">
                                                                        @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-secondary btn-sm cancel-edit"
                                                                        data-id="{{ $personne->id }}"
                                                                        data-type="personne">
                                                                        @lang('trans.cancel')
                                                                    </button>
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
            // Handle form submission for adding deceased person
            $('#deceasedPersonForm').submit(function(e) {
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
                        toastr.error(xhr.responseJSON?.message || "Une erreur s'est produite.");
                    }
                });
            });

            // Handle update form submission
            $(document).on('submit', '[id^="updateDeceasedPersonForm-"]', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);
                var id = form.find('input[name="personne_id"]').val();

                $.ajax({
                    url: `/user/personnes-deces/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || "Une erreur s'est produite.");
                    }
                });
            });

            // Handle delete
            $(document).on('click', '.delete-personne', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Confirmer la suppression',
                    text: "Êtes-vous sûr de vouloir supprimer cette personne?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/user/personnes-deces/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#personne-${id}, #edit-form-personne-${id}`)
                                    .remove();
                                toastr.success('Personne supprimée avec succès');
                            }
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-personne', function() {
                const id = $(this).data('id');
                toggleEditForm(id, 'personne');
            });
            // Cancel edit button
            $(document).on('click', '.cancel-edit', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                toggleEditForm(id, type);
            });

            // Show edit form
            $(document).on('click', '.edit-membre, .edit-fret, .edit-party', function() {
                const id = $(this).data('id');
                const type = $(this).closest('tr').attr('id').split('-')[0];
                toggleEditForm(id, type);
            });
        });
    </script>
@endpush

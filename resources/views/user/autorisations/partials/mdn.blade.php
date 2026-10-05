                @if (in_array($demandeAutorisation->type->id, [5, 6, 7]))
                    <div class="card mt-4">
                        <x-anac-card-header icon="fas fa-file-contract" :title="__('trans.mdn_management')" />
                        <div class="card-body">
                            {{-- Add MDN Form --}}
                            <form method="POST" id="mdnForm" action="{{ url('/user/mdns') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="date_autorisation">@lang('trans.authorization_date') <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date_autorisation"
                                                name="date_autorisation" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="numero_mdn">@lang('trans.mdn_number') <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numero_mdn" name="numero_mdn"
                                                placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nationalite">@lang('trans.nationality') <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="nationalite" name="pays_id" required>
                                                @foreach ($pays as $pay)
                                                    <option value="{{ $pay->id }}">{{ $pay->nom }}
                                                        ({{ $pay->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitMdn" type="submit" class="anac-btn anac-btn--primary float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- MDN List --}}
                            @if (isset($mdns) && $mdns->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="mdnTable">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('trans.authorization_date')</th>
                                                        <th>@lang('trans.mdn_number')</th>
                                                        <th>@lang('trans.nationality')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($mdns as $mdn)
                                                        <tr id="mdn-{{ $mdn->id }}">
                                                            <td>{{ $mdn->formatted_date_autorisation }}</td>
                                                            <td>{{ $mdn->numero_mdn }}</td>
                                                            <td>
                                                                <span class="badge badge--info">
                                                                    {{ $mdn->pays->nom ?? __('trans.not_available') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="anac-actions">
                                                                    <button class="btn btn-sm anac-action anac-action--yellow edit-mdn"
                                                                        data-id="{{ $mdn->id }}">
                                                                        <i class="fas fa-edit"></i> @lang('trans.update')
                                                                    </button>
                                                                    <button class="btn btn-sm anac-action anac-action--red delete-mdn"
                                                                        data-id="{{ $mdn->id }}">
                                                                        <i class="fas fa-trash"></i> @lang('trans.delete')
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-mdn-{{ $mdn->id }}"
                                                            style="display: none;">
                                                            <td colspan="4">
                                                                <form id="updateMdnForm-{{ $mdn->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="mdn_id"
                                                                        value="{{ $mdn->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.authorization_date')</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="date_autorisation"
                                                                                    value="{{ $mdn->date_autorisation ? $mdn->date_autorisation->format('Y-m-d') : '' }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.mdn_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="numero_mdn"
                                                                                    value="{{ $mdn->numero_mdn }}"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.nationality')</label>
                                                                                <select class="form-control"
                                                                                    id="nationalite" name="pays_id"
                                                                                    required>
                                                                                    @foreach ($pays as $pay)
                                                                                        <option
                                                                                            value="{{ $pay->id }}">
                                                                                            {{ $pay->nom }}
                                                                                            ({{ $pay->code }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-sm anac-action anac-action--blue update-mdn">
                                                                        <i class="fas fa-save"></i> @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm anac-action anac-action--grey cancel-edit-mdn"
                                                                        data-id="{{ $mdn->id }}">
                                                                        <i class="fas fa-times"></i> @lang('trans.cancel')
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
            // Show edit form for MDN
            $(document).on('click', '.edit-mdn', function() {
                const id = $(this).data('id');
                toggleMdnEditForm(id);
            });

            // Cancel edit
            $(document).on('click', '.cancel-edit-mdn', function() {
                const id = $(this).data('id');
                $('#edit-form-mdn-' + id).hide();
                $('#mdn-' + id).show();
            });

            // Handle MDN form submission
            $('#mdnForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        toastr.success(response.message || @json(__('trans.added_success')));
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || @json(__('trans.error_occurred'));
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        toastr.error(message);
                    }
                });
            });

            // Handle MDN update
            $(document).on('submit', 'form[id^="updateMdnForm-"]', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = form.find('input[name="mdn_id"]').val();
                let formData = new FormData(this);

                $.ajax({
                    url: '{{ url('/user/mdns') }}/' + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        toastr.success(response.message || @json(__('trans.updated_success')));
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || @json(__('trans.error_occurred'));
                        if (xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        toastr.error(message);
                    }
                });
            });

            // Handle MDN delete
            $(document).on('click', '.delete-mdn', function() {
                const id = $(this).data('id');

                if (confirm(@json(__('trans.confirm_delete_text')))) {
                    $.ajax({
                        url: '{{ url('/user/mdns') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.message || @json(__('trans.deleted_success')));
                            $('#mdn-' + id).remove();
                            $('#edit-form-mdn-' + id).remove();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                                @json(__('trans.error_occurred')));
                        }
                    });
                }
            });
        });

        // Toggle MDN edit form
        function toggleMdnEditForm(id) {
            $('#mdn-' + id).toggle();
            $('#edit-form-mdn-' + id).toggle();

            // Hide other open edit forms
            $('tr[id^="edit-form-mdn-"]').each(function() {
                const formId = $(this).attr('id').replace('edit-form-mdn-', '');
                if (formId != id) {
                    $(this).hide();
                    $('#mdn-' + formId).show();
                }
            });
        }
    </script>
@endpush

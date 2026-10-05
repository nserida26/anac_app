                <!-- Flight Crew Section -->
                @if (!$isDepouilleMortelle && !in_array($demandeAutorisation->first_type_vol_id, [12, 13]))
                    <div class="card">
                        <x-anac-card-header icon="fas fa-users" :title="__('trans.flight_crew')" />
                        <div class="card-body">
                            <form method="POST" id="crewForm" action="{{ url('/user/equipes') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">
                                {{--
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nom"><span class="text-danger">*</span>@lang('trans.last_name')</label>
                                                <input type="text" class="form-control" id="nom" name="nom"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="prenom"><span class="text-danger">*</span>@lang('trans.first_name')</label>
                                                <input type="text" class="form-control" id="prenom" name="prenom"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="age"><span class="text-danger">*</span>@lang('trans.age')</label>
                                                <input type="number" min="18" class="form-control"
                                                    id="age" name="age" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="fonction"><span class="text-danger">*</span>@lang('trans.role')</label>
                                                <select class="form-control" id="fonction" name="fonction" required>
                                                    <option value="pilot">@lang('trans.pilot')</option>
                                                    <option value="copilot">@lang('trans.copilot')</option>
                                                    <option value="mechanic">@lang('trans.mechanic')</option>
                                                    <option value="steward">@lang('trans.steward')</option>
                                                    <option value="hostess">@lang('trans.hostess')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="email"><span class="text-danger">*</span>@lang('trans.email')</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    required>
                                            </div>
                                        </div>

                                    </div>
                                --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fonction">@lang('trans.role')</label>
                                            <select class="form-control" id="fonction" name="fonction" required>
                                                <option value="pilot">@lang('trans.pilot')</option>
                                                <option value="copilot">@lang('trans.copilot')</option>
                                                <option value="mechanic">@lang('trans.mechanic')</option>
                                                <option value="steward">@lang('trans.steward')</option>
                                                <option value="hostess">@lang('trans.hostess')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="licence_numero">@lang('trans.license_number')</label>
                                            <input type="text" class="form-control" id="licence_numero"
                                                name="licence_numero">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="licence_expiration">@lang('trans.license_expiry')</label>
                                            <input type="date" class="form-control" id="licence_expiration"
                                                name="licence_expiration">
                                        </div>
                                    </div>
                                    <!--justificatif-->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="justificatif">@lang('trans.proof')</label>
                                            <input type="file" class="form-control" id="justificatif"
                                                name="justificatif">
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitCrew" type="submit" class="anac-btn anac-btn--primary float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($equipe_vols->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="crewTable">
                                                <thead>
                                                    <tr>
                                                        {{-- <th>@lang('trans.name')</th> --}}
                                                        <th>@lang('trans.role')</th>
                                                        {{-- <th>@lang('trans.age')</th> --}}
                                                        {{-- <th>@lang('trans.email')</th> --}}
                                                        <th>@lang('trans.license')</th>
                                                        <th>@lang('trans.proof')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($equipe_vols as $membre)
                                                        <tr id="membre-{{ $membre->id }}">
                                                            {{-- <td>{{ $membre->prenom }} {{ $membre->nom }}</td>
                                                        <td>{{ strtoupper($membre->fonction) }}</td>
                                                        <td>{{ $membre->age }}</td>
                                                        <td>{{ $membre->email }}</td> --}}
                                                            <td>{{ strtoupper($membre->fonction) }}</td>
                                                            <td>
                                                                @if ($membre->licence_numero)
                                                                    {{ $membre->licence_numero }}
                                                                    @if (!empty($membre->licence_expiration))
                                                                        ({{ date('d/m/Y', strtotime($membre->licence_expiration)) }})
                                                                    @endif
                                                                @else
                                                                    @lang('trans.not_available')
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($membre->justificatif)
                                                                    <a href="{{ asset('/uploads/' . $membre->justificatif) }}"
                                                                        target="_blank" class="btn btn-sm anac-action anac-action--blue">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    @lang('trans.not_available')
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="anac-actions">
                                                                    <button class="btn btn-sm anac-action anac-action--yellow edit-membre"
                                                                        data-id="{{ $membre->id }}">@lang('trans.update')</button>
                                                                    <button class="btn btn-sm anac-action anac-action--red delete-membre"
                                                                        data-id="{{ $membre->id }}">@lang('trans.delete')</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-membre-{{ $membre->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updateCrewForm-{{ $membre->id }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf

                                                                    <input type="hidden" name="membre_id" id="membre_id"
                                                                        value="{{ $membre->id }}">

                                                                    <div class="row">
                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.role')</label>
                                                                                <select class="form-control"
                                                                                    name="fonction" required>
                                                                                    @foreach (['pilot', 'copilot', 'mechanic', 'steward', 'hostess'] as $role)
                                                                                        <option
                                                                                            value="{{ $role }}"
                                                                                            {{ $membre->fonction == $role ? 'selected' : '' }}>
                                                                                            @lang('trans.' . $role)
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.license_number')</label>
                                                                                <input type="text" class="form-control"
                                                                                    name="licence_numero"
                                                                                    value="{{ $membre->licence_numero }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.license_expiry')</label>
                                                                                <input type="date" class="form-control"
                                                                                    name="licence_expiration"
                                                                                    value="{{ $membre->licence_expiration ? $membre->licence_expiration->format('Y-m-d') : '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label
                                                                                    for="justificatif">@lang('trans.proof')</label>
                                                                                <input type="file" class="form-control"
                                                                                    id="justificatif" name="justificatif">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit"
                                                                        class="btn btn-sm anac-action anac-action--blue update-membre">
                                                                        @lang('trans.update')
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm anac-action anac-action--grey cancel-edit"
                                                                        data-id="{{ $membre->id }}" data-type="membre">
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
            // Handle crew member operations
            $('#crewForm').submit(function(e) {
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
                        toastr.error(xhr.responseJSON?.message || @json(__('trans.error_occurred')));
                    }
                });
            });

            // Handle form submission
            $(document).on('submit', '[id^="updateCrewForm-"]', function(e) {
                e.preventDefault();

                var form = $(this);
                var formData = new FormData(this);
                var id = form.find('input[name="membre_id"]').val();

                $.ajax({
                    url: `/user/equipes/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Refresh or update the row
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || @json(__('trans.error_occurred')));
                    }
                });
            });

            $(document).on('click', '.delete-membre', function() {
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
                            url: `/user/equipes/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#membre-${id}, #edit-form-membre-${id}`).remove();
                                toastr.success(@json(__('trans.deleted_success')));
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

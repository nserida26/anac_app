                @if ($demandeAutorisation->first_type_vol_id == 1)
                    <!-- Freight Section -->
                    <div class="card">
                        <x-anac-card-header icon="fas fa-box" :title="__('trans.freight')" />
                        <div class="card-body">
                            <form method="POST" id="fretForm" action="{{ url('/user/frets') }}">
                                @csrf
                                <input type="hidden" value="{{ $demandeAutorisation->id }}"
                                    id="demande_autorisation_id" name="demande_autorisation_id">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nature">@lang('trans.nature')<span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" id="nature" name="nature" required>
                                                <option value="normal">@lang('trans.normal')</option>
                                                <option value="dangerous">@lang('trans.dangerous')</option>
                                                <option value="perishable">@lang('trans.perishable')</option>
                                                <option value="living">@lang('trans.living')</option>
                                                @if ($demandeAutorisation->type_demande_autorisation_id == 4)
                                                    <option value="depouille_mortelle" selected>
                                                        @lang('trans.depouille_mortelle')</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="poids">@lang('trans.weight_kg')</label>
                                            <input type="number" step="0.01" min="0" class="form-control"
                                                id="poids" name="poids">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="instructions_speciales">@lang('trans.special_instructions')</label>
                                            <textarea class="form-control" id="instructions_speciales" name="instructions_speciales" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <button id="submitFret" type="submit" class="anac-btn anac-btn--primary float-right">
                                            <i class="fas fa-plus"></i> @lang('trans.add')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if ($fretVols->isNotEmpty())
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="fretTable">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('trans.nature')</th>
                                                        <th>@lang('trans.weight_kg')</th>
                                                        <th>@lang('trans.description')</th>
                                                        <th>@lang('trans.actions')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fretVols as $fret)
                                                        <tr id="fret-{{ $fret->id }}">
                                                            <td>{{ strtoupper($fret->nature) }}</td>
                                                            <td>{{ $fret->poids }} @lang('trans.unit_kg')</td>
                                                            <td>{{ $fret->instructions_speciales }}</td>
                                                            <td>
                                                                <div class="anac-actions">
                                                                    <button class="btn btn-sm anac-action anac-action--yellow edit-fret"
                                                                        data-id="{{ $fret->id }}">@lang('trans.update')</button>
                                                                    <button class="btn btn-sm anac-action anac-action--red delete-fret"
                                                                        data-id="{{ $fret->id }}">@lang('trans.destroy')</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="edit-form-fret-{{ $fret->id }}"
                                                            style="display: none;">
                                                            <td colspan="6">
                                                                <form id="updateFretForm-{{ $fret->id }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="fret_id"
                                                                        value="{{ $fret->id }}">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.nature')</label>
                                                                                <select class="form-control"
                                                                                    name="nature" required>
                                                                                    <option value="normal"
                                                                                        {{ $fret->nature == 'normal' ? 'selected' : '' }}>
                                                                                        @lang('trans.normal')</option>
                                                                                    <option value="dangerous"
                                                                                        {{ $fret->nature == 'dangerous' ? 'selected' : '' }}>
                                                                                        @lang('trans.dangerous')</option>
                                                                                    <option value="perishable"
                                                                                        {{ $fret->nature == 'perishable' ? 'selected' : '' }}>
                                                                                        @lang('trans.perishable')</option>
                                                                                    <option value="living"
                                                                                        {{ $fret->nature == 'living' ? 'selected' : '' }}>
                                                                                        @lang('trans.living')</option>
                                                                                    @if ($demandeAutorisation->type_demande_autorisation_id == 4 || $fret->nature == 'depouille_mortelle')
                                                                                        <option value="depouille_mortelle"
                                                                                            {{ $fret->nature == 'depouille_mortelle' ? 'selected' : '' }}>
                                                                                            @lang('trans.depouille_mortelle')</option>
                                                                                    @endif
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.weight_kg')</label>
                                                                                <input type="number" step="0.01"
                                                                                    class="form-control" name="poids"
                                                                                    value="{{ $fret->poids }}">
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>@lang('trans.special_instructions')</label>
                                                                                <textarea class="form-control" name="instructions_speciales" rows="2">{{ $fret->instructions_speciales }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="btn btn-sm anac-action anac-action--blue update-fret"
                                                                        data-id="{{ $fret->id }}">@lang('trans.update')</button>
                                                                    <button type="button"
                                                                        class="btn btn-sm anac-action anac-action--grey cancel-edit"
                                                                        data-id="{{ $fret->id }}"
                                                                        data-type="fret">@lang('trans.cancel')</button>
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
            // Handle freight operations
            $('#fretForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {


                        location.reload();
                    },
                    error: function(xhr) {

                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', 'form[id^="updateFretForm-"]', function(e) {
                e.preventDefault();
                const formId = $(this).attr('id');
                const id = formId.split('-')[1];

                $.ajax({
                    url: `/user/frets/${id}`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.delete-fret', function() {
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
                            url: `/user/frets/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                $(`#fret-${id}, #edit-form-fret-${id}`).remove();
                                toastr.success(@json(__('trans.deleted_success')));
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

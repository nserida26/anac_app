@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('dashboard') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection
@push('css')
@endpush
@section('content')
    <div class="row">
        <!-- Bloc "profile" -->

        <div class="col-lg-12 order-lg-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mt-4">

                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>@lang('trans.signatory_name')</th>
                                        <td>
                                            @if (isset($signature->nom) && !empty($signature->nom))
                                                {{ $signature->nom }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>@lang('trans.signature')</th>
                                        <td>
                                            @if (isset($signature->signature))
                                                <img src="{{ asset('/uploads/' . $signature->signature) }}"
                                                    alt="User Signature" width="100">
                                                <button class="btn btn-danger btn-sm delete-btn" data-type="signature"
                                                    data-id="{{ $signature->id }}">
                                                    <i class="fas fa-trash"></i> @lang('trans.delete')
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>@lang('trans.stamp')</th>
                                        <td>
                                            @if (isset($cachet->cachet))
                                                <img src="{{ asset('/uploads/' . $cachet->cachet) }}" alt="User cachet"
                                                    width="100">
                                                <button class="btn btn-danger btn-sm delete-btn" data-type="cachet"
                                                    data-id="{{ $cachet->id }}">
                                                    <i class="fas fa-trash"></i> @lang('trans.delete')
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc pour le formulaire "My Account" -->
        @if (!isset($signature))
            <div class="col-lg-12 order-lg-1">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">@lang('trans.signatory_name') + @lang('trans.signature') + @lang('trans.stamp')</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.sc.store') }}" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="signatory_name">@lang('trans.signatory_name') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="signatory_name" name="signatory_name"
                                                value="{{ old('signatory_name') }}" required>
                                            @error('signatory_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="signature">@lang('trans.signature') <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="signature" name="signature"
                                                accept="image/*" onchange="previewSignature(event)" required>
                                            @error('signature')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group focused">
                                            <label class="form-control-label" for="cachet">@lang('trans.stamp') <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="cachet" name="cachet"
                                                accept="image/*" onchange="previewCachet(event)" required>
                                            @error('cachet')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-4">
                                        <img id="signaturePreview" src="" alt="Signature Preview"
                                            style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                    <div class="col-lg-4">
                                        <img id="cachetPreview" src="" alt="Cachet Preview"
                                            style="display: none; max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col text-center">
                                            <button type="submit" class="btn btn-primary float-right">Save</button>
                                        </div>
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
    <script>
        function previewSignature(event) {
            const input = event.target;
            const preview = document.getElementById('signaturePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewCachet(event) {
            const input = event.target;
            const preview = document.getElementById('cachetPreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Delete button functionality
            $('.delete-btn').click(function() {
                const type = $(this).data('type');
                const id = $(this).data('id');
                const button = $(this);

                if (confirm('Are you sure you want to delete this ' + type + '?')) {
                    $.ajax({
                        url: "{{ route('admin.sc.delete') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            type: type,
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                // Reload the page to reflect changes
                                location.reload();
                            } else {
                                alert('Error deleting ' + type);
                            }
                        },
                        error: function() {
                            alert('Error deleting ' + type);
                        }
                    });
                }
            });
        });
    </script>
@endpush

@push('custom')
@endpush
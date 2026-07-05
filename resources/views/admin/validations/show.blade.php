@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="{{ route('validations') }}">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection

@push('css')
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
    </style>
@endpush
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Validatio Details</span>

                    </div>
                    <div class="card-body">
                        @isset($validation)
                            <div class="row">
                                <!-- Profile Picture Column -->
                                <div class="col-md-3 text-center mb-4">
                                    <img src="{{ asset('/uploads/' . ($validation->photo ?? 'default.png')) }}"
                                        alt="Profile Picture" class="profile-picture rounded-circle mb-3">
                                    <h5 class="font-weight-bold">{{ $validation->np ?? '-' }}</h5>
                                    <p class="text-muted">Validation #{{ $validation->numero_licence ?? '-' }}</p>
                                </div>

                                <!-- Details Column -->
                                <div class="col-md-9">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">@lang('trans.category')</th>
                                                    <td>{{ $validation->categorie_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.machine')</th>
                                                    <td>{{ $validation->machine_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.type')</th>
                                                    <td>{{ $validation->type_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.license_number')</th>
                                                    <td class="font-weight-bold">{{ $validation->numero_licence ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.fl_name')</th>
                                                    <td>{{ $validation->np ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.dob')</th>
                                                    <td>{{ !empty($validation->date_naissance) ? date('d/m/Y', strtotime($validation->date_naissance)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.address')</th>
                                                    <td>{{ $validation->adresse ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.deliverance_date')</th>
                                                    <td>{{ !empty($validation->date_deliverance) ? date('d/m/Y', strtotime($validation->date_deliverance)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.update_date')</th>
                                                    <td>{{ !empty($validation->date_mise_a_jour) ? date('d/m/Y', strtotime($validation->date_mise_a_jour)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.expiration_date')</th>
                                                    <td
                                                        class="{{ \Carbon\Carbon::parse($validation->date_expiration)->isPast() ? 'text-danger' : 'text-success' }}">
                                                        {{ !empty($validation->date_expiration) ? date('d/m/Y', strtotime($validation->date_expiration)) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.stamp')</th>
                                                    <td class="text-center">
                                                        @if (isset($validation->cachet) && $validation->cachet != '')
                                                            <img src="{{ asset('/uploads/' . $validation->cachet) }}"
                                                                alt="Stamp" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.signature_dg')</th>
                                                    <td class="text-center">
                                                        @if (isset($validation->signature_dg) && $validation->signature_dg != '')
                                                            <img src="{{ asset('/uploads/' . $validation->signature_dg) }}"
                                                                alt="DG Signature" class="img-thumbnail signature-img">
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('trans.signature_dsv')</th>
                                                    <td class="text-center">
                                                        @if (isset($validation->signature_dsv) && $validation->signature_dsv != '')
                                                            <img src="{{ asset('/uploads/' . $validation->signature_dsv) }}"
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
                                No validation information found.
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')

@endpush

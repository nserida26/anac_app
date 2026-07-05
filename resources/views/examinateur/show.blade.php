@extends('examinateur.layouts.app')
@section('title')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheader')
    @lang('trans.dashboard_examiner')
@endsection
@section('contentheaderlink')
    <a href="{{ route('examinateur') }}">
        @lang('trans.dashboard_examiner') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_examiner')
@endsection
@push('css')
    <style>
        #documentViewer {
            width: 210mm;
            height: 297mm;
            max-width: 100%;
            /* Makes it responsive */
            display: block;
            margin: auto;
            /* Center horizontally */
        }
    </style>
@endpush
@section('content')

    <div class="container-fluid">
        <h1></h1>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        @lang('trans.examen')
                    </div>
                    <div class="card-body">
                        @isset($examen)
                            <div class="row justify-content-center">

                                <div class="col-lg-9">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>@lang('trans.fl_name')</th>
                                            <td>{{ $examen->demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.dob')</th>
                                            <td>{{ $examen->demandeur->date_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.lieu_naissance')</th>
                                            <td>{{ $examen->demandeur->lieu_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.address')</th>
                                            <td>{{ $examen->demandeur->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.exam_date')</th>
                                            <td>{{ $examen->date_examen ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.medical_fitness')</th>
                                            <td>{{ $examen->aptitude ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.report')</th>
                                            <td class="text-center">
                                                @if (isset($examen->rapport) && $examen->rapport != '')
                                                    <button class="btn btn-primary"
                                                        onclick="openPdfModal('{{ asset('/uploads/' . $examen->rapport) }}')"><i
                                                            class="fas fa-eye"></i></button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>@lang('trans.certificate')</th>
                                            <td class="text-center">
                                                @if (isset($examen->attestation) && $examen->attestation != '')
                                                    <button class="btn btn-primary"
                                                        onclick="openPdfModal('{{ asset('/uploads/' . $examen->attestation) }}')"><i
                                                            class="fas fa-eye"></i></button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>



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
@push('custom')
@endpush

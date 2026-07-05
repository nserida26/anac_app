@extends('centre.layouts.app')
@section('title')
    @lang('trans.dashboard_center')
@endsection
@section('contentheader')
    @lang('trans.dashboard_center')
@endsection
@section('contentheaderlink')
    <a href="{{ route('centre') }}">
        @lang('trans.dashboard_center') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_center')
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
                                            <th>@lang('trans.np')</th>
                                            <td>{{ $examen->demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.date_naissance')</th>
                                            <td>{{ $examen->demandeur->date_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.lieu_naissance')</th>
                                            <td>{{ $examen->demandeur->lieu_naissance ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.adresse')</th>
                                            <td>{{ $examen->demandeur->adresse ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.date_examen')</th>
                                            <td>{{ $examen->date_examen ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.aptitude')</th>
                                            <td>{{ $examen->aptitude ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.rapport')</th>
                                            <td>{!! $examen->rapport ?? '-' !!}</td>
                                        </tr>

                                        <tr>
                                            <th>@lang('trans.attestation')</th>
                                            <td class="text-center">
                                                @if (isset($examen->attestation) && $examen->attestation != '')
                                                    <iframe id="documentViewer"
                                                        src="{{ asset('/uploads/' . $examen->attestation) }}">
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

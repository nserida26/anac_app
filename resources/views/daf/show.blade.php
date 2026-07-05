@extends('daf.layouts.app')
@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheaderlink')
    <a href="{{ route('daf') }}">
        @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')]) </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
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
                        @lang('trans.paiement')
                    </div>
                    <div class="card-body">
                        @isset($paiement)
                            <div class="row justify-content-center">

                                <div class="col-lg-9">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>@lang('trans.ref')</th>
                                            <td>{{ $paiement->reference ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.application')</th>
                                            <td>{{ $paiement->demande->code ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.applicant')</th>
                                            <td>{{ $paiement->demande->demandeur->np ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.amount')</th>
                                            <td>{{ $paiement->montant ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.status')</th>
                                            <td>{{ $paiement->statut ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('trans.date')</th>
                                            <td>{{ $paiement->date_paiement ?? '-' }}</td>
                                        </tr>


                                        <tr>
                                            <th>@lang('trans.receipt')</th>
                                            <td class="text-center">
                                                @if (isset($paiement->quittance) && $paiement->quittance != '')
                                                    <button class="btn btn-primary"
                                                        onclick="openPdfModal('{{ asset('/uploads/' . $paiement->quittance) }}')"><i
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

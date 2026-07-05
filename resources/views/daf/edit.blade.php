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
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.update_invoice')</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('daf.update', $facture) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="demande_id" value="{{ $facture->demande->id }}">

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.date')</label>
                                <input type="date" name="date_facture" class="form-control"
                                    value="{{ $facture->date_facture }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.end_date')</label>
                                <input type="date" name="date_limite" class="form-control"
                                    value="{{ $facture->date_limite }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.amount')</label>
                                <input type="number" name="montant" class="form-control" value="{{ $facture->montant }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.invoice_signed')</label>
                                <input type="file" name="facture" class="form-control" accept="application/pdf" required>
                            </div>

                            <button type="submit" class="btn btn-success">@lang('trans.update')</button>
                        </form>
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

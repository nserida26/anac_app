@extends('user.layouts.app')
@section('title')
    @lang('trans.dashboard')
@endsection
@section('contentheader')
    @lang('trans.dashboard')
@endsection
@section('contentheaderlink')
    <a href="{{ route('user') }}">
        @lang('trans.dashboard') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard')
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
@endpush
@section('content')
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.payment')</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('update-state', $paiement->demande_autorisation_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="paiement_id" value="{{ $paiement->id }}">
                            <input type="hidden" name="action" value="compagnie_payer">
                            <input type="hidden" name="is_approved" value="1">
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.payment_date')</label>
                                <input type="date" name="date_paiement" class="form-control"
                                    value="{{ $paiement->date_paiement }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.method')</label>
                                <select name="methode" id="methode" class="form-control">
                                    <option value="">@lang('trans.select_payment_method')</option>
                                    <option value="credit_card">@lang('trans.credit_card')</option>
                                    <option value="paypal">@lang('trans.paypal')</option>
                                    <option value="bank_transfer">@lang('trans.bank_transfer')</option>
                                    <option value="cash">@lang('trans.cash')</option>
                                    <option value="check">@lang('trans.check')</option>
                                    <option value="mobile_payment">@lang('trans.mobile_payment')</option>
                                    <option value="cryptocurrency">@lang('trans.cryptocurrency')</option>
                                    <option value="direct_debit">@lang('trans.direct_debit')</option>
                                    <option value="wire_transfer">@lang('trans.wire_transfer')</option>
                                    <option value="other">@lang('trans.other')</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.proof')</label>
                                <input type="file" name="justificatif" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success">@lang('trans.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

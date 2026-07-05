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
                    <div class="card-header">Paiement</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('update-state', $paiement->demande_autorisation_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="paiement_id" value="{{ $paiement->id }}">
                            <input type="hidden" name="action" value="compagnie_payer">
                            <input type="hidden" name="is_approved" value="1">
                            <div class="mb-3">
                                <label class="form-label">Date paiement</label>
                                <input type="date" name="date_paiement" class="form-control"
                                    value="{{ $paiement->date_paiement }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.method')</label>
                                <select name="methode" id="methode" class="form-control">
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="mobile_payment">Mobile Payment (Apple Pay/Google Pay)</option>
                                    <option value="cryptocurrency">Cryptocurrency</option>
                                    <option value="direct_debit">Direct Debit</option>
                                    <option value="wire_transfer">Wire Transfer</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.proof')</label>
                                <input type="file" name="justificatif" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

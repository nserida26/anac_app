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
                    <div class="card-header">@lang('trans.paiement')</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('user.licences.update', $paiement) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="paiement_id" value="{{ $paiement->id }}">

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.date')</label>
                                <input type="date" name="date_paiement" class="form-control"
                                    value="{{ $paiement->date_paiement }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.receipt')(PDF)</label>
                                <input type="file" name="quittance" class="form-control" accept="application/pdf"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-success">@lang('trans.send')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
@endpush
@push('custom')
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200, // Set height of the editor
                placeholder: 'Enter your text...',

            });
        });
    </script>
@endpush

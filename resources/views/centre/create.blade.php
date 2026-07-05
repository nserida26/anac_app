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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.add_training')</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('centre.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="centre_formation_id" value="{{ $centre->id }}">
                            <input type="hidden" name="demandeur_id" value="{{ $demandeur->id }}">
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.training_type')</label>
                                <select class="form-control" name="type_formation_id">
                                    @foreach ($type_formations as $type_formation)
                                        <option value="{{ $type_formation->id }}">
                                            {{ $type_formation->nom }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.training_date')</label>
                                <input type="date" name="date_formation" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.location')</label>
                                <input type="text" class="form-control" id="lieu" name="lieu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.certificate')</label>
                                <input type="file" name="attestation" class="form-control" accept="application/pdf"
                                    required>
                            </div>


                            <button type="submit" class="btn btn-success">@lang('trans.save')</button>
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

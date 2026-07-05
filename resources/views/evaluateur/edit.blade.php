@extends('evaluateur.layouts.app')
@section('title')
    @lang('trans.dashboard_evaluator')
@endsection
@section('contentheader')
    @lang('trans.dashboard_evaluator')
@endsection
@section('contentheaderlink')
    <a href="{{ route('evaluateur') }}">
        @lang('trans.dashboard_evaluator') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_evaluator')
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@lang('trans.update_medical_fitness')</div>
                    <div class="card-body">



                        <!-- Formulaire -->
                        <form action="{{ route('evaluateur.update', $examen) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">@lang('trans.medical_fitness_by_evaluator')</label>
                                <input type="number" min="0" name="validite_evaluateur" class="form-control"
                                    value="{{ $examen->validite }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('trans.report_by_evaluator')</label>
                                <input type="file" name="rapport_evaluateur" class="form-control"
                                    accept="application/pdf" required>
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

@extends('dir.layouts.app')
@section('title')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheader')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@section('contentheaderlink')
    <a href="{{ route('dsv') }}">
        @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])</a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_dir', ['role' => strtoupper(auth()->user()->getRoleNames()->first() ?? '')])
@endsection
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Ajouter un Ordre de Recette</div>
                    <div class="card-body">
                        <!-- Formulaire -->
                        <form action="{{ route('dsv.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="demande_id" value="{{ $demande->id }}">

                            <div class="mb-3">
                                <label class="form-label">Date Ordre</label>
                                <input type="date" name="date_ordre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Montant</label>
                                <input type="number" name="montant" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ordre signe(PDF)</label>
                                <input type="file" name="ordre" class="form-control" accept="application/pdf" required>
                            </div>

                            <button type="submit" class="btn btn-success">Enregistrer</button>
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

@extends('layouts.admin')
@section('title')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheader')
    @lang('trans.dashboard_admin')
@endsection
@section('contentheaderlink')
    <a href="">
        @lang('trans.dashboard_admin') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.dashboard_admin')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Simulateur</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('simulateurs.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">

                        <h5 class="card-title">{{ $simulateur->libelle }}</h5>
                        <br>
                        <p class="card-text">
                            <strong>Type d'avion:</strong> {{ $simulateur->typeAvion->code ?? 'N/A' }}<br>
                            <strong>Compagnie:</strong> {{ $simulateur->compagnie }}<br>
                            <strong>Date délivrance initiale:</strong>
                            {{ $simulateur->date_delivrance_initiale ? $simulateur->date_delivrance_initiale->format('d/m/Y') : 'N/A' }}<br>
                            <strong>Date renouvellement:</strong>
                            {{ $simulateur->date_renouvellement ? $simulateur->date_renouvellement->format('d/m/Y') : 'N/A' }}<br>
                            <strong>Date expiration:</strong>
                            {{ $simulateur->date_expiration ? $simulateur->date_expiration->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('simulateurs.edit', $simulateur) }}" class="btn btn-warning">Modifier</a>
                    <form action="{{ route('simulateurs.destroy', $simulateur) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                    </form>
                    <a href="{{ route('simulateurs.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>

            </div>

        </div>
    </section>
@endsection

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
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Simulateur') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('simulateurs.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Libellé</th>
                                        <th>Type d'avion</th>
                                        <th>Compagnie</th>
                                        <th>Date expiration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($simulateurs as $simulateur)
                                        <tr>
                                            <td>{{ $simulateur->id }}</td>
                                            <td>{{ $simulateur->libelle }}</td>
                                            <td>{{ $simulateur->typeAvion->code ?? 'N/A' }}</td>
                                            <td>{{ $simulateur->compagnie }}</td>
                                            <td>{{ $simulateur->date_expiration ? $simulateur->date_expiration->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('simulateurs.show', $simulateur) }}"
                                                    class="btn btn-info btn-sm">Voir</a>
                                                <a href="{{ route('simulateurs.edit', $simulateur) }}"
                                                    class="btn btn-warning btn-sm">Modifier</a>
                                                <form action="{{ route('simulateurs.destroy', $simulateur) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Êtes-vous sûr?')">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

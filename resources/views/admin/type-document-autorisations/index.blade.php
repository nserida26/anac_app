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
                                {{ __('Type Document Autorisation') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('type-document-autorisations.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>

                                        <th>Type Vol</th>
                                        <th>Type Demande</th>
                                        <th>Nom Fr</th>
                                        <th>Nom En</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($typeDocumentAutorisations as $typeDocumentAutorisation)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ optional($typeDocumentAutorisation->typeVol)->nom }}</td>
                                            <td>{{ optional($typeDocumentAutorisation->typeDemande)->libelle }}</td>
                                            <td>{{ $typeDocumentAutorisation->nom_fr }}</td>
                                            <td>{{ $typeDocumentAutorisation->nom_en }}</td>

                                            <td>
                                                <form action="{{ route('type-document-autorisations.destroy', $typeDocumentAutorisation->id) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary "
                                                        href="{{ route('type-document-autorisations.show', $typeDocumentAutorisation->id) }}"><i
                                                            class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('type-document-autorisations.edit', $typeDocumentAutorisation->id) }}"><i
                                                            class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"><i
                                                            class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $typeDocumentAutorisations->links() !!}
            </div>
        </div>
    </div>
@endsection

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

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Checklist Details</h3>
                    <div class="float-right">
                        <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">ID</th>
                            <td>{{ $checklist->id }}</td>
                        </tr>
                        <tr>
                            <th>Number</th>
                            <td>{{ $checklist->numero }}</td>
                        </tr>
                        <tr>
                            <th>Index</th>
                            <td>{{ $checklist->index ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ $checklist->type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Label</th>
                            <td>{{ $checklist->libelle }}</td>
                        </tr>
                        <tr>
                            <th>Section</th>
                            <td>{{ $checklist->section ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Order</th>
                            <td>{{ $checklist->ordre ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Type Demande</th>
                            <td>{{ $checklist->typeDemande->nom_fr ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Type Licence</th>
                            <td>{{ $checklist->typeLicence->nom ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $checklist->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $checklist->updated_at }}</td>
                        </tr>
                    </table>
                    
                    @if($checklist->demandeChecklists->count() > 0)
                    <div class="mt-4">
                        <h5>Associated Demandes</h5>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Demande ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checklist->demandeChecklists as $demandeChecklist)
                                <tr>
                                    <td>{{ $demandeChecklist->id }}</td>
                                    <td>{{ $demandeChecklist->demande_id }}</td>
                                    <td>{{ $demandeChecklist->status ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
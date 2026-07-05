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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Checklists Management</h3>
                    <div class="float-right">
                        <a href="{{ route('checklists.create') }}" class="btn btn-primary">Create New Checklist</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="GET" action="{{ route('checklists.index') }}" class="mb-3">
                        <div class="row">
                            
                            <div class="col-md-4">
                                <select name="type_demande" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Type Demandes</option>
                                    @foreach($typeDemandes as $typeDemande)
                                        <option value="{{ $typeDemande->id }}" {{ request('type_demande') == $typeDemande->id ? 'selected' : '' }}>
                                            {{ $typeDemande->nom_fr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="type_licence" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Type Licences</option>
                                    @foreach($typeLicences as $typeLicence)
                                        <option value="{{ $typeLicence->id }}" {{ request('type_licence') == $typeLicence->id ? 'selected' : '' }}>
                                            {{ $typeLicence->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                @if(request('search') || request('type_demande') || request('type_licence'))
                                    <a href="{{ route('checklists.index') }}" class="btn btn-secondary btn-block">Clear All Filters</a>
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Number</th>
                                    <th>Label</th>
                                    <th>Section</th>
                                    <th>Order</th>
                                    <th>Type Demande</th>
                                    <th>Type Licence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checklists as $checklist)
                                <tr>
                                    <td>{{ $checklist->id }}</td>
                                    <td>{{ $checklist->numero }}</td>
                                    <td>{{ Str::limit($checklist->libelle, 50) }}</td>
                                    <td>{{ $checklist->section }}</td>
                                    <td>{{ $checklist->ordre }}</td>
                                    <td>{{ $checklist->typeDemande->nom_fr ?? 'N/A' }}</td>
                                    <td>{{ $checklist->typeLicence->nom ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('checklists.show', $checklist) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('checklists.destroy', $checklist) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No checklists found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $checklists->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
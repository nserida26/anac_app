{{-- resources/views/admin/immatriculation-blacklists/index.blade.php --}}
@extends('layouts.admin')

@section('title')
    @lang('trans.immatriculation_blacklists')
@endsection

@section('contentheader')
    @lang('trans.immatriculation_blacklists')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-ban mr-2"></i> @lang('trans.add_blacklist_entry')</h3>
                </div>
                <form action="{{ route('admin.immatriculation-blacklists.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pattern">@lang('trans.immatriculation_pattern') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="pattern" name="pattern"
                                           placeholder="4X" maxlength="20" required value="{{ old('pattern') }}">
                                    <small class="form-text text-muted">@lang('trans.immatriculation_pattern_help')</small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="motif">@lang('trans.motif')</label>
                                    <input type="text" class="form-control" id="motif" name="motif" maxlength="255" value="{{ old('motif') }}">
                                </div>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> @lang('trans.add')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-2"></i> @lang('trans.blacklisted_entries')</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>@lang('trans.immatriculation_pattern')</th>
                                <th>@lang('trans.motif')</th>
                                <th>@lang('trans.added_by')</th>
                                <th>@lang('trans.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                            <tr>
                                <td><span class="badge badge-danger">{{ $entry->pattern }}</span></td>
                                <td>{{ $entry->motif ?: '—' }}</td>
                                <td>{{ optional($entry->creePar)->email ?: '—' }}</td>
                                <td>
                                    <form action="{{ route('admin.immatriculation-blacklists.destroy', $entry) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm(@json(__('trans.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="@lang('trans.delete')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted p-3">@lang('trans.no_blacklist_entries')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

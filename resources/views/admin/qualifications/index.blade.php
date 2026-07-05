@extends('layouts.admin')
@section('title')
    @lang('trans.qualifications')
@endsection
@section('contentheader')
    @lang('trans.qualifications')
@endsection
@section('contentheaderlink')
    <a href="{{ route('qualifications.index') }}">
        @lang('trans.qualifications') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.qualifications')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Qualification') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('qualifications.create') }}" class="btn btn-primary btn-sm float-right"
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

                                        <th>Libelle</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($qualifications as $qualification)
                                        <tr>
                                            <td>{{ ++$i }}</td>

                                            <td>{{ $qualification->libelle }}</td>

                                            <td>
                                                <form action="{{ route('qualifications.destroy', $qualification->id) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary "
                                                        href="{{ route('qualifications.show', $qualification->id) }}"><i
                                                            class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('qualifications.edit', $qualification->id) }}"><i
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
                {!! $qualifications->links() !!}
            </div>
        </div>
    </div>
@endsection

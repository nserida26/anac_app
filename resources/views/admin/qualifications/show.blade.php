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
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Qualification</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('qualifications.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <strong>Libelle:</strong>
                            {{ $qualification->libelle }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

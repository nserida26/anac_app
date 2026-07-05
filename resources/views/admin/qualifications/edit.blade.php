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
        <div class="">
            <div class="col-md-12">

                @includeif('partials.errors')

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Qualification</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('qualifications.update', $qualification->id) }}" role="form"
                            enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('admin.qualifications.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

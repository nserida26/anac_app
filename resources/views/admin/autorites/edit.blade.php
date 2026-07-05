@extends('layouts.admin')

@section('template_title')
    {{ __('Update') }} Autorite
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Autorite</span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('autorites.update', $autorite->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('admin.autorites.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

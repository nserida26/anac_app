@extends('layouts.admin')
@section('title')
    @lang('trans.users')
@endsection
@section('contentheader')
    @lang('trans.users')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.users.index') }}"> @lang('trans.users') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.users')
@endsection
@push('css')
@endpush
@section('content')
    <div class="row">
        <!-- /.col -->
        <div class="col-md-10 col-sm-offset-1">
            <div class="card">
                <div class="card-header">User Informations</div>
                <div class="card-body">


                    <form class="form-horizontal">

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                        </div>

                        <!-- checkbox -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">User Roles</label>
                            <div class="col-sm-10">

                                @php
                                    $userRoles = DB::table('role_user')->where('user_id', '=', $user->id)->get();
                                    $userRolesArray = [];

                                    foreach ($userRoles as $userRole) {
                                        array_push($userRolesArray, $userRole->role_id);
                                    }
                                @endphp

                                <ul style="list-stule:disc;">
                                    @foreach ($roles as $role)
                                        <li>{{ $role->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">

                            <label for="image" class="col-sm-2 control-label">Profile Picture</label>

                            <div class="col-sm-10">
                                <img src="{{ asset('assets/admin/imgs/' . $user->photo) }}" id="photo"
                                    style="margin-bottom: 8px;height:100px;border-radius:5px">
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-primary"
                                    style="margin-top:10px">{{ __('Back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
@endsection

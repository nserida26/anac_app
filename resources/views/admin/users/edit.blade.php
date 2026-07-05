@extends('layouts.admin')
@section('title')
    @lang('trans.users')
@endsection
@section('contentheader')
    @lang('trans.users')
@endsection
@section('contentheaderlink')
    <a href="{{ route('users.index') }}"> @lang('trans.users') </a>
@endsection
@section('contentheaderactive')
    @lang('trans.users')
@endsection
@push('css')
@endpush
@section('content')
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row">


                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Edit User</div>
                        <div class="card-body">

                            <form action="{{ route('users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ $user->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="roles">Roles</label>
                                    <select name="roles[]" id="roles" class="form-control" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                @if (in_array($role->name, $userRoles)) selected @endif>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo').attr('src', e.target.result).height(80).width(80);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#image").change(function() {
            readURL(this);
        });
    </script>
@endpush

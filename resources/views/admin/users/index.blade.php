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
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <a href="{{ route('users.create') }}" class="btn btn-success btn-sm mb-3" title="Create User">
                            <i class="fa fa-plus" aria-hidden="true"></i> Create User
                        </a>
                        <div class="table-responsive">




                            <table class="table" id="datatable">
                                <thead>
                                    <tr>

                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>@lang('trans.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>

                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                            <td>
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $('#datatable').DataTable();
        $(document).on('click', '.delete', function(e) {

            var form = $(this).parents('form:first');

            var confirmed = false;

            e.preventDefault();

            swal({
                title: 'Are you sure want to delete?',
                text: "Onec Delete, This will be permanently delete!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willDelete) => {
                if (willDelete) {
                    // window.location.href = link;
                    confirmed = true;

                    form.submit();

                } else {
                    swal("Safe Data!");
                }
            });
        });
    </script>
@endpush

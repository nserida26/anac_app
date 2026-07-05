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
    <div class="row">


        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><a href="{{ route('roles.create') }}" class="btn btn-success btn-sm mb-3"
                        title="Create Role">
                        <i class="fa fa-plus" aria-hidden="true"></i> Create Role
                    </a></div>

                <div class="card-body">

                    <ul>
                        @foreach ($roles as $role)
                            <li>
                                {{ $role->name }}

                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
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

@extends('user.layouts.app')
@section('title')
    @lang('trans.dashboard')
@endsection
@section('contentheader')
    @lang('trans.dashboard')
@endsection
@section('contentheaderlink')
    <a href="{{ route('user') }}">
        @lang('trans.dashboard')
    </a>
@endsection

@section('content')
    <div class="container-fluid">
        <h2>Pending Login Requests</h2>

        @foreach ($requests as $request)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Request from {{ $request->compagnieUser->compagnie->name }}</h5>
                    <p>Requested at: {{ date('M d, Y H:i', strtotime($request->created_at)) }} </p>
                    <p>Expires at: {{ date('M d, Y H:i', strtotime($request->expires_at)) }}</p>

                    <form method="POST" action="{{ route('user.approve.login', $request) }}">
                        @csrf
                        <button type="submit" class="btn btn-success">Approve</button>
                        <a href="#" class="btn btn-danger">Deny</a>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection

{{-- resources/views/centre_medical/examens/create.blade.php --}}
@extends('centre.layouts.app')

@section('title')
    @lang('trans.nouveau_rapport_medical')
@endsection

@section('contentheader')
    @lang('trans.nouveau_rapport_medical')
@endsection

@section('contentheaderlink')
    <a href="{{ route('centre_medical.examens') }}">@lang('trans.rapports_medicaux')</a>
@endsection

@section('contentheaderactive')
    @lang('trans.nouveau_rapport_medical')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    @include('examens_medicaux.partials.formulaire', [
                        'action' => route('centre_medical.examens.store'),
                        'demandeurs' => $demandeurs,
                        'examinateurs' => $examinateurs,
                        'annulation' => route('centre_medical.examens'),
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(function () {
        $('#demandeur_id').select2({ width: '100%' });
    });
</script>
@endpush

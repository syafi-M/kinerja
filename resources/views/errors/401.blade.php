@extends('errors::minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized'))
@section('solusi')
    <div class="btn btn-primary btn-xs sm:btn-md font-bold">
        <a href="{{ route('dashboard.index')}}">KEMBALI KE DASHBOARD</a>
    </div>
@endsection

@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('solusi')
    <div class="btn btn-primary btn-xs sm:btn-md font-bold">
        <a href="{{ route('dashboard.index')}}">KEMBALI KE DASHBOARD</a>
    </div>
@endsection
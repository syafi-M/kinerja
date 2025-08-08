@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('solusi')
    <a href="{{ route('login') }}" class="btn btn-primary btn-xs sm:btn-md font-bold">
        <span>KEMBALI KE LOGIN</span>
    </a>
@endsection

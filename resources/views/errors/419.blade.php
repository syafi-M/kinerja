@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('solusi')
    <div class="btn btn-primary btn-xs sm:btn-md font-bold">
        <a href="{{ route('login')}}">KEMBALI KE LOGIN</a>
    </div>
@endsection

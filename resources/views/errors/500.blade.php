@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
  <div class="flex flex-col">
      <span class="text-center font-bold text-gray-100 text-lg md:text-xl lg:text-2xl">KESALAHAN SERVER</span>
      <p class="text-center text-sm">Terjadi Masalah Di Dalam Server Tunggu Beberapa Saat</p>
  </div>
@endsection
@section('icon')
<div>
    <img src="https://www.svgrepo.com/show/426192/cogs-settings.svg" alt="Logo" class="mb-8 h-40">
</div>
@endsection
@section('solusi')
    <div class="btn btn-primary btn-xs sm:btn-md font-bold">
        <a href="{{ route('dashboard.index')}}">KEMBALI KE DASHBOARD</a>
    </div>
@endsection

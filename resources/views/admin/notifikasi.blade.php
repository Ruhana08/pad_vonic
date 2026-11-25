@extends('layouts.admin')

@section('title', 'Notifikasi - Admin Vonic')

@section('content')
  <div class="mx-auto max-w-[1100px]">
    <x-notifikasi-page :backUrl="route('admin.dashboard')" />
  </div>
@endsection

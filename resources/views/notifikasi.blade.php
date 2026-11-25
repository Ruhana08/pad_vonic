@extends('layouts.user')

@section('title', 'Notifikasi - Vonic')

@section('content')
  <div class="mx-auto max-w-[1000px]">
    <x-notifikasi-page :backUrl="route('home')" />
  </div>
@endsection

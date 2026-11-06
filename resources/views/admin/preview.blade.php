@extends('layouts.admin')
@section('title', 'Preview Komponen')

@section('content')
  <x-card class="p-6">
    <x-section-header title="Ringkasan Hasil Pemungutan"
                      subtitle="Pantau perolehan suara dari setiap kategori pemungutan yang sedang berlangsung." />

    <div class="mt-6 grid gap-4 md:grid-cols-2">
      <x-card variant="elevated" class="p-5">
        <div class="flex items-center justify-between">
          <span class="text-sm">Dosen dan Tenaga Didik dipisah</span>
          <x-badge>58%</x-badge>
        </div>
        <x-progress :value="58" class="mt-3" />
      </x-card>

      <x-card variant="elevated" class="p-5">
        <div class="flex items-center justify-between">
          <span class="text-sm">Fully In-Person Only</span>
          <span class="text-sm font-semibold">42%</span>
        </div>
        <x-progress :value="42" tone="soft" class="mt-3" />
      </x-card>
    </div>

    <div class="mt-6 flex items-center gap-3">
      <x-avatar name="Melisa Danuar, S.Kom" />
      <div>
        <p class="text-sm font-semibold">Melisa Danuar, S.Kom</p>
        <x-status>Mengirimkan vote</x-status>
      </div>
    </div>
  </x-card>
@endsection
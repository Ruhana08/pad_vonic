<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Test Vonic</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 p-10 font-sans text-black">
  <h1 class="mb-6 text-3xl font-bold text-secondary-1">Token Vonic</h1>

  <div class="flex gap-4">
    <div class="h-20 w-20 rounded-md bg-primary-1"></div>
    <div class="h-20 w-20 rounded-md bg-primary-2"></div>
    <div class="h-20 w-20 rounded-md bg-primary-3"></div>
    <div class="h-20 w-20 rounded-md bg-primary-4"></div>
    <div class="h-20 w-20 rounded-md bg-secondary-1"></div>
  </div>

  <div class="mt-8 flex gap-6">
    <div class="h-24 w-24 rounded-full bg-white shadow-type-1"></div>
    <div class="h-24 w-24 rounded-full bg-white shadow-type-2"></div>
    <div class="h-24 w-24 rounded-full bg-white shadow-type-3"></div>
  </div>

  <div class="mt-8 flex flex-wrap items-center gap-4">
  <x-button>Simpan</x-button>
  <x-button variant="secondary">Batal</x-button>
  <x-button variant="ghost">Lihat Detail</x-button>
  <x-button disabled>Nonaktif</x-button>
</div>

<div class="mt-4 flex flex-wrap items-center gap-4">
  <x-button size="sm">Small</x-button>
  <x-button size="md">Medium</x-button>
  <x-button size="lg">Large</x-button>
</div>

</body>
</html>
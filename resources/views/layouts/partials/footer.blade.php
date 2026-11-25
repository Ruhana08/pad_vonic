<footer class="mt-auto flex h-[64px] items-center justify-between border-t border-grey-1 bg-white px-4 sm:px-8">
  
  <!-- Sisi Kiri: Logo Vonic + Teks "Vonic" (Font Skranji) -->
  <div class="flex items-center gap-2.5 sm:gap-3">
    <img src="{{ asset('images/logo-vonic-b.webp') }}" 
         alt="Vonic" 
         class="h-6 sm:h-7 w-auto" 
         onError="this.style.display='none'">
         
    <span class="font-sans text-lg sm:text-xl font-bold text-primary-1">Vonic</span>
  </div>

  <!-- Sisi Kanan: Teks Copyright -->
  <div class="text-xs sm:text-sm font-semibold text-primary-1">
    &copy; {{ date('Y') }} DTEDI SV UGM
  </div>

</footer>
@props(['path', 'placeholder' => 'Tulis di sini...'])

<div class="overflow-hidden rounded-sm bg-white shadow-type-3"
     x-init="if (typeof mountQuill === 'function') { mountQuill($el.querySelector('[data-editor]'), {{ $path }}, h => {{ $path }} = h); } else { setTimeout(() => { window.initPollQuill && window.initPollQuill(); }, 50); }">
  <div data-editor data-path="{{ $path }}" data-placeholder="{{ $placeholder }}"></div>
</div>

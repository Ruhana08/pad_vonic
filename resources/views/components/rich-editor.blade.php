@props(['path', 'placeholder' => 'Tulis di sini...'])

<div class="overflow-hidden rounded-sm bg-white shadow-type-3"
     x-init="mountQuill($el.querySelector('[data-editor]'), {{ $path }}, h => {{ $path }} = h)">
  <div data-editor data-placeholder="{{ $placeholder }}"></div>
</div>
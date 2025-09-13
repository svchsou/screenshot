@extends('layouts.app')

@section('content')
  <div class="mui-row">
    <!-- Left ad -->
    <div class="mui-col-md-3 mui-col-sm-12">
      @include('partials.ads', ['slot' => env('ADSENSE_SLOT_LEFT')])
    </div>

    <!-- Upload card -->
    <div class="mui-col-md-6 mui-col-sm-12">
      <div class="mui-panel">
        <h1 class="mui--text-headline" style="margin-top:0">Upload Screenshot</h1>
        <form id="uploadForm" method="POST" action="{{ route('upload.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="mui-textfield" style="width:100%">
            <label>Select image</label>
            <input id="fileInput" type="file" name="image" accept="{{ implode(',', $allowed) }}" required>
          </div>
          <div id="dropzone" class="dropzone mui--z1" tabindex="0" role="button" aria-label="Dropzone">
            Drag & drop or paste image here
          </div>
          <div class="mui--text-caption mui--text-dark-secondary" style="margin-top:8px;">
            Allowed: {{ implode(', ', $allowed) }}. Max size: {{ $maxMb }}MB.
          </div>
          @error('image')
            <div class="mui--text-danger mui--text-body2">{{ $message }}</div>
          @enderror
          <div style="margin-top:16px">
            <button type="submit" class="mui-btn mui-btn--raised mui-btn--primary">Upload</button>
          </div>
        </form>
        <div id="shortUrlBox" class="mui--hide" style="margin-top:12px;">
          <div class="mui-textfield" style="width:100%">
            <input id="shortUrl" type="text" readonly value="">
          </div>
          <button onclick="copyShortUrl()" class="mui-btn">Copy URL</button>
        </div>

        <!-- Bottom ad under the form -->
        @include('partials.ads', ['slot' => env('ADSENSE_SLOT_BOTTOM')])
      </div>
    </div>

    <!-- Right ad -->
    <div class="mui-col-md-3 mui-col-sm-12">
      @include('partials.ads', ['slot' => env('ADSENSE_SLOT_RIGHT')])
    </div>
  </div>

  <script>
  // Drag-and-drop and paste support
  (function(){
    const dz = document.getElementById('dropzone');
    const fi = document.getElementById('fileInput');
    dz.addEventListener('click', () => fi.click());
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
    dz.addEventListener('dragleave', e => { e.preventDefault(); dz.classList.remove('dragover'); });
    dz.addEventListener('drop', e => {
      e.preventDefault(); dz.classList.remove('dragover');
      if (e.dataTransfer.files.length) fi.files = e.dataTransfer.files;
    });
    document.addEventListener('paste', e => {
      if (e.clipboardData.files.length) fi.files = e.clipboardData.files;
    });
  })();
  function copyShortUrl(){
    const input = document.getElementById('shortUrl');
    input.select(); document.execCommand('copy');
  }
  </script>
@endsection

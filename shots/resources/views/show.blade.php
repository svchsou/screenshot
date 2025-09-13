@extends('layouts.app')

@section('content')
  <meta property="og:image" content="{{ $imageUrl }}" />
  <meta property="og:title" content="Screenshot" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url()->current() }}" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:image" content="{{ $imageUrl }}" />

  @include('partials.ads', ['slot' => env('ADSENSE_SLOT_TOP')])

  <div class="mui-panel" style="max-width:760px; margin:0 auto;">
    <div style="text-align:center; margin-bottom:16px;">
      <a href="{{ $imageUrl }}" target="_blank">
        <img src="{{ $thumbUrl }}" alt="Screenshot" class="thumb" loading="lazy">
      </a>
    </div>

    <div class="mui--text-body2 mui--text-dark-secondary" style="margin-bottom:8px;">
      <span>Type: {{ $shot->mime }}</span> |
      <span>Size: {{ number_format($shot->size_bytes / 1024, 1) }} KB</span> |
      <span>Dimensions: {{ $shot->width }}x{{ $shot->height }}</span> |
      <span>Views: {{ $shot->views_count }}</span>
    </div>

    <div class="mui-row" style="gap:8px; align-items:center;">
      <div class="mui-col-xs-12 mui-col-md-9">
        <div class="mui-textfield" style="width:100%; margin-bottom:0;">
          <input id="shortUrl" type="text" readonly value="{{ url()->current() }}" onclick="this.select()">
        </div>
      </div>
      <div class="mui-col-xs-6 mui-col-md-2">
        <button onclick="copyShortUrl()" class="mui-btn mui-btn--flat">Copy URL</button>
      </div>
      @if($deleteToken)
      <div class="mui-col-xs-6 mui-col-md-1" style="text-align:right;">
        <form method="POST" action="{{ route('screenshots.delete', $shot->slug) }}">
          @csrf
          <input type="hidden" name="delete_token" value="{{ $deleteToken }}">
          <button type="submit" class="mui-btn mui-btn--danger">Delete</button>
        </form>
      </div>
      @endif
    </div>

    <div class="mui--text-caption mui--text-dark-hint" style="margin-top:16px;">
      <a href="#">Terms of Service</a> · <a href="#">DMCA</a>
    </div>
  </div>

  @include('partials.ads', ['slot' => env('ADSENSE_SLOT_MID')])

  <script>
  function copyShortUrl(){
    const input = document.getElementById('shortUrl');
    input.select(); document.execCommand('copy');
  }
  </script>
@endsection


@if(env('ADS_ENABLED') && env('ADSENSE_CLIENT_ID'))
  <div class="mui--text-center" style="margin:12px 0;">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="{{ env('ADSENSE_CLIENT_ID') }}"
         data-ad-slot="{{ $slot ?? env('ADSENSE_SLOT_TOP') }}"
         data-ad-format="auto"
         data-full-width-responsive="true"
         @if(env('ADSENSE_TEST_MODE')) data-adtest="on" @endif></ins>
  </div>
  <script>(adsbygoogle=window.adsbygoogle||[]).push({});</script>
@endif

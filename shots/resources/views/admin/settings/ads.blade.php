@extends('layouts.app')

@section('content')
  <div class="mui-panel" style="max-width:720px; margin:0 auto;">
    <h1 class="mui--text-headline" style="margin-top:0">AdSense Settings</h1>
    @if(session('status'))
      <div class="mui--text-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.settings.ads.save') }}">
      @csrf
      <div class="mui-checkbox">
        <label>
          <input type="checkbox" name="ADS_ENABLED" value="1" @if($data['ADS_ENABLED']) checked @endif> Enable Ads
        </label>
      </div>
      <div class="mui-textfield"><label>AdSense Client ID</label>
        <input type="text" name="ADSENSE_CLIENT_ID" value="{{ $data['ADSENSE_CLIENT_ID'] }}">
      </div>
      <div class="mui-row">
        <div class="mui-col-sm-4"><div class="mui-textfield"><label>Left Slot ID</label>
          <input type="text" name="ADSENSE_SLOT_LEFT" value="{{ $data['ADSENSE_SLOT_LEFT'] }}">
        </div></div>
        <div class="mui-col-sm-4"><div class="mui-textfield"><label>Right Slot ID</label>
          <input type="text" name="ADSENSE_SLOT_RIGHT" value="{{ $data['ADSENSE_SLOT_RIGHT'] }}">
        </div></div>
        <div class="mui-col-sm-4"><div class="mui-textfield"><label>Bottom Slot ID</label>
          <input type="text" name="ADSENSE_SLOT_BOTTOM" value="{{ $data['ADSENSE_SLOT_BOTTOM'] }}">
        </div></div>
      </div>
      <div class="mui-checkbox"><label>
        <input type="checkbox" name="ADSENSE_TEST_MODE" value="1" @if($data['ADSENSE_TEST_MODE']) checked @endif> Enable Test Ads (data-adtest="on")
      </label></div>
      <button class="mui-btn mui-btn--raised mui-btn--primary" type="submit">Save</button>
    </form>
    <div class="mui--text-caption" style="margin-top:12px;">
      Tip: For safe testing, leave your own client ID and enable "Test Ads". Google recommends adding <code>data-adtest="on"</code> during development.
    </div>
  </div>
@endsection

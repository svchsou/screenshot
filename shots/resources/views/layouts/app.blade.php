<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Shots') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Material UI (MUI CSS) -->
    <link href="https://cdn.muicss.com/mui-0.10.3/css/mui.min.css" rel="stylesheet">
    <script src="https://cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    @if(env('ADS_ENABLED') && env('ADSENSE_CLIENT_ID'))
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ env('ADSENSE_CLIENT_ID') }}" crossorigin="anonymous"></script>
    @endif
    <!-- Minimal local overrides -->
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">
    <style>
      .appbar-title { font-weight: 700; }
      .dropzone { border: 2px dashed #90caf9; border-radius: 6px; padding: 16px; color:#607d8b; text-align:center; }
      .dropzone.dragover { background: #e3f2fd; }
      .thumb { max-width:100%; max-height:500px; border-radius:4px; border:1px solid #eee; }
    </style>
  </head>
  <body>
    <header class="mui-appbar">
      <div class="mui-container">
        <table width="100%"><tr class="mui--appbar-height">
          <td class="mui--text-title appbar-title"><a href="/" style="color:#fff; text-decoration:none">{{ config('app.name', 'Shots') }}</a></td>
          <td class="mui--text-right">
            <a href="{{ route('upload.form') }}" class="mui--text-light" style="margin-right:12px;">Upload</a>
            @if(session('is_admin'))
              <a href="{{ route('admin.storage.index') }}" class="mui--text-light" style="margin-right:12px;">Storage</a>
              <a href="{{ route('admin.settings.ads') }}" class="mui--text-light" style="margin-right:12px;">Ads</a>
              <a href="{{ route('admin.monitor.index') }}" class="mui--text-light" style="margin-right:12px;">Monitor</a>
              <a href="{{ route('admin.users.index') }}" class="mui--text-light" style="margin-right:12px;">Users</a>
              <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button class="mui--text-light" style="background:none;border:none;cursor:pointer;">Logout</button>
              </form>
            @else
              <a href="{{ route('admin.login') }}" class="mui--text-light">Admin</a>
            @endif
          </td>
        </tr></table>
      </div>
    </header>
    <main class="mui-container" style="margin-top:24px; margin-bottom:48px;">
      @yield('content')
    </main>
  </body>
</html>

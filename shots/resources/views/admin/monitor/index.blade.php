@extends('layouts.app')

@section('content')
  <div class="mui-panel" style="max-width:1000px; margin:0 auto;">
    <h1 class="mui--text-headline" style="margin-top:0">Monitor</h1>
    @if(session('status'))
      <div class="mui--text-success">{{ session('status') }}</div>
    @endif

    <div class="mui-row">
      <div class="mui-col-md-6 mui-col-xs-12">
        <h2 class="mui--text-title">Storage Usage</h2>
        <table class="mui-table">
          <thead><tr><th>Disk</th><th class="mui--text-right">Count</th><th class="mui--text-right">Total (MB)</th></tr></thead>
          <tbody>
          @foreach($byDisk as $row)
            <tr>
              <td>{{ $row->disk }}</td>
              <td class="mui--text-right">{{ number_format($row->count) }}</td>
              <td class="mui--text-right">{{ number_format(($row->total ?? 0) / 1048576, 2) }}</td>
            </tr>
          @endforeach
          </tbody>
          <tfoot>
            <tr><th>Total</th><th class="mui--text-right" colspan="2">{{ number_format(($total ?? 0) / 1048576, 2) }} MB</th></tr>
          </tfoot>
        </table>
      </div>
      <div class="mui-col-md-6 mui-col-xs-12">
        <h2 class="mui--text-title">Maintenance</h2>
        <form method="POST" action="{{ route('admin.purge') }}">
          @csrf
          <button type="submit" class="mui-btn mui-btn--danger">Purge Expired Screenshots</button>
        </form>
      </div>
    </div>

    <h2 class="mui--text-title" style="margin-top:24px;">Recent Logs</h2>
    <pre style="background:#0a0f14;color:#c3d1df;padding:12px;border-radius:4px;max-height:400px;overflow:auto;">
{{ $logs }}
    </pre>
  </div>
@endsection


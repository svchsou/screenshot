@extends('layouts.app')

@section('content')
  <div class="mui-panel" style="max-width:640px; margin:0 auto;">
    <h1 class="mui--text-headline" style="margin-top:0">User Access Control</h1>
    @if(session('status'))
      <div class="mui--text-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('admin.users.save') }}">
      @csrf
      <div class="mui-textfield"><label>Admin Username</label>
        <input type="text" name="ADMIN_USERNAME" value="{{ $data['ADMIN_USERNAME'] }}" required>
      </div>
      <div class="mui-textfield"><label>New Password (leave blank to keep current)</label>
        <input type="password" name="ADMIN_PASSWORD">
      </div>
      <div class="mui--text-caption">Current password {{ $data['HAS_PASSWORD_HASH'] ? 'is set' : 'is not set' }}.</div>
      <button class="mui-btn mui-btn--raised mui-btn--primary" type="submit">Save</button>
    </form>
  </div>
@endsection


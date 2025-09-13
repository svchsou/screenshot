@extends('layouts.app')

@section('content')
  <div class="mui-panel" style="max-width:420px; margin:40px auto;">
    <h1 class="mui--text-headline" style="margin-top:0">Admin Login</h1>
    <form method="POST" action="{{ route('admin.login.post') }}">
      @csrf
      <div class="mui-textfield"><label>Username</label>
        <input type="text" name="username" value="{{ old('username') }}" required>
      </div>
      <div class="mui-textfield"><label>Password</label>
        <input type="password" name="password" required>
      </div>
      @if($errors->any())
        <div class="mui--text-danger">{{ $errors->first() }}</div>
      @endif
      <button class="mui-btn mui-btn--raised mui-btn--primary" type="submit">Login</button>
    </form>
  </div>
@endsection


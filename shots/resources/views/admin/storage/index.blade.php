@extends('layouts.app')

@section('content')
  <div class="mui-panel">
    <div class="mui--clearfix">
      <h1 class="mui--text-headline" style="margin-top:0; display:inline-block;">Storage Destinations</h1>
      <a href="{{ route('admin.storage.create') }}" class="mui-btn mui-btn--raised mui-btn--primary" style="float:right;">Add Destination</a>
    </div>
    @if(session('status'))
      <div class="mui--text-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
      <div class="mui--text-danger">{{ session('error') }}</div>
    @endif
    <table class="mui-table">
      <thead>
        <tr><th>Name</th><th>Type</th><th>Default</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @foreach($destinations as $dest)
        <tr>
          <td>{{ $dest->name }}</td>
          <td>{{ $dest->type }}</td>
          <td>{{ $dest->is_default ? 'Yes' : 'No' }}</td>
          <td>
            <a class="mui-btn mui-btn--small" href="{{ route('admin.storage.edit', $dest->id) }}">Edit</a>
            <form action="{{ route('admin.storage.destroy', $dest->id) }}" method="POST" style="display:inline;">
              @csrf @method('DELETE')
              <button type="submit" class="mui-btn mui-btn--small mui-btn--danger" onclick="return confirm('Delete?')">Delete</button>
            </form>
            <form action="{{ route('admin.storage.validate', $dest->id) }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="mui-btn mui-btn--small mui-btn--accent">Validate</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection

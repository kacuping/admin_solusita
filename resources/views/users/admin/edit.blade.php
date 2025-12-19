@extends('layouts.app')

@section('title', 'Edit Admin')
@section('page-title', 'Management User / Admin / Edit')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Edit Admin</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.admin.update', $user) }}" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Nama</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
          @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
          @error('email')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Password (kosongkan jika tidak diubah)</label>
          <input type="password" name="password" class="form-control">
          @error('password')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Perbarui</button>
          <a href="{{ route('users.admin.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection


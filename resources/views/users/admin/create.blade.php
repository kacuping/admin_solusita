@extends('layouts.app')

@section('title', 'Tambah Admin')
@section('page-title', 'Management User / Admin / Tambah')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Tambah Admin</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.admin.store') }}" class="needs-validation" novalidate>
        @csrf
        <div class="form-group">
          <label>Nama</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
          @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
          @error('email')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
          @error('password')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('users.admin.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection


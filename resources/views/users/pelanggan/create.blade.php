@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('page-title', 'Management User / Pelanggan / Tambah')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Tambah Pelanggan</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('users.pelanggan.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
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
          <label>Alamat</label>
          <input type="text" name="address" value="{{ old('address') }}" class="form-control">
          @error('address')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>No HP</label>
          <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
          @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Avatar</label>
          <input type="file" name="avatar" class="form-control-file" accept="image/*">
          @error('avatar')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('users.pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection


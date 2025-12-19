@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Management Layanan / Kategori / Tambah')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Tambah Kategori</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('services.kategori.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        <div class="form-group">
          <label>Kategori</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
          @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Avatar</label>
          <input type="file" name="avatar" class="form-control-file" accept="image/*">
          @error('avatar')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="{{ route('services.kategori.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection


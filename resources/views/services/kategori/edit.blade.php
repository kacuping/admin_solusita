@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Management Layanan / Kategori / Edit')

@section('content')
  <div class="card">
    <div class="card-header">
      <h4>Edit Kategori</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('services.kategori.update', $category) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Kategori</label>
          <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
          @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Avatar</label>
          <input type="file" name="avatar" class="form-control-file" accept="image/*">
          @error('avatar')<div class="text-danger">{{ $message }}</div>@enderror
          @if($category->avatar)
            <div class="mt-2">
              <img src="{{ asset('storage/' . $category->avatar) }}" alt="avatar" width="60" height="60" style="object-fit:cover;border-radius:50%">
            </div>
          @endif
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Perbarui</button>
          <a href="{{ route('services.kategori.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </form>
    </div>
  </div>
@endsection


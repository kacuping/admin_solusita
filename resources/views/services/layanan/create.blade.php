@extends('layouts.app')

@section('title', 'Tambah Layanan')
@section('page-title', 'Management Layanan / Layanan / Tambah')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Tambah Layanan Baru</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('services.layanan.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Layanan</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Kategori</label>
                <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unit" class="form-label">Satuan</label>
                <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                    <option value="">Pilih Satuan</option>
                    <option value="M2" {{ old('unit') == 'M2' ? 'selected' : '' }}>M2</option>
                    <option value="Seter" {{ old('unit') == 'Seter' ? 'selected' : '' }}>Seter</option>
                    <option value="Unit" {{ old('unit') == 'Unit' ? 'selected' : '' }}>Unit</option>
                </select>
                @error('unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required min="0" step="1">
                </div>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('services.layanan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

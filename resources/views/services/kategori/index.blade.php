@extends('layouts.app')

@section('title', 'Management Layanan - Kategori')
@section('page-title', 'Management Layanan / Kategori')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Daftar Kategori</h4>
      <a href="{{ route('services.kategori.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Avatar</th>
              <th>Kategori</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categories as $category)
              <tr>
                <td>{{ $category->id }}</td>
                <td>
                  @if($category->avatar)
                    <img src="{{ asset('storage/' . $category->avatar) }}" alt="avatar" width="40" height="40" style="object-fit:cover;border-radius:50%">
                  @else
                    -
                  @endif
                </td>
                <td>{{ $category->name }}</td>
                <td>
                  <a href="{{ route('services.kategori.edit', $category) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('services.kategori.destroy', $category) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">Belum ada kategori</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $categories->links() }}
    </div>
  </div>
@endsection


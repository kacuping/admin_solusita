@extends('layouts.app')

@section('title', 'Management Layanan - Layanan')
@section('page-title', 'Management Layanan / Layanan')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Daftar Layanan</h4>
      <a href="{{ route('services.layanan.create') }}" class="btn btn-primary">Tambah Layanan</a>
    </div>
    <div class="card-body">
      {{-- Flash message handled by global script in layout --}}
      
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Layanan</th>
              <th>Kategori</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Deskripsi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($services as $service)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $service->name }}</td>
                <td>{{ $service->category->name ?? '-' }}</td>
                <td>{{ $service->unit }}</td>
                <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                <td>{{ Str::limit($service->description, 50) }}</td>
                <td>
                  <a href="{{ route('services.layanan.edit', $service) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('services.layanan.destroy', $service) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center">Belum ada layanan</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

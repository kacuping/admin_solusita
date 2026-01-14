@extends('layouts.app')

@section('title', 'Management User - Pelanggan')
@section('page-title', 'Management User / Pelanggan')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Daftar Pelanggan</h4>
      <a href="{{ route('users.pelanggan.create') }}" class="btn btn-primary">Tambah Pelanggan</a>
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
              <th>Nama</th>
              <th>Email</th>
              <th>Alamat</th>
              <th>No HP</th>
              <th>Status Notif</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
              <tr>
                <td>{{ $customer->id }}</td>
                <td>
                  @if($customer->avatar)
                    <img src="{{ asset('storage/' . $customer->avatar) }}" alt="avatar" width="40" height="40" style="object-fit:cover;border-radius:50%">
                  @else
                    -
                  @endif
                </td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->phone }}</td>
                <td>
                    @if($customer->device_token)
                        <span class="badge badge-success">Terhubung</span>
                    @else
                        <span class="badge badge-secondary">Belum Terhubung</span>
                    @endif
                </td>
                <td>
                  <a href="{{ route('users.pelanggan.edit', $customer) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('users.pelanggan.destroy', $customer) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center">Belum ada pelanggan</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $customers->links() }}
    </div>
  </div>
@endsection

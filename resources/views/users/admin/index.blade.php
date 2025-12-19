@extends('layouts.app')

@section('title', 'Management User - Admin')
@section('page-title', 'Management User / Admin')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Daftar Admin</h4>
      <a href="{{ route('users.admin.create') }}" class="btn btn-primary">Tambah Admin</a>
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
              <th>Nama</th>
              <th>Email</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($admins as $admin)
              <tr>
                <td>{{ $admin->id }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>
                  <a href="{{ route('users.admin.edit', $admin) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('users.admin.destroy', $admin) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">Belum ada admin</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $admins->links() }}
    </div>
  </div>
@endsection

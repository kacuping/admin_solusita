@extends('layouts.app')

@section('title', 'Management User - Cleaner')
@section('page-title', 'Management User / Cleaner')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4>Daftar Cleaner</h4>
      <a href="{{ route('users.cleaner.create') }}" class="btn btn-primary">Tambah Cleaner</a>
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
              <th>No HP</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cleaners as $cleaner)
              <tr>
                <td>{{ $cleaner->id }}</td>
                <td>
                  @if($cleaner->avatar)
                    <img src="{{ asset('storage/' . $cleaner->avatar) }}" alt="avatar" width="40" height="40" style="object-fit:cover;border-radius:50%">
                  @else
                    -
                  @endif
                </td>
                <td>{{ $cleaner->name }}</td>
                <td>{{ $cleaner->email }}</td>
                <td>{{ $cleaner->phone }}</td>
                <td>
                  <a href="{{ route('users.cleaner.edit', $cleaner) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('users.cleaner.destroy', $cleaner) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center">Belum ada cleaner</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $cleaners->links() }}
    </div>
  </div>
@endsection

@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Test Notifikasi FCM</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('settings.test-notification') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Target Pengiriman</label>
                            <select name="target_type" class="form-control" id="targetType" onchange="toggleTarget()">
                                <option value="specific_user">Pilih User Pelanggan (Aktif)</option>
                                <option value="manual_token">Input Manual Token</option>
                            </select>
                        </div>

                        <div class="form-group" id="userSelectGroup">
                            <label>Pilih User</label>
                            <select name="user_id" class="form-control select2">
                                <option value="">-- Pilih User --</option>
                                @foreach($usersWithToken as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya menampilkan user yang sudah memiliki device token.</small>
                        </div>

                        <div class="form-group d-none" id="manualTokenGroup">
                            <label>Device Token (FCM)</label>
                            <textarea name="manual_token" class="form-control" rows="3" placeholder="Paste token FCM di sini..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Judul Notifikasi</label>
                            <input type="text" name="title" class="form-control" value="Test Notification" required>
                        </div>

                        <div class="form-group">
                            <label>Isi Pesan</label>
                            <textarea name="body" class="form-control" rows="2" required>Ini adalah pesan tes notifikasi dari Admin Panel.</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Kirim Notifikasi</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Informasi Aplikasi</h4>
                </div>
                <div class="card-body">
                    <p>Halaman pengaturan aplikasi.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleTarget() {
            const type = document.getElementById('targetType').value;
            if (type === 'specific_user') {
                document.getElementById('userSelectGroup').classList.remove('d-none');
                document.getElementById('manualTokenGroup').classList.add('d-none');
            } else {
                document.getElementById('userSelectGroup').classList.add('d-none');
                document.getElementById('manualTokenGroup').classList.remove('d-none');
            }
        }
    </script>
    @endpush
@endsection

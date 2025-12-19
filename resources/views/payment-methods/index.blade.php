@extends('layouts.app')

@section('title', 'Metode Pembayaran')
@section('page-title', 'Metode Pembayaran')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Daftar Metode Pembayaran</h4>
            <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">Tambah Metode</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Metode</th>
                            <th>Bank</th>
                            <th>Nomor Rekening</th>
                            <th>Atas Nama</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $paymentMethod)
                            <tr>
                                <td>{{ $paymentMethods->firstItem() + $loop->index }}</td>
                                <td>{{ $paymentMethod->name }}</td>
                                <td>{{ $paymentMethod->bank_name }}</td>
                                <td>{{ $paymentMethod->account_number }}</td>
                                <td>{{ $paymentMethod->account_holder }}</td>
                                <td>
                                    <span class="badge badge-{{ $paymentMethod->is_active ? 'success' : 'danger' }}">
                                        {{ $paymentMethod->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('payment-methods.edit', $paymentMethod) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada metode pembayaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $paymentMethods->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h4>Riwayat Transaksi Pembayaran</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Kode Transaksi</th>
                            <th>Pelanggan</th>
                            <th>Cleaner</th>
                            <th>Layanan</th>
                            <th>Total</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>{{ $transactions->firstItem() + $loop->index }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}</td>
                                <td>{{ $transaction->code }}</td>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                                <td>{{ $transaction->cleaner->name ?? '-' }}</td>
                                <td>{{ $transaction->service->name ?? '-' }}</td>
                                <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($transaction->payment_type)
                                        <span class="badge badge-info">{{ strtoupper($transaction->payment_type) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($transaction->status == 'process')
                                        <span class="badge badge-primary">Process</span>
                                    @elseif($transaction->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($transaction->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->payment_url && filter_var($transaction->payment_url, FILTER_VALIDATE_URL))
                                        <a href="{{ $transaction->payment_url }}" target="_blank" class="btn btn-sm btn-info">Link</a>
                                    @elseif($transaction->payment_url)
                                        <span class="badge badge-light">{{ $transaction->payment_url }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada riwayat transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $transactions->appends(['page' => $paymentMethods->currentPage()])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

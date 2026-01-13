@extends('layouts.app')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Transaksi Hari Ini</h4>
            {{-- <a href="#" class="btn btn-primary" disabled>Tambah Transaksi</a> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Alamat</th>
                            <th>Layanan</th>
                            <th>Tgl Order</th>
                            <th>Status</th>
                            <th>Cleaner</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayTransactions as $transaction)
                            <tr>
                                <td>{{ $todayTransactions->firstItem() + $loop->index }}</td>
                                <td>{{ $transaction->code }}</td>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                                <td>{{ $transaction->order_address ?? ($transaction->user->address ?? '-') }}</td>
                                <td>{{ $transaction->service->name ?? '-' }}</td>
                                <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : ($transaction->status == 'cancelled' ? 'danger' : 'primary')) }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->cleaner->name ?? '-' }}</td>
                                <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail"
                                        data-id="{{ $transaction->id }}">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada transaksi hari ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $todayTransactions->appends(['history_page' => $historyTransactions->currentPage()])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Riwayat Transaksi (30 Hari Terakhir)</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('transaksi.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal Order</label>
                            <input type="date" class="form-control" name="filter_date"
                                value="{{ request('filter_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Layanan</label>
                            <select class="form-control" name="filter_service">
                                <option value="">Semua Layanan</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}"
                                        {{ request('filter_service') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="filter_status">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="process" {{ request('filter_status') == 'process' ? 'selected' : '' }}>
                                    Process</option>
                                <option value="completed" {{ request('filter_status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="cancelled" {{ request('filter_status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cleaner</label>
                            <select class="form-control" name="filter_cleaner">
                                <option value="">Semua Cleaner</option>
                                @foreach ($cleaners as $cleaner)
                                    <option value="{{ $cleaner->id }}"
                                        {{ request('filter_cleaner') == $cleaner->id ? 'selected' : '' }}>
                                        {{ $cleaner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Alamat</th>
                            <th>Layanan</th>
                            <th>Tgl Order</th>
                            <th>Status</th>
                            <th>Cleaner</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyTransactions as $transaction)
                            <tr>
                                <td>{{ $historyTransactions->firstItem() + $loop->index }}</td>
                                <td>{{ $transaction->code }}</td>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                                <td>{{ $transaction->order_address ?? ($transaction->user->address ?? '-') }}</td>
                                <td>{{ $transaction->service->name ?? '-' }}</td>
                                <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : ($transaction->status == 'cancelled' ? 'danger' : 'primary')) }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>{{ $transaction->cleaner->name ?? '-' }}</td>
                                <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail"
                                        data-id="{{ $transaction->id }}">Detail</button>
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
                {{ $historyTransactions->appends(['today_page' => $todayTransactions->currentPage()])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div class="modal fade" id="transactionDetailModal" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Transaksi <span id="modal-trx-code"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Tanggal Order</th>
                                <td id="detail-date"></td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td id="detail-customer"></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td id="detail-email"></td>
                            </tr>
                            <tr>
                                <th>Layanan</th>
                                <td id="detail-service"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="detail-status"></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td id="detail-address"></td>
                            </tr>
                            <tr id="row-cancellation-reason" style="display:none;">
                                <th>Alasan Pembatalan</th>
                                <td id="detail-cancellation-reason" class="text-danger font-weight-bold"></td>
                            </tr>
                            <tr>
                                <th>Koordinat</th>
                                <td id="detail-coords"></td>
                            </tr>
                            <tr>
                                <th>Cleaner</th>
                                <td id="detail-cleaner-container">
                                    <div id="cleaner-display" style="display:none;"></div>
                                    <div id="cleaner-select-container" style="display:none;">
                                        <form id="assign-cleaner-form">
                                            @csrf
                                            <div class="input-group">
                                                <select class="custom-select" id="cleaner-select" name="cleaner_id"
                                                    required>
                                                    <option value="">Pilih Cleaner</option>
                                                    @foreach ($cleaners as $cleaner)
                                                        <option value="{{ $cleaner->id }}">{{ $cleaner->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit"
                                                        id="btn-assign">Assign</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div id="cleaner-empty" style="display:none;">-</div>
                                </td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td id="detail-total"></td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran</th>
                                <td id="detail-payment-type"></td>
                            </tr>
                            <tr id="row-payment-proof" style="display:none;">
                                <th>Bukti Transfer</th>
                                <td>
                                    <a href="#" id="link-payment-proof" target="_blank">
                                        <img id="img-payment-proof" src="" alt="Bukti Transfer" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-complete-trx" style="display:none;">Selesaikan Transaksi</button>
                    <button type="button" class="btn btn-danger" id="btn-cancel-trx" style="display:none;">Batalkan
                        Transaksi</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-specific')
    <script>
        $(document).ready(function() {
            // Pindahkan modal ke body untuk menghindari masalah z-index/backdrop
            $('#transactionDetailModal').appendTo("body");

            let currentTransactionId = null;

            $('.btn-detail').click(function() {
                var transactionId = $(this).data('id');
                currentTransactionId = transactionId;
                var url = "{{ route('transaksi.show', ':id') }}".replace(':id', transactionId);

                // Reset modal content
                $('#detail-code').text('Loading...');
                $('#detail-date').text('');
                $('#detail-customer').text('');
                $('#detail-email').text('');
                $('#detail-status').text('');
                $('#detail-total').text('');
                $('#modal-trx-code').text('');

                // Reset cleaner sections
                $('#cleaner-display').hide().text('');
                $('#cleaner-select-container').hide();
                $('#cleaner-empty').hide();
                $('#cleaner-select').val('');

                // Reset cancel button
                $('#btn-cancel-trx').hide();
                $('#btn-complete-trx').hide();

                $('#transactionDetailModal').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#detail-code').text(response.code);
                        $('#modal-trx-code').text(response.code);
                        $('#detail-date').text(new Date(response.transaction_date)
                            .toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }));
                        $('#detail-customer').text(response.user ? response.user.name : '-');
                        $('#detail-email').text(response.user ? response.user.email : '-');
                        $('#detail-service').text(response.service ? response.service.name :
                            '-');

                        let statusLabel = response.status.charAt(0).toUpperCase() + response
                            .status.slice(1);
                        $('#detail-status').text(statusLabel);
                        $('#detail-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(
                            response.total));

                        // Payment Info
                        let paymentType = response.payment_type ? response.payment_type.toUpperCase() : '-';
                        if (paymentType === 'BRI_VA') paymentType = 'BRI Virtual Account';
                        $('#detail-payment-type').text(paymentType);

                        if (response.payment_proof) {
                            let proofUrl = "{{ asset('storage') }}/" + response.payment_proof;
                            $('#img-payment-proof').attr('src', proofUrl);
                            $('#link-payment-proof').attr('href', proofUrl);
                            $('#row-payment-proof').show();
                        } else {
                            $('#row-payment-proof').hide();
                        }

                        $('#detail-address').text(response.order_address ? response.order_address : '-');
                        let coords = (response.order_lat && response.order_lng)
                            ? `${response.order_lat}, ${response.order_lng}`
                            : '-';
                        $('#detail-coords').text(coords);

                        // Cancellation Info
                        if (response.status === 'cancelled') {
                            $('#row-cancellation-reason').show();
                            let reason = response.cancellation_reason || '-';
                            $('#detail-cancellation-reason').text(reason);
                            
                            // Adjust status label based on reason
                            if (reason.includes('User Cancelled')) {
                                $('#detail-status').text('User Cancelled');
                            } else if (reason.includes('Admin Cancelled')) {
                                $('#detail-status').text('Admin Cancelled');
                            } else if (reason.includes('System Cancelled')) {
                                $('#detail-status').text('System Cancelled');
                            }
                        } else {
                            $('#row-cancellation-reason').hide();
                        }

                        // Logic Cleaner Display
                        if (response.cleaner) {
                            $('#cleaner-display').text(response.cleaner.name).show();
                        } else {
                            if (response.status === 'pending') {
                                $('#cleaner-select-container').show();
                            } else {
                                $('#cleaner-empty').show();
                            }
                        }

                        // Logic Cancel Button
                        if (response.status === 'pending' || response.status === 'process') {
                            $('#btn-cancel-trx').show();
                        }

                        // Logic Complete Button
                        if (response.status === 'process') {
                            $('#btn-complete-trx').show();
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data transaksi', 'error');
                        $('#transactionDetailModal').modal('hide');
                    }
                });
            });

            // Handle Assign Cleaner
            $('#assign-cleaner-form').submit(function(e) {
                e.preventDefault();
                if (!currentTransactionId) return;

                var cleanerId = $('#cleaner-select').val();
                var url = "{{ route('transaksi.assign', ':id') }}".replace(':id', currentTransactionId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cleaner_id: cleanerId
                    },
                    success: function(response) {
                        $('#transactionDetailModal').modal('hide');
                        Swal.fire('Berhasil',
                                'Cleaner berhasil ditugaskan dan status menjadi Process',
                                'success')
                            .then(() => {
                                location.reload();
                            });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal menugaskan cleaner', 'error');
                    }
                });
            });

            // Handle Complete Transaction
            $('#btn-complete-trx').click(function() {
                if (!currentTransactionId) return;

                Swal.fire({
                    title: 'Selesaikan Transaksi?',
                    text: "Pastikan pekerjaan telah selesai sebelum mengubah status",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Selesaikan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('transaksi.complete', ':id') }}".replace(':id', currentTransactionId);

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                $('#transactionDetailModal').modal('hide');
                                Swal.fire(
                                    'Berhasil!',
                                    'Transaksi telah diselesaikan.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                var msg = 'Gagal menyelesaikan transaksi';
                                if(xhr.responseJSON && xhr.responseJSON.error) {
                                    msg = xhr.responseJSON.error;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Handle Cancel Transaction
            $('#btn-cancel-trx').click(function() {
                if (!currentTransactionId) return;

                Swal.fire({
                    title: 'Batalkan Transaksi?',
                    text: "Jika setuju tekan Ya untuk membatalkan Transaksi",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak',
                    showClass: {
                        popup: 'animate__animated animate__zoomIn'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__zoomOut'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Alasan Pembatalan',
                            input: 'textarea',
                            // inputLabel: 'Alasan Pembatalan (Wajib diisi)',
                            inputPlaceholder: 'Tuliskan alasan pembatalan di sini...',
                            inputAttributes: {
                                'aria-label': 'Tuliskan alasan pembatalan di sini'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Proses',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            preConfirm: (reason) => {
                                if (!reason) {
                                    Swal.showValidationMessage(
                                        'Alasan pembatalan wajib diisi');
                                }
                                return reason;
                            },
                            showClass: {
                                popup: 'animate__animated animate__zoomIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut'
                            }
                        }).then((inputResult) => {
                            if (inputResult.isConfirmed) {
                                var url = "{{ route('transaksi.cancel', ':id') }}".replace(
                                    ':id', currentTransactionId);

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        reason: inputResult.value
                                    },
                                    success: function(response) {
                                        $('#transactionDetailModal').modal(
                                            'hide');
                                        Swal.fire({
                                            title: 'Dibatalkan!',
                                            text: 'Transaksi berhasil dibatalkan.',
                                            icon: 'success',
                                            showClass: {
                                                popup: 'animate__animated animate__zoomIn'
                                            },
                                            hideClass: {
                                                popup: 'animate__animated animate__zoomOut'
                                            }
                                        }).then(() => {
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr) {
                                        let errorMsg =
                                            'Gagal membatalkan transaksi';
                                        if (xhr.responseJSON && xhr.responseJSON
                                            .message) {
                                            errorMsg = xhr.responseJSON.message;
                                        }
                                        Swal.fire('Error', errorMsg, 'error');
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

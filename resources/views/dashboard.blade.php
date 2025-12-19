@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Pendapatan</h4>
                    </div>
                    <div class="card-body">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Transaksi</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalTransactions }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Pelanggan</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalCustomers }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Cleaner</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalCleaners }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan 7 Hari Terakhir</h4>
                </div>
                <div class="card-body">
                    <canvas id="incomeChart" height="182"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Status Transaksi</h4>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Terbaru</h4>
                    <div class="card-header-action">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->code }}</td>
                                        <td>{{ $transaction->user->name ?? '-' }}</td>
                                        <td>{{ $transaction->service->name ?? '-' }}</td>
                                        <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                        <td>
                                            @if ($transaction->status == 'pending')
                                                <div class="badge badge-warning">Pending</div>
                                            @elseif($transaction->status == 'process')
                                                <div class="badge badge-info">Process</div>
                                            @elseif($transaction->status == 'completed')
                                                <div class="badge badge-success">Completed</div>
                                            @elseif($transaction->status == 'cancelled')
                                                <div class="badge badge-danger">Cancelled</div>
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada transaksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-libraries')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
@endpush

@push('js-specific')
    <script>
        var ctxIncome = document.getElementById("incomeChart").getContext('2d');
        var incomeChart = new Chart(ctxIncome, {
            type: 'line',
            data: {
                labels: {!! json_encode($incomeChart['labels']) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($incomeChart['data']) !!},
                    borderWidth: 2,
                    backgroundColor: 'rgba(63,82,227,.8)',
                    borderColor: 'transparent',
                    pointBorderWidth: 0,
                    pointRadius: 3.5,
                    pointBackgroundColor: 'transparent',
                    pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
                }]
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        gridLines: {
                            drawBorder: false,
                            color: '#f2f2f2',
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 150000,
                            callback: function(value, index, values) {
                                return 'Rp ' + value;
                            }
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false,
                            tickMarkLength: 15,
                        }
                    }]
                },
            }
        });

        var ctxStatus = document.getElementById("statusChart").getContext('2d');
        var statusChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: {!! json_encode($statusChart['data']) !!},
                    backgroundColor: [
                        '#ffa426', // Pending (Orange)
                        '#6777ef', // Process (Blue)
                        '#63ed7a', // Completed (Green)
                        '#fc544b', // Cancelled (Red)
                    ],
                    label: 'Status Transaksi'
                }],
                labels: {!! json_encode($statusChart['labels']) !!},
            },
            options: {
                responsive: true,
                legend: {
                    position: 'bottom',
                },
            }
        });
    </script>
@endpush

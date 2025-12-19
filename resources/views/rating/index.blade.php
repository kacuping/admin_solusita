@extends('layouts.app')

@section('title', 'Rating Cleaner')
@section('page-title', 'Rating Cleaner')
@push('css')
    <style>
        .modal-backdrop { z-index: 1060 !important; }
        .modal { z-index: 1070 !important; }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Daftar Rating Cleaner</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cleaner</th>
                            <th>Average Rating</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cleanerRatings as $cr)
                            <tr>
                                <td>{{ $cleanerRatings->firstItem() + $loop->index }}</td>
                                <td>{{ $cr->cleaner->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $full = (int) floor($cr->avg_rating);
                                        $half = $cr->avg_rating - $full >= 0.5;
                                        $empty = 5 - $full - ($half ? 1 : 0);
                                    @endphp
                                    @for ($i = 0; $i < $full; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    @if ($half)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    @endif
                                    @for ($i = 0; $i < $empty; $i++)
                                        <i class="far fa-star text-muted"></i>
                                    @endfor
                                    <span class="ml-2">{{ number_format($cr->avg_rating, 2) }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-detail" data-cleaner-id="{{ $cr->cleaner_id }}" data-url="{{ route('rating.show', $cr->cleaner_id) }}">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data rating</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                {{ $cleanerRatings->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <div class="modal fade" id="ratingDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Rating Cleaner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 id="detail-cleaner-name" class="mb-1"></h6>
                        <div id="detail-cleaner-rating" class="mb-2"></div>
                        <small id="detail-cleaner-summary" class="text-muted"></small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="detail-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.btn-detail', function() {
            var url = $(this).data('url');
            $('#detail-cleaner-name').text('');
            $('#detail-cleaner-rating').html('');
            $('#detail-cleaner-summary').text('');
            $('#detail-table tbody').html('');
            $('#ratingDetailModal').modal('show');
            $.get(url, function(res) {
                $('#detail-cleaner-name').text(res.cleaner.name);
                var avg = res.avg_rating || 0;
                var full = Math.floor(avg);
                var half = (avg - full) >= 0.5;
                var empty = 5 - full - (half ? 1 : 0);
                var stars = '';
                for (var i = 0; i < full; i++) stars += '<i class="fas fa-star text-warning"></i>';
                if (half) stars += '<i class="fas fa-star-half-alt text-warning"></i>';
                for (var i = 0; i < empty; i++) stars += '<i class="far fa-star text-muted"></i>';
                $('#detail-cleaner-rating').html(stars + ' <span class="ml-2">' + (avg.toFixed ? avg.toFixed(2) : avg) + '</span>');
                $('#detail-cleaner-summary').text(res.ratings_count + ' ulasan');
                if (Array.isArray(res.reviews)) {
                    res.reviews.forEach(function(r) {
                        var rFull = Math.floor(r.rating || 0);
                        var rHalf = ((r.rating || 0) - rFull) >= 0.5;
                        var rEmpty = 5 - rFull - (rHalf ? 1 : 0);
                        var rStars = '';
                        for (var i = 0; i < rFull; i++) rStars += '<i class="fas fa-star text-warning"></i>';
                        if (rHalf) rStars += '<i class="fas fa-star-half-alt text-warning"></i>';
                        for (var i = 0; i < rEmpty; i++) rStars += '<i class="far fa-star text-muted"></i>';
                        var row = '<tr>' +
                            '<td>' + new Date(r.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + '</td>' +
                            '<td>' + (r.customer || '-') + '</td>' +
                            '<td>' + (r.service || '-') + '</td>' +
                            '<td>' + rStars + '</td>' +
                            '<td>' + (r.review || '-') + '</td>' +
                            '</tr>';
                        $('#detail-table tbody').append(row);
                    });
                }
            });
        });
    </script>
@endsection

@push('js-specific')
    <script>
        $(document).on('click', '.btn-detail', function() {
            var url = $(this).data('url');
            $('#detail-cleaner-name').text('');
            $('#detail-cleaner-rating').html('');
            $('#detail-cleaner-summary').text('');
            $('#detail-table tbody').html('');
            var $modal = $('#ratingDetailModal');
            $modal.appendTo('body');
            $modal.modal('show');
            $.get(url, function(res) {
                $('#detail-cleaner-name').text(res.cleaner.name);
                var avg = res.avg_rating || 0;
                var full = Math.floor(avg);
                var half = (avg - full) >= 0.5;
                var empty = 5 - full - (half ? 1 : 0);
                var stars = '';
                for (var i = 0; i < full; i++) stars += '<i class="fas fa-star text-warning"></i>';
                if (half) stars += '<i class="fas fa-star-half-alt text-warning"></i>';
                for (var i = 0; i < empty; i++) stars += '<i class="far fa-star text-muted"></i>';
                $('#detail-cleaner-rating').html(stars + ' <span class="ml-2">' + (avg.toFixed ? avg.toFixed(2) : avg) + '</span>');
                $('#detail-cleaner-summary').text(res.ratings_count + ' ulasan');
                if (Array.isArray(res.reviews)) {
                    res.reviews.forEach(function(r) {
                        var rFull = Math.floor(r.rating || 0);
                        var rHalf = ((r.rating || 0) - rFull) >= 0.5;
                        var rEmpty = 5 - rFull - (rHalf ? 1 : 0);
                        var rStars = '';
                        for (var i = 0; i < rFull; i++) rStars += '<i class="fas fa-star text-warning"></i>';
                        if (rHalf) rStars += '<i class="fas fa-star-half-alt text-warning"></i>';
                        for (var i = 0; i < rEmpty; i++) rStars += '<i class="far fa-star text-muted"></i>';
                        var row = '<tr>' +
                            '<td>' + new Date(r.date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + '</td>' +
                            '<td>' + (r.customer || '-') + '</td>' +
                            '<td>' + (r.service || '-') + '</td>' +
                            '<td>' + rStars + '</td>' +
                            '<td>' + (r.review || '-') + '</td>' +
                            '</tr>';
                        $('#detail-table tbody').append(row);
                    });
                }
            });
        });
    </script>
@endpush

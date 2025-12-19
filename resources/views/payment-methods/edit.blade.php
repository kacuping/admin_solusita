@extends('layouts.app')

@section('title', 'Edit Metode Pembayaran')
@section('page-title', 'Edit Metode Pembayaran')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Form Edit Metode Pembayaran</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('payment-methods.update', $paymentMethod) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama Metode</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $paymentMethod->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nama Bank / Provider</label>
                    <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                        value="{{ old('bank_name', $paymentMethod->bank_name) }}" required>
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nomor Rekening</label>
                    <input type="text" name="account_number"
                        class="form-control @error('account_number') is-invalid @enderror"
                        value="{{ old('account_number', $paymentMethod->account_number) }}" required>
                    @error('account_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Atas Nama</label>
                    <input type="text" name="account_holder"
                        class="form-control @error('account_holder') is-invalid @enderror"
                        value="{{ old('account_holder', $paymentMethod->account_holder) }}" required>
                    @error('account_holder')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="control-label">Status</div>
                    <label class="custom-switch mt-2">
                        <input type="checkbox" name="is_active" class="custom-switch-input" value="1"
                            {{ $paymentMethod->is_active ? 'checked' : '' }}>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">Aktif</span>
                    </label>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

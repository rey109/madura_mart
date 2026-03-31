@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('order')
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $title }}</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">{{ $title }}</h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Order Details #{{ $data->id }}</h6>
                        <a href="{{ route('order.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="mb-0">Customer Information</h6>
                                <p class="text-sm mb-0">Name: {{ $data->pelanggan->nama_pelanggan ?? 'Unknown' }}</p>
                                <p class="text-sm mb-0">Phone: {{ $data->pelanggan->no_hp ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="mb-0">Order Summary</h6>
                                <p class="text-sm mb-0">Date: {{ $data->tgl_pemesanan }}</p>
                                <p class="text-sm mb-0">Status: <span class="badge bg-gradient-{{ $data->status_pemesanan == 'selesai' ? 'success' : ($data->status_pemesanan == 'dibatalkan' ? 'danger' : 'warning') }}">{{ ucfirst($data->status_pemesanan) }}</span></p>
                                <p class="text-sm mb-0">Payment: {{ strtoupper($data->metode_pembayaran) }}</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qty</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $detail->product->nama_barang ?? 'Deleted Product' }}</h6>
                                                    @if($detail->catatan)
                                                    <p class="text-xs text-secondary mb-0">Note: {{ $detail->catatan }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            {{ $detail->jumlah_jual }}
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="text-end font-weight-bold">Total</td>
                                        <td class="text-center font-weight-bold">Rp {{ number_format($data->total_bayar, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        @if($data->keterangan_status)
                        <div class="mt-4">
                            <h6 class="mb-1">Status Note</h6>
                            <p class="text-sm bg-light p-3 border-radius-md">{{ $data->keterangan_status }}</p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

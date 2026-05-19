@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('purchase')
    <style>
        .pos-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: white;
            padding: 30px;
        }
        .form-label {
            color: #344767;
            font-size: 13px;
            font-weight: 700;
        }
        .form-control, .form-select {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            color: #555;
        }
        .form-control:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.25);
        }
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
        .btn-cancel {
            background-color: #8392ab;
            color: white;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }
        .btn-cancel:hover {
            background-color: #6c757d;
            color: white;
        }
        .btn-update {
            background-color: #cb0c9f;
            color: white;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
        }
        .btn-update:hover {
            background-color: #b10a8b;
            color: white;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="pos-card mb-4">
                    <h5 class="mb-4" style="color: #344767; font-weight: 600;">Edit Purchases Data</h5>
                    
                    <form action="{{ route('purchase.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @php
                            // Ambil detail pertama sebagai representasi di form
                            $detail = $data->details->first();
                        @endphp

                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Invoice No</label>
                                    <input type="text" class="form-control" name="no_nota" value="{{ $data->no_nota }}" readonly>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" name="tgl_nota" value="{{ $data->tgl_nota }}" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">Distributor</label>
                                    <input type="text" class="form-control" name="distributor_name" value="{{ $data->distributor->nama_distributor ?? '' }}" readonly>
                                    <input type="hidden" name="id_distributor" value="{{ $data->id_distributor }}">
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">Book</label>
                                    <input type="text" class="form-control" name="book_name" value="{{ $detail->product->nama_barang ?? '' }}" readonly>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Purchase Price</label>
                                    <input type="number" class="form-control" id="purchase_price" value="{{ $detail->harga_beli ?? 0 }}" readonly>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" value="{{ $detail->jumlah_beli ?? 0 }}" readonly>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">SubTotal</label>
                                    <input type="number" class="form-control" id="subtotal" value="{{ $detail->subtotal ?? 0 }}" readonly>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label">Total Payment</label>
                                    <input type="text" class="form-control bg-light" name="total_bayar" value="{{ $data->total_bayar }}" readonly style="background-color: #e9ecef !important; font-size: 16px; font-weight: bold; color: #333;">
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('purchase.index') }}" class="btn btn-cancel me-2">CANCEL</a>
                            <button type="submit" class="btn btn-update">EDIT THIS PURCHASE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

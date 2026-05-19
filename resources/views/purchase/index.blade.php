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
            padding: 20px;
        }
        .header-box {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .header-icon {
            width: 50px;
            height: 50px;
            background-color: #f8f9fa;
            color: #344767;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
        }
        .btn-add-new {
            background-color: #cb0c9f;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 20px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-add-new:hover {
            background-color: #b10a8b;
            color: white;
        }
        .section-title {
            color: #8392ab;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e9ecef;
            margin-left: 15px;
        }
        .table-custom thead th {
            background-color: #f8f9fa !important;
            color: #8392ab !important;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 10px;
            white-space: nowrap;
        }
        .table-custom tbody td {
            font-size: 12px;
            color: #555;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            padding: 10px;
        }
        .total-pay-badge {
            background-color: #cb0c9f;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }
        .action-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #e9ecef;
            background-color: #fff;
            color: #344767;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            margin: 0 2px;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background-color: #f8f9fa;
            color: #cb0c9f;
        }
        .product-img {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #eee;
        }
    </style>

    <div class="container-fluid py-4">
        
        <div class="header-box mb-4">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h5 class="mb-1" style="font-weight: 700; color: #444;">Purchase</h5>
                    <p class="mb-0 text-muted" style="font-size: 13px;">Daftar semua data pembelian produk</p>
                </div>
            </div>
            <div>
                <a href="{{ route('purchase.create') }}" class="btn-add-new">
                    <i class="fas fa-plus me-1"></i> Add New Purchase
                </a>
            </div>
        </div>

        <div class="pos-card">
            <div class="section-title">PURCHASE DATA</div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th>INVOICE NO</th>
                            <th>INVOICE DATE</th>
                            <th>DISTRIBUTOR</th>
                            <th>PRODUCT</th>
                            <th>EXPIRED DATE</th>
                            <th class="text-center">STOCK</th>
                            <th>SELLING PRICE</th>
                            <th>PURCHASE PRICE</th>
                            <th>SELLING MARGIN</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">IMAGE</th>
                            <th>SUB TOTAL</th>
                            <th class="text-center">TOTAL PAY</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach ($datas as $purchase)
                            @foreach ($purchase->details as $index => $detail)
                                <tr>
                                    <td class="text-center font-weight-bold" style="color: #8392ab;">{{ $no++ }}</td>
                                    <td class="font-weight-bold">{{ $purchase->no_nota }}</td>
                                    <td>{{ $purchase->tgl_nota }}</td>
                                    <td>{{ $purchase->distributor->nama_distributor ?? '-' }}</td>
                                    <td>{{ $detail->product->nama_barang ?? 'Unknown' }}</td>
                                    <td>{{ $detail->product->tgl_expired ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->product->stok ?? 0 }}</td>
                                    <td>Rp {{ number_format($detail->harga_beli + $detail->margin_jual) }}</td>
                                    <td>Rp {{ number_format($detail->harga_beli) }}</td>
                                    <td>Rp {{ number_format($detail->margin_jual) }}</td>
                                    <td class="text-center">{{ $detail->jumlah_beli }}</td>
                                    <td class="text-center">
                                        @if(isset($detail->product->foto_barang) && $detail->product->foto_barang)
                                            <img src="{{ asset('storage/' . $detail->product->foto_barang) }}" class="product-img" alt="product">
                                        @else
                                            <div class="product-img d-flex align-items-center justify-content-center bg-light text-muted" style="font-size: 10px;">No Img</div>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">Rp {{ number_format($detail->subtotal) }}</td>
                                    <td class="text-center">
                                        @if($index === 0)
                                            <!-- Only show Total Pay once per invoice, or show on every row as requested by photo? Photo shows on every row! -->
                                            <span class="total-pay-badge">Rp {{ number_format($purchase->total_bayar) }}</span>
                                        @else
                                            <span class="total-pay-badge">Rp {{ number_format($purchase->total_bayar) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(auth()->user()->role === 'owner')
                                            <a href="{{ route('purchase.edit', $purchase->id) }}" class="action-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="delete-form-{{ $purchase->id }}" action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn" title="Delete" onclick="confirmDelete('delete-form-{{ $purchase->id }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="action-btn" title="Edit" onclick="requirePasswordForEdit('{{ route('purchase.edit', $purchase->id) }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form id="delete-form-{{ $purchase->id }}" action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn" title="Delete" onclick="requirePasswordForDelete('delete-form-{{ $purchase->id }}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        @if($datas->isEmpty())
                            <tr>
                                <td colspan="15" class="text-center py-4 text-muted">Belum ada data pembelian.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $datas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Password default untuk Kepala Perpus / Atasan
        const BOSS_PASSWORD = "123";

        function requirePasswordForEdit(url) {
            swal({
                title: "Password required!",
                text: "Write your boss's password:",
                type: "input",
                inputType: "password",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonColor: "#cb0c9f",
                confirmButtonText: "OK",
                cancelButtonText: "CANCEL",
                animation: "slide-from-top",
                inputPlaceholder: ".........."
            }, function(inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Password tidak boleh kosong!");
                    return false;
                }
                
                if (inputValue === BOSS_PASSWORD) {
                    swal({
                        title: "Nice!",
                        text: "Your password is correct!",
                        type: "success",
                        confirmButtonColor: "#cb0c9f",
                        confirmButtonText: "OK",
                        closeOnConfirm: true
                    }, function() {
                        window.location.href = url;
                    });
                } else {
                    swal.showInputError("Password salah!");
                    return false;
                }
            });
        }

        function requirePasswordForDelete(formId) {
            swal({
                title: "Password required!",
                text: "Write your boss's password:",
                type: "input",
                inputType: "password",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonColor: "#cb0c9f",
                confirmButtonText: "OK",
                cancelButtonText: "CANCEL",
                animation: "slide-from-top",
                inputPlaceholder: ".........."
            }, function(inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("Password tidak boleh kosong!");
                    return false;
                }
                
                if (inputValue === BOSS_PASSWORD) {
                    swal({
                        title: "Are you sure want to delete?",
                        text: "Your will not be able to recover this data!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#ea0606",
                        confirmButtonText: "YES, DELETE IT!",
                        cancelButtonText: "CANCEL",
                        closeOnConfirm: false
                    }, function(isConfirm) {
                        if (isConfirm) {
                            document.getElementById(formId).submit();
                        }
                    });
                } else {
                    swal.showInputError("Password salah!");
                    return false;
                }
            });
        }

        // Untuk Owner: langsung konfirmasi hapus tanpa password
        function confirmDelete(formId) {
            swal({
                title: "Are you sure want to delete?",
                text: "Your will not be able to recover this data!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ea0606",
                confirmButtonText: "YES, DELETE IT!",
                cancelButtonText: "CANCEL",
                closeOnConfirm: false
            }, function(isConfirm) {
                if (isConfirm) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
    @endpush
@endsection


@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('purchase')
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
        @if(session('error'))
        <div class="alert alert-danger text-white" role="alert">
            {{ session('error') }}
        </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-12 col-xl-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>New Purchase Transaction</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('purchase.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">No Nota</label>
                                    <input type="text" class="form-control" name="no_nota" placeholder="Enter Invoice Number" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tanggal Nota</label>
                                    <input type="date" class="form-control" name="tgl_nota" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Distributor</label>
                                    <select class="form-control" name="id_distributor" required>
                                        <option value="">Pilih Distributor</option>
                                        @foreach($distributors as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_distributor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <hr class="horizontal dark my-3">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Items list</h6>
                            
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Buy Price</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Margin (%)</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Subtotal</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <!-- Rows added by JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end font-weight-bold">Grand Total</td>
                                            <td class="font-weight-bold" id="grandTotal">Rp 0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-3 mb-3">
                                <button type="button" class="btn btn-sm btn-info mb-0" onclick="addItem()">+ Add Item</button>
                            </div>

                            <div class="text-end mt-4">
                                <a href="{{ route('purchase.index') }}" class="btn bg-gradient-secondary me-3">Cancel</a>
                                <button type="submit" class="btn bg-gradient-primary">Process Purchase</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const products = @json($products);
        
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
        }
        
        function addItem() {
            const tableBody = document.getElementById('itemsBody');
            const rowCount = tableBody.rows.length;
            const row = tableBody.insertRow(rowCount);
            
            let productOptions = '<option value="">Select Product</option>';
            products.forEach(p => {
                productOptions += `<option value="${p.id}">${p.nama_barang}</option>`;
            });
            
            row.innerHTML = `
                <td>
                    <select class="form-control form-control-sm" name="products[]" required>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm buy-price-input" name="buy_prices[]" min="0" value="0" required onchange="calculateSubtotal(this)" onkeyup="calculateSubtotal(this)">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm margin-input" name="margins[]" min="0" value="0" required>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input" name="quantities[]" min="1" value="1" required onchange="calculateSubtotal(this)" onkeyup="calculateSubtotal(this)">
                </td>
                <td class="subtotal-label text-sm font-weight-bold">Rp 0</td>
                <td>
                    <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="removeItem(this)">
                        <i class="far fa-trash-alt me-2"></i>Delete
                    </button>
                </td>
            `;
        }
        
        function calculateSubtotal(element) {
            const row = element.closest('tr');
            const price = parseFloat(row.querySelector('.buy-price-input').value) || 0;
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            
            const subtotal = price * qty;
            row.querySelector('.subtotal-label').textContent = formatRupiah(subtotal);
            
            updateGrandTotal();
        }
        
        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('#itemsBody tr').forEach(row => {
                const price = parseFloat(row.querySelector('.buy-price-input').value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                total += price * qty;
            });
            document.getElementById('grandTotal').textContent = formatRupiah(total);
        }
        
        function removeItem(button) {
            button.closest('tr').remove();
            updateGrandTotal();
        }
        
        document.addEventListener('DOMContentLoaded', () => {
             addItem();
        });
    </script>
@endsection

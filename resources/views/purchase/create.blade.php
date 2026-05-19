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
        .form-label {
            color: #a0a0a0;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .form-control, .form-select {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            color: #555;
        }
        .form-control:focus, .form-select:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.25);
        }
        .form-control[readonly] {
            background-color: #e9ecef;
        }
        .item-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .item-badge {
            background-color: #344767;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 15px;
        }
        .text-primary-custom {
            color: #cb0c9f !important;
            font-weight: bold;
        }
        .btn-remove-item {
            border: 1px solid #ea0606;
            background: transparent;
            color: #ea0606;
            border-radius: 8px;
            font-size: 12px;
            padding: 6px 15px;
            transition: all 0.3s;
        }
        .btn-remove-item:hover {
            background: #ea0606;
            color: white;
        }
        .btn-add-item {
            background-color: #344767;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            padding: 8px 20px;
            font-weight: 600;
        }
        .btn-add-item:hover {
            background-color: #2b3a55;
            color: white;
        }
        .total-banner {
            background-color: #344767;
            color: white;
            border-radius: 12px;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .total-banner h5 {
            margin: 0;
            color: white;
            font-weight: 600;
        }
        .total-banner h3 {
            margin: 0;
            color: white;
            font-weight: 800;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-cancel {
            background: white;
            border: 1px solid #ddd;
            color: #777;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }
        .btn-save {
            background-color: #cb0c9f;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }
        .btn-save:hover {
            background-color: #b10a8b;
            color: white;
        }
    </style>

    <div class="container-fluid py-4">
        
        <form action="{{ route('purchase.store') }}" method="POST" id="purchaseForm">
            @csrf
            
            <!-- Header -->
            <div class="header-box mb-4">
                <div class="header-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div>
                    <h5 class="mb-1" style="font-weight: 700; color: #444;">Add New Purchase</h5>
                    <p class="mb-0 text-muted" style="font-size: 13px;">Isi data pembelian dan item produk di bawah ini</p>
                </div>
            </div>

            <div class="pos-card mb-4">
                <!-- Info Nota -->
                <div class="section-title">INFO NOTA</div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">NO NOTA</label>
                        <input type="text" class="form-control" name="no_nota" placeholder="Contoh: NTB-001" value="[Auto Generated]">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">TANGGAL NOTA</label>
                        <input type="date" class="form-control" name="tgl_nota" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">DISTRIBUTOR</label>
                        <select class="form-select" name="id_distributor" required>
                            <option value="">-- Pilih Distributor --</option>
                            @foreach($distributors as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_distributor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Purchase Items -->
                <div class="section-title mt-5">PURCHASE ITEMS</div>
                <div id="itemsContainer">
                    <!-- Item cards will be injected here by JS -->
                </div>

                <div class="mt-3">
                    <button type="button" class="btn-add-item" onclick="addItem()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
            </div>

            <!-- Total Bayar -->
            <div class="pos-card">
                <div class="total-banner shadow-sm">
                    <h5>TOTAL BAYAR</h5>
                    <h3 id="grandTotal">Rp 0</h3>
                </div>
                
                <div class="action-buttons">
                    <a href="{{ route('purchase.index') }}" class="btn-cancel"><i class="fas fa-times me-1"></i> Cancel</a>
                    <button type="submit" class="btn-save"><i class="fas fa-check me-1"></i> Save Purchase</button>
                </div>
            </div>

        </form>
    </div>

    <script>
        const products = @json($products);
        let itemCount = 0;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function createProductOptions() {
            let options = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                options += `<option value="${p.id}" data-stok="${p.stok}" data-harga="${p.harga_jual}">${p.nama_barang}</option>`;
            });
            return options;
        }

        function addItem() {
            itemCount++;
            const container = document.getElementById('itemsContainer');
            const row = document.createElement('div');
            row.className = 'item-card';
            row.id = `item-row-${itemCount}`;
            row.innerHTML = `
                <div class="item-badge">ITEM #${itemCount}</div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">PRODUCT</label>
                        <select class="form-select product-select" name="products[]" required onchange="onProductSelect(${itemCount})">
                            ${createProductOptions()}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PURCHASE PRICE</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">Rp</span>
                            <input type="number" class="form-control border-start-0 ps-0 purchase-price" name="buy_prices[]" value="0" min="0" required oninput="calculateRow(${itemCount})">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">STOCK</label>
                        <input type="text" class="form-control bg-light stock-input" value="-" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SELLING PRICE</label>
                        <input type="text" class="form-control bg-light text-primary-custom selling-price-disp" value="Rp 0" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">SELLING MARGIN</label>
                        <input type="number" class="form-control margin-input" name="margins[]" value="0" min="0" oninput="calculateRow(${itemCount})">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">QUANTITY</label>
                        <input type="number" class="form-control qty-input" name="quantities[]" value="1" min="1" required oninput="calculateRow(${itemCount})">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">SUB TOTAL</label>
                        <input type="text" class="form-control bg-light text-primary-custom subtotal-input" value="Rp 0" readonly>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn-remove-item" onclick="removeItem(${itemCount})">
                        <i class="fas fa-times me-1"></i> Remove Item
                    </button>
                </div>
            `;
            container.appendChild(row);
        }

        function onProductSelect(index) {
            const row = document.getElementById(`item-row-${index}`);
            const select = row.querySelector('.product-select');
            const stockInput = row.querySelector('.stock-input');
            const selectedOpt = select.options[select.selectedIndex];
            
            if(selectedOpt.value !== "") {
                stockInput.value = selectedOpt.getAttribute('data-stok');
                // The current selling price of the product is already in the DB.
                // We might just display it, but selling margin can adjust it.
            } else {
                stockInput.value = "-";
            }
            calculateRow(index);
        }

        function calculateRow(index) {
            const row = document.getElementById(`item-row-${index}`);
            if(!row) return;

            const purchasePrice = parseFloat(row.querySelector('.purchase-price').value) || 0;
            const margin = parseFloat(row.querySelector('.margin-input').value) || 0;
            const qty = parseFloat(row.querySelector('.qty-input').value) || 1;

            const sellingPrice = purchasePrice + margin;
            const subtotal = purchasePrice * qty;

            row.querySelector('.selling-price-disp').value = formatRupiah(sellingPrice);
            row.querySelector('.subtotal-input').value = formatRupiah(subtotal);

            updateGrandTotal();
        }

        function removeItem(index) {
            const row = document.getElementById(`item-row-${index}`);
            if(row) {
                row.remove();
                updateGrandTotal();
                reindexItems();
            }
        }

        function reindexItems() {
            const container = document.getElementById('itemsContainer');
            const badges = container.querySelectorAll('.item-badge');
            badges.forEach((badge, idx) => {
                badge.innerText = `ITEM #${idx + 1}`;
            });
            itemCount = badges.length; // Ensure item count stays aligned if we want
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            const rows = document.querySelectorAll('.item-card');
            rows.forEach(row => {
                const purchasePrice = parseFloat(row.querySelector('.purchase-price').value) || 0;
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                grandTotal += (purchasePrice * qty);
            });
            document.getElementById('grandTotal').innerText = formatRupiah(grandTotal);
        }

        // Add 1 default item on load
        document.addEventListener('DOMContentLoaded', () => {
            addItem();
        });

    </script>
@endsection

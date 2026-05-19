@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('order')
    <style>
        .pos-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: white;
        }
        .item-row:hover {
            background-color: #f8f9fa;
        }
        .qty-input {
            width: 70px !important;
            text-align: center;
            border-radius: 8px !important;
        }
        .search-results {
            position: absolute;
            width: 100%;
            z-index: 1000;
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            max-height: 400px;
            overflow-y: auto;
            display: none;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .search-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f1f1f1;
        }
        .search-item:last-child {
            border-bottom: none;
        }
        .search-item:hover {
            background-color: #f8f9fa;
            padding-left: 25px;
        }
        .sidebar-sticky {
            position: sticky;
            top: 20px;
        }
        .btn-qty {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px !important;
        }
        .cart-empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        .cart-empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
    @endpush

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="font-weight-bolder mb-0">Customer Booking</h4>
                <p class="text-sm mb-0">Manage and Track Customer Orders</p>
            </div>
            <a href="{{ route('order.index') }}" class="btn bg-gradient-info btn-sm mb-0 shadow-sm">
                <i class="fas fa-list me-2"></i> ORDER HISTORY
            </a>
        </div>

        <form action="{{ route('order.store') }}" method="POST" id="orderForm">
            @csrf
            <div class="row">
                <!-- Left Column: Product Search & Cart -->
                <div class="col-lg-8">
                    <div class="card pos-card mb-4">
                        <div class="card-body p-4">
                            <!-- Global Product Search -->
                            <div class="position-relative mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-control-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Add Products to Order</label>
                                    <button type="button" class="btn btn-link text-info text-xs p-0 mb-1" onclick="startScanner()">
                                        <i class="fas fa-camera me-1"></i> Scan Barcode
                                    </button>
                                </div>
                                <div class="input-group input-group-lg border-radius-lg border">
                                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-info"></i></span>
                                    <input type="text" class="form-control bg-transparent border-0 ps-0" id="globalSearch" placeholder="Search by product name..." autocomplete="off">
                                </div>
                                <div id="searchResults" class="search-results">
                                    <!-- Search results will appear here -->
                                </div>

                                <!-- Scanner Area (Hidden by default) -->
                                <div id="reader" style="display: none; border-radius: 12px; overflow: hidden; margin-top: 15px; border: 1px solid #eee;"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-0">Ordered Items</h6>
                                <span class="badge bg-light text-dark border-radius-sm" id="itemCount">0 Items</span>
                            </div>

                            <div class="table-responsive" style="min-height: 300px;">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Price</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Qty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Subtotal</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartBody">
                                        <!-- Items added here -->
                                    </tbody>
                                </table>
                                <div id="emptyCart" class="cart-empty-state">
                                    <i class="fas fa-cart-plus"></i>
                                    <h5>No products added yet</h5>
                                    <p class="text-secondary text-sm">Select products above to build the customer order.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary & Info -->
                <div class="col-lg-4">
                    <div class="sidebar-sticky">
                        <div class="card pos-card mb-4 border-start border-info border-5">
                            <div class="card-header pb-0 bg-transparent">
                                <h6 class="mb-0">Order Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">No Order</label>
                                    <input type="text" class="form-control bg-light" name="no_order" value="[Auto Generated]" readonly>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">Tanggal Pemesanan</label>
                                    <input type="date" class="form-control" name="tgl_pemesanan" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">Pelanggan</label>
                                    <select class="form-control" name="id_pelanggan" required>
                                        <option value="">Pilih Pelanggan</option>
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}">{{ $c->nama_pelanggan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">Status Order</label>
                                    <select class="form-control" name="status_pemesanan" required>
                                        <option value="draft">Draft</option>
                                        <option value="dipesan">Dipesan (Pending)</option>
                                        <option value="diproses">Diproses (Processing)</option>
                                        <option value="selesai">Selesai (Completed)</option>
                                        <option value="dibatalkan penjual">Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">Pembayaran</label>
                                    <select class="form-control" name="metode_pembayaran" required>
                                        <option value="cod">COD (Cash)</option>
                                        <option value="tf">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-control-label text-xs">Catatan</label>
                                    <textarea class="form-control" name="status_catatan" rows="2" placeholder="Order notes..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card pos-card bg-gradient-info text-white overflow-hidden shadow-lg">
                            <div class="card-body p-4 position-relative">
                                <div class="mb-4 text-center">
                                    <span class="text-sm opacity-8">Total Order Value</span>
                                    <h2 class="text-white font-weight-bolder mb-0" id="grandTotal">Rp 0</h2>
                                </div>
                                <button type="submit" class="btn btn-white w-100 btn-lg mb-0 text-info font-weight-bold shadow-sm" style="border-radius: 12px;">
                                    PLACE ORDER <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const products = @json($products);
        let cart = [];

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        // Global Search Logic
        const searchInput = document.getElementById('globalSearch');
        const searchResults = document.getElementById('searchResults');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            if (query.length < 1) {
                searchResults.style.display = 'none';
                return;
            }

            const filtered = products.filter(p => 
                p.nama_barang.toLowerCase().includes(query) || 
                (p.kd_barang && p.kd_barang.toLowerCase().includes(query))
            );

            if (filtered.length > 0) {
                let html = '';
                filtered.forEach(p => {
                    html += `
                        <div class="search-item d-flex justify-content-between align-items-center" onclick="addToCart(${p.id})">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-3 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-box text-xs opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">${p.nama_barang}</h6>
                                    <small class="text-xs text-muted">Stock: ${p.stok} | Price: ${formatRupiah(p.harga_jual)}</small>
                                </div>
                            </div>
                            <span class="badge bg-light text-info text-xxs">Select <i class="fas fa-plus"></i></span>
                        </div>
                    `;
                });
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div class="p-4 text-center text-xs text-muted">No products found.</div>';
                searchResults.style.display = 'block';
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });

        function addToCart(productId) {
            const product = products.find(p => p.id == productId);
            const existing = cart.find(item => item.id == productId);

            if (existing) {
                existing.qty++;
            } else {
                cart.push({
                    id: product.id,
                    name: product.nama_barang,
                    price: product.harga_jual,
                    stock: product.stok,
                    qty: 1
                });
            }

            searchInput.value = '';
            searchResults.style.display = 'none';
            renderCart();
        }

        function renderCart() {
            const body = document.getElementById('cartBody');
            const empty = document.getElementById('emptyCart');
            const countLabel = document.getElementById('itemCount');
            
            if (cart.length === 0) {
                body.innerHTML = '';
                empty.style.display = 'block';
                countLabel.textContent = '0 Items';
                updateGrandTotal();
                return;
            }

            empty.style.display = 'none';
            countLabel.textContent = `${cart.length} Items`;
            
            let html = '';
            cart.forEach((item) => {
                const subtotal = item.price * item.qty;
                html += `
                    <tr class="item-row">
                        <td>
                            <div class="d-flex px-2 py-2">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${item.name}</h6>
                                    <p class="text-xs text-secondary mb-0">Stock: ${item.stock}</p>
                                    <input type="hidden" name="products[]" value="${item.id}">
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-sm font-weight-bold">${formatRupiah(item.price)}</span>
                        </td>
                        <td class="align-middle text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-qty btn-outline-secondary mb-0" onclick="updateQty(${item.id}, -1)">-</button>
                                <input type="number" name="quantities[]" class="form-control form-control-sm qty-input mx-2" value="${item.qty}" min="1" onchange="updateItemData(${item.id}, this.value)">
                                <button type="button" class="btn btn-qty btn-outline-secondary mb-0" onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                        </td>
                        <td class="align-middle text-end">
                            <span class="text-sm font-weight-bold">${formatRupiah(subtotal)}</span>
                        </td>
                        <td class="align-middle text-center">
                            <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="removeFromCart(${item.id})">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            updateGrandTotal();
        }

        function updateItemData(id, value) {
            const item = cart.find(i => i.id == id);
            if (item) {
                item.qty = Math.max(1, parseInt(value) || 1);
                renderCart();
            }
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id == id);
            if (item) {
                item.qty = Math.max(1, item.qty + delta);
                renderCart();
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id != id);
            renderCart();
        }

        function updateGrandTotal() {
            let total = 0;
            cart.forEach(item => {
                total += item.price * item.qty;
            });
            document.getElementById('grandTotal').textContent = formatRupiah(total);
        }

        // BARCODE SCANNER LOGIC
        let html5QrCode = null;

        function startScanner() {
            const readerDiv = document.getElementById('reader');
            
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = 'none';
                    html5QrCode = null;
                }).catch((err) => {
                    console.error("Error stopping scanner", err);
                });
                return;
            }

            readerDiv.style.display = 'block';
            html5QrCode = new Html5Qrcode("reader");
            
            const config = { 
                fps: 20, 
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    return { width: viewfinderWidth * 0.8, height: viewfinderHeight * 0.4 };
                },
                aspectRatio: 1.0,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .catch((err) => {
                    console.error("Error starting scanner", err);
                    alert("Gagal mengakses kamera.");
                    readerDiv.style.display = 'none';
                    html5QrCode = null;
                });
        }

        function onScanSuccess(decodedText, decodedResult) {
            const product = products.find(p => p.kd_barang === decodedText);
            
            if (product) {
                addToCart(product.id);
                
                const searchInput = document.getElementById('globalSearch');
                const originalPlaceholder = searchInput.placeholder;
                searchInput.placeholder = "Added: " + product.nama_barang;
                searchInput.classList.add('border-success');
                
                setTimeout(() => {
                    searchInput.placeholder = originalPlaceholder;
                    searchInput.classList.remove('border-success');
                }, 2000);
            }
        }
    </script>
@endsection

@extends('be.master')

@section('menu')
    @include('be.menu')
@endsection

@section('sale')
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
        .promo-badge {
            transition: all 0.3s;
            cursor: pointer;
            border-radius: 8px !important;
        }
        .promo-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
                <h4 class="font-weight-bolder mb-0">Madura Mart POS</h4>
                <p class="text-sm mb-0">Efficient Sales & Inventory Management</p>
            </div>
            <a href="{{ route('sale.index') }}" class="btn bg-gradient-info btn-sm mb-0 shadow-sm">
                <i class="fas fa-history me-2"></i> TRANSACTION HISTORY
            </a>
        </div>

        <form action="{{ route('sale.store') }}" method="POST" id="saleForm">
            @csrf
            <div class="row">
                <!-- Left Column: Product Search & Cart -->
                <div class="col-lg-8">
                    <div class="card pos-card mb-4">
                        <div class="card-body p-4">
                            <!-- Global Product Search -->
                            <div class="position-relative mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-control-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Search Product</label>
                                    <button type="button" class="btn btn-link text-primary text-xs p-0 mb-1" onclick="startScanner()">
                                        <i class="fas fa-camera me-1"></i> Scan Barcode
                                    </button>
                                </div>
                                <div class="input-group input-group-lg border-radius-lg border">
                                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-primary"></i></span>
                                    <input type="text" class="form-control bg-transparent border-0 ps-0" id="globalSearch" placeholder="Type product name or scan barcode..." autocomplete="off">
                                </div>
                                <div id="searchResults" class="search-results">
                                    <!-- Search results will appear here -->
                                </div>

                                <!-- Scanner Area (Hidden by default) -->
                                <div id="reader" style="display: none; border-radius: 12px; overflow: hidden; margin-top: 15px; border: 1px solid #eee;"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-0">Shopping Cart</h6>
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
                                        <!-- Cart items added here -->
                                    </tbody>
                                </table>
                                <div id="emptyCart" class="cart-empty-state">
                                    <i class="fas fa-shopping-basket"></i>
                                    <h5>Your cart is empty</h5>
                                    <p class="text-secondary text-sm">Find products using the search bar above to start a transaction.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card pos-card">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Available Promotions</h6>
                            <div id="applicableDiscounts" class="d-flex flex-wrap gap-2">
                                <p class="text-muted text-xs ms-2">Add items to see applicable discounts.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Summary & Receipt Info -->
                <div class="col-lg-4">
                    <div class="sidebar-sticky">
                        <div class="card pos-card mb-4 border-start border-primary border-5">
                            <div class="card-header pb-0 bg-transparent">
                                <h6 class="mb-0">Invoice Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label class="form-control-label text-xs">Receipt Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hashtag text-xs"></i></span>
                                        <input type="text" class="form-control bg-light" name="no_struk" value="[Auto Generated]" readonly>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-control-label text-xs">Transaction Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar text-xs"></i></span>
                                        <input type="date" class="form-control" name="tgl_jual" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card pos-card bg-gradient-dark text-white overflow-hidden shadow-lg">
                            <div class="card-body p-4 position-relative">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-sm opacity-8">Subtotal</span>
                                    <span class="text-sm font-weight-bold" id="labelSubtotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-sm opacity-8">Discount</span>
                                    <span class="text-sm font-weight-bold text-success" id="labelDiscount">- Rp 0</span>
                                    <input type="hidden" name="id_diskon" id="inputDiscountId">
                                    <input type="hidden" name="total_diskon" id="inputTotalDiscount" value="0">
                                </div>
                                <hr class="horizontal light my-3">
                                <div class="d-flex justify-content-between mb-4">
                                    <h4 class="text-white mb-0">Grand Total</h4>
                                    <h4 class="text-white font-weight-bolder mb-0" id="grandTotal">Rp 0</h4>
                                </div>
                                <button type="submit" class="btn btn-white w-100 btn-lg mb-0 text-dark font-weight-bold shadow-sm" style="border-radius: 12px; font-size: 1rem;">
                                    FINALIZE SALE <i class="fas fa-check-circle ms-2"></i>
                                </button>
                                <p class="text-center text-xs opacity-6 mt-3 mb-0">Ensure all items are correct before processing.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const products = @json($products);
        const allDiscounts = @json($discounts);
        let cart = [];
        let appliedDiscount = null;

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
                p.kd_barang.toLowerCase().includes(query)
            );

            if (filtered.length > 0) {
                let html = '';
                filtered.forEach(p => {
                    html += `
                        <div class="search-item d-flex justify-content-between align-items-center" onclick="addToCart(${p.id})">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-3 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-box text-xs opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">${p.nama_barang}</h6>
                                    <small class="text-xs text-muted">${p.kd_barang} | Stock: ${p.stok}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-sm">${formatRupiah(p.harga_jual)}</h6>
                                <span class="badge bg-light text-primary text-xxs">Add Item <i class="fas fa-plus"></i></span>
                            </div>
                        </div>
                    `;
                });
                searchResults.innerHTML = html;
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div class="p-4 text-center text-xs text-muted"><i class="fas fa-search mb-2 d-block" style="font-size: 2rem; opacity: 0.2;"></i> No products found matching your search.</div>';
                searchResults.style.display = 'block';
            }
        });

        // Close search results when clicking outside
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
                updateTotals();
                return;
            }

            empty.style.display = 'none';
            countLabel.textContent = `${cart.length} Items`;
            
            let html = '';
            cart.forEach((item, index) => {
                const subtotal = item.price * item.qty;
                html += `
                    <tr class="item-row">
                        <td>
                            <div class="d-flex px-2 py-2">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${item.name}</h6>
                                    <p class="text-xs text-secondary mb-0">Available Stock: ${item.stock}</p>
                                    <input type="hidden" name="products[]" value="${item.id}">
                                </div>
                            </div>
                        </td>
                        <td class="align-middle text-center">
                            <span class="text-sm font-weight-bold">${formatRupiah(item.price)}</span>
                        </td>
                        <td class="align-middle text-center">
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button" class="btn btn-qty btn-outline-secondary mb-0" onclick="updateQty(${item.id}, -1)">
                                    <i class="fas fa-minus text-xxs"></i>
                                </button>
                                <input type="number" name="quantities[]" class="form-control form-control-sm qty-input mx-2" value="${item.qty}" min="1" onchange="setQty(${item.id}, this.value)">
                                <button type="button" class="btn btn-qty btn-outline-secondary mb-0" onclick="updateQty(${item.id}, 1)">
                                    <i class="fas fa-plus text-xxs"></i>
                                </button>
                            </div>
                        </td>
                        <td class="align-middle text-end">
                            <span class="text-sm font-weight-bold">${formatRupiah(subtotal)}</span>
                        </td>
                        <td class="align-middle">
                            <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="removeFromCart(${item.id})">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            body.innerHTML = html;
            updateTotals();
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id == id);
            if (item) {
                item.qty = Math.max(1, item.qty + delta);
                renderCart();
            }
        }

        function setQty(id, val) {
            const item = cart.find(i => i.id == id);
            if (item) {
                item.qty = Math.max(1, parseInt(val) || 1);
                renderCart();
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id != id);
            renderCart();
        }

        function updateTotals() {
            let subtotal = 0;
            const itemsForDiscount = [];
            cart.forEach(item => {
                subtotal += item.price * item.qty;
                itemsForDiscount.push({ id: item.id, qty: item.qty });
            });

            document.getElementById('labelSubtotal').textContent = formatRupiah(subtotal);
            
            // Promo check
            updateApplicableDiscounts(subtotal, itemsForDiscount);
            
            let discountValue = 0;
            if (appliedDiscount) {
                if (appliedDiscount.tipe_diskon === 'percentage') {
                    discountValue = (subtotal * appliedDiscount.nilai_diskon) / 100;
                    if (appliedDiscount.max_diskon && discountValue > appliedDiscount.max_diskon) {
                        discountValue = appliedDiscount.max_diskon;
                    }
                } else {
                    discountValue = parseFloat(appliedDiscount.nilai_diskon);
                }
                if (discountValue > subtotal) discountValue = subtotal;
            }

            document.getElementById('labelDiscount').textContent = '- ' + formatRupiah(discountValue);
            document.getElementById('inputTotalDiscount').value = discountValue;
            document.getElementById('grandTotal').textContent = formatRupiah(subtotal - discountValue);
        }

        function updateApplicableDiscounts(subtotal, items) {
            const container = document.getElementById('applicableDiscounts');
            const applicable = allDiscounts.filter(d => {
                if (subtotal < d.min_transaksi) return false;
                if (d.id_barang) {
                    const item = items.find(i => i.id == d.id_barang);
                    if (!item || item.qty < d.min_qty) return false;
                }
                return true;
            });

            if (applicable.length === 0) {
                container.innerHTML = '<p class="text-muted text-xs ms-2">Add more items to unlock discounts.</p>';
                appliedDiscount = null;
                document.getElementById('inputDiscountId').value = '';
                return;
            }

            let html = '';
            applicable.forEach(d => {
                const isActive = appliedDiscount && appliedDiscount.id === d.id;
                const color = isActive ? 'bg-gradient-success' : 'bg-gradient-primary';
                const text = d.tipe_diskon === 'percentage' ? `${d.nilai_diskon}%` : formatRupiah(d.nilai_diskon);
                
                html += `
                    <div class="badge promo-badge ${color} border-0 p-2 mb-2" onclick="applyDiscount(${d.id})">
                        <i class="fas fa-tag me-1"></i> ${d.nama_diskon} (${text})
                        ${isActive ? '<i class="fas fa-check ms-1"></i>' : ''}
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function applyDiscount(id) {
            if (appliedDiscount && appliedDiscount.id === id) {
                appliedDiscount = null;
                document.getElementById('inputDiscountId').value = '';
            } else {
                appliedDiscount = allDiscounts.find(d => d.id === id);
                document.getElementById('inputDiscountId').value = id;
            }
            updateTotals();
        }

        // BARCODE SCANNER LOGIC
        let html5QrCode = null;

        function startScanner() {
            const readerDiv = document.getElementById('reader');
            
            if (html5QrCode) {
                // If already running, stop it
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

            // Enhanced camera constraints for better focus
            const cameraConfig = { 
                facingMode: "environment",
                focusMode: "continuous",
                advanced: [{ zoom: 2.0 }] // Try to zoom a bit for small barcodes
            };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .catch((err) => {
                    console.error("Error starting scanner", err);
                    alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan.");
                    readerDiv.style.display = 'none';
                    html5QrCode = null;
                });
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Find product by kd_barang
            const product = products.find(p => p.kd_barang === decodedText);
            
            if (product) {
                // Play a success sound if you want, or just feedback
                addToCart(product.id);
                
                // Show temporary toast or feedback
                const searchInput = document.getElementById('globalSearch');
                const originalPlaceholder = searchInput.placeholder;
                searchInput.placeholder = "Added: " + product.nama_barang;
                searchInput.classList.add('border-success');
                
                setTimeout(() => {
                    searchInput.placeholder = originalPlaceholder;
                    searchInput.classList.remove('border-success');
                }, 2000);

                // Stop scanner after success to save resources, or keep it running for multiple items
                // html5QrCode.stop(); 
                // readerDiv.style.display = 'none';
            } else {
                console.warn("Product with barcode " + decodedText + " not found.");
            }
        }
    </script>
@endsection

// js/pos.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Application State ---
    let state = {
        products: [],
        categories: [],
        customers: [],
        reps: [],
        cart: [], // Array of { product_id, quantity }
        pricingMode: 'retail', // 'retail' or 'reseller' or 'special'
        activeCategory: 'All',
        activeAudience: 'All',
        activeSize: 'All',
        searchQuery: '',
        paymentMode: 'Cash',
        paymentStatus: 'Paid',
        discount: 0.00,
        customerSpecialPrices: {} // maps product_id to special price
    };

    function getProductPrice(p) {
        if (state.pricingMode === 'special') {
            const key = `${p.category_id}_${p.size_id}`;
            if (state.customerSpecialPrices && state.customerSpecialPrices[key] !== undefined) {
                return parseFloat(state.customerSpecialPrices[key]);
            }
            return parseFloat(p.retail_price);
        }
        return state.pricingMode === 'reseller' ? parseFloat(p.reseller_price) : parseFloat(p.retail_price);
    }

    // --- DOM Elements ---
    const productsGrid = document.getElementById('productsGrid');
    const categoryTabs = document.getElementById('categoryTabs');
    const searchInput = document.getElementById('searchInput');
    const audienceBtns = document.querySelectorAll('.audience-btn');
    const repSelect = document.getElementById('repSelect');
    const customerSelect = document.getElementById('customerSelect');
    const cartItemsList = document.getElementById('cartItemsList');
    const summarySubtotal = document.getElementById('summarySubtotal');
    const discountInput = document.getElementById('discountInput');
    const summaryTotal = document.getElementById('summaryTotal');
    const paymentChips = document.querySelectorAll('.payment-chip');
    const checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const toast = document.getElementById('notificationToast');
    const tierSpecialBtn = document.getElementById('tierSpecial');
    const statusPaid = document.getElementById('statusPaid');
    const statusUnpaid = document.getElementById('statusUnpaid');
    const paymentModesSection = document.getElementById('paymentModesSection');

    // Modals
    const addRepModal = document.getElementById('addRepModal');
    const addCustomerModal = document.getElementById('addCustomerModal');
    const addRepBtn = document.getElementById('addRepBtn');
    const addCustomerBtn = document.getElementById('addCustomerBtn');
    const saveRepSubmitBtn = document.getElementById('saveRepSubmitBtn');
    const saveCustomerSubmitBtn = document.getElementById('saveCustomerSubmitBtn');
    const newRepName = document.getElementById('newRepName');
    const newCustName = document.getElementById('newCustName');
    const newCustContact = document.getElementById('newCustContact');
    const newCustEmail = document.getElementById('newCustEmail');

    // Pricing Tier Buttons
    const tierRetailBtn = document.getElementById('tierRetail');
    const tierResellerBtn = document.getElementById('tierReseller');

    // --- Initial Load ---
    initTheme();
    loadCatalog();
    loadReps();
    loadCustomers();
    setupEventListeners();

    // --- Theme System ---
    function initTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark');
            updateThemeButton(true);
        } else {
            updateThemeButton(false);
        }
    }

    function toggleTheme() {
        const isDark = document.body.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateThemeButton(isDark);
    }

    function updateThemeButton(isDark) {
        if (isDark) {
            themeToggleBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
            `;
        } else {
            themeToggleBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            `;
        }
    }

    // --- Alerts/Toast ---
    function showToast(message, isError = false) {
        toast.textContent = message;
        toast.style.backgroundColor = isError ? '#ff3b30' : 'var(--accent-color)';
        toast.style.color = isError ? '#ffffff' : 'var(--accent-text)';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // --- Loading Data ---
    function loadCatalog() {
        productsGrid.innerHTML = `
            <div class="loading-state">
                <div class="spinner"></div>
                <p>Curating catalog...</p>
            </div>
        `;

        fetch('../apis/pos/get_products.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.products = res.data;

                    // Extract unique categories dynamically
                    const catMap = {};
                    res.data.forEach(p => {
                        catMap[p.category_id] = p.category_name;
                    });
                    state.categories = Object.keys(catMap).map(id => ({
                        category_id: id,
                        category_name: catMap[id]
                    }));

                    renderCategoryTabs();
                    renderSizeFilter();
                    renderCatalog();
                } else {
                    showToast('Failed to load products: ' + res.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Failed to connect to server', true);
            });
    }

    function loadReps() {
        fetch('../apis/pos/get_reps.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.reps = res.data;
                    renderRepsSelect();
                }
            });
    }

    function loadCustomers() {
        fetch('../apis/pos/get_customers.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.customers = res.data;
                    renderCustomersSelect();
                }
            });
    }

    // --- Renderers ---
    function renderRepsSelect(selectedId = '') {
        repSelect.innerHTML = '<option value="">Select Representative</option>';
        state.reps.forEach(rep => {
            const opt = document.createElement('option');
            opt.value = rep.rep_id;
            opt.textContent = rep.rep_name;
            if (rep.rep_id == selectedId) {
                opt.selected = true;
            }
            repSelect.appendChild(opt);
        });
    }

    function renderCustomersSelect(selectedId = '') {
        customerSelect.innerHTML = '<option value="">Select Customer</option>';
        state.customers.forEach(cust => {
            const opt = document.createElement('option');
            opt.value = cust.customer_id;
            opt.textContent = cust.customer_name + (cust.contact_number ? ` (${cust.contact_number})` : '');
            if (cust.customer_id == selectedId) {
                opt.selected = true;
            }
            customerSelect.appendChild(opt);
        });
    }

    function renderCategoryTabs() {
        // Keep "All" tab and append others
        categoryTabs.innerHTML = `<button class="category-tab ${state.activeCategory === 'All' ? 'active' : ''}" data-category="All">All Categories</button>`;
        state.categories.forEach(cat => {
            const btn = document.createElement('button');
            btn.className = `category-tab ${state.activeCategory == cat.category_id ? 'active' : ''}`;
            btn.setAttribute('data-category', cat.category_id);
            btn.textContent = cat.category_name;
            categoryTabs.appendChild(btn);
        });
    }

    function renderSizeFilter() {
        const sizeFilter = document.getElementById('sizeFilter');
        if (!sizeFilter) return;

        // Extract unique sizes dynamically
        const sizeMap = {};
        state.products.forEach(p => {
            if (p.size_id && p.volume_ml) {
                sizeMap[p.size_id] = p.volume_ml;
            }
        });

        sizeFilter.innerHTML = '<option value="All">All Sizes</option>';
        Object.keys(sizeMap).forEach(sizeId => {
            const opt = document.createElement('option');
            opt.value = sizeId;
            opt.textContent = `${sizeMap[sizeId]} ml`;
            sizeFilter.appendChild(opt);
        });

        // Restore active size selection
        sizeFilter.value = state.activeSize || 'All';
    }

    function renderCatalog() {
        // Filter products based on search, audience, category, and size
        const filtered = state.products.filter(p => {
            const matchesSearch = p.scent_name.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
                p.category_name.toLowerCase().includes(state.searchQuery.toLowerCase());

            const matchesAudience = state.activeAudience === 'All' || p.target_audience === state.activeAudience;

            const matchesCategory = state.activeCategory === 'All' || p.category_id == state.activeCategory;

            const matchesSize = state.activeSize === 'All' || p.size_id == state.activeSize;

            return matchesSearch && matchesAudience && matchesCategory && matchesSize;
        });

        if (filtered.length === 0) {
            productsGrid.innerHTML = `
                <div class="loading-state">
                    <p>No perfumes match your current filters.</p>
                </div>
            `;
            return;
        }

        productsGrid.innerHTML = '';
        filtered.forEach(p => {
            const card = document.createElement('div');
            const price = getProductPrice(p);
            const isOutOfStock = parseInt(p.stock) <= 0;

            card.className = `product-card ${isOutOfStock ? 'out-of-stock' : ''}`;
            card.innerHTML = `
                <div class="card-top">
                    <span class="card-audience-badge">${p.target_audience}</span>
                    <span class="card-stock ${parseInt(p.stock) <= 5 && !isOutOfStock ? 'low-stock' : ''}">
                        ${isOutOfStock ? 'OUT OF STOCK' : `Stock: ${p.stock}`}
                    </span>
                </div>
                <div class="card-middle">
                    <h3 class="card-title">${p.scent_name}</h3>
                    <div class="card-meta">${p.volume_ml}ml • ${p.category_name}</div>
                </div>
                <div class="card-bottom">
                    <div class="card-price-area">
                        <span class="card-price-label">${state.pricingMode}</span>
                        <span class="card-price">₱${parseFloat(price).toFixed(2)}</span>
                    </div>
                    <button class="card-add-btn" data-id="${p.product_id}" ${isOutOfStock ? 'disabled' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            `;

            // Card click event (only if not out of stock)
            if (!isOutOfStock) {
                card.addEventListener('click', (e) => {
                    // Prevent button click double triggering
                    if (e.target.closest('.card-add-btn')) return;
                    addToCart(p.product_id);
                });

                const addBtn = card.querySelector('.card-add-btn');
                addBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    addToCart(p.product_id);
                });
            }

            productsGrid.appendChild(card);
        });
    }

    function renderCart() {
        if (state.cart.length === 0) {
            cartItemsList.innerHTML = `
                <div class="empty-cart-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="48" height="48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <p>No items added yet</p>
                </div>
            `;
            calculateTotals();
            return;
        }

        cartItemsList.innerHTML = '';
        state.cart.forEach(item => {
            const p = state.products.find(prod => prod.product_id == item.product_id);
            if (!p) return;

            const unitPrice = getProductPrice(p);
            const itemTotal = parseFloat(unitPrice) * item.quantity;

            const cartRow = document.createElement('div');
            cartRow.className = 'cart-item';
            cartRow.innerHTML = `
                <div class="cart-item-details">
                    <h4 class="cart-item-title">${p.scent_name}</h4>
                    <span class="cart-item-meta">${p.volume_ml}ml • ${p.category_name} (${p.target_audience})</span>
                </div>
                <div class="cart-item-actions">
                    <span class="cart-item-price">₱${itemTotal.toFixed(2)}</span>
                    <div class="quantity-controls">
                        <button class="qty-btn dec-btn" data-id="${p.product_id}">-</button>
                        <span class="qty-val">${item.quantity}</span>
                        <button class="qty-btn inc-btn" data-id="${p.product_id}">+</button>
                    </div>
                </div>
            `;

            // Event Listeners for quantity
            cartRow.querySelector('.dec-btn').addEventListener('click', () => adjustQuantity(p.product_id, -1));
            cartRow.querySelector('.inc-btn').addEventListener('click', () => adjustQuantity(p.product_id, 1));

            cartItemsList.appendChild(cartRow);
        });

        calculateTotals();
        setTimeout(() => {
            const cartContainer = document.querySelector('.cart-items-container');
            if (cartContainer) {
                cartContainer.scrollTop = cartContainer.scrollHeight;
            }
        }, 50);
    }

    // --- Cart Actions ---
    function addToCart(productId) {
        const p = state.products.find(prod => prod.product_id == productId);
        if (!p) return;

        const maxStock = parseInt(p.stock);
        const existing = state.cart.find(item => item.product_id == productId);

        if (existing) {
            if (existing.quantity >= maxStock) {
                showToast(`Cannot add more. Only ${maxStock} in stock.`, true);
                return;
            }
            existing.quantity++;
        } else {
            if (maxStock <= 0) {
                showToast(`Product is out of stock.`, true);
                return;
            }
            state.cart.push({ product_id: productId, quantity: 1 });
        }

        renderCart();
    }

    function adjustQuantity(productId, delta) {
        const item = state.cart.find(item => item.product_id == productId);
        if (!item) return;

        const p = state.products.find(prod => prod.product_id == productId);
        const maxStock = parseInt(p.stock);

        if (delta > 0 && item.quantity >= maxStock) {
            showToast(`Cannot add more. Only ${maxStock} in stock.`, true);
            return;
        }

        item.quantity += delta;
        if (item.quantity <= 0) {
            state.cart = state.cart.filter(i => i.product_id != productId);
        }

        renderCart();
    }

    function calculateTotals() {
        let subtotal = 0;
        state.cart.forEach(item => {
            const p = state.products.find(prod => prod.product_id == item.product_id);
            if (p) {
                const price = getProductPrice(p);
                subtotal += parseFloat(price) * item.quantity;
            }
        });

        const discount = state.discount;
        let total = subtotal - discount;
        if (total < 0) total = 0;

        summarySubtotal.textContent = `₱${subtotal.toFixed(2)}`;
        summaryTotal.textContent = `₱${total.toFixed(2)}`;
    }

    function openWaybillModal(orderId) {
        fetch(`../apis/orders/get_orders.php?order_id=${orderId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const order = res.data.order;
                    const items = res.data.items;

                    const date = new Date(order.order_timestamp || new Date());
                    const dateStr = date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
                        date.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });

                    let rowsHTML = '';
                    let subtotal = 0;

                    items.forEach(item => {
                        const qty = parseInt(item.quantity);
                        const price = parseFloat(item.price_applied);
                        const lineTotal = price * qty;
                        subtotal += lineTotal;

                        rowsHTML += `
                            <tr style="page-break-inside: avoid;">
                                <td>
                                    <div style="font-weight: 700;">${item.scent_name}</div>
                                    <div style="font-size: 7px; color: #555;">${item.target_audience || item.category_name} • ${item.volume_ml}ml</div>
                                </td>
                                <td>\u20b1${price.toFixed(2)}</td>
                                <td style="text-align: center;">${qty}</td>
                                <td style="text-align: right; font-weight: 700;">\u20b1${lineTotal.toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    const discount = parseFloat(order.discount || 0);
                    const grandTotal = Math.max(0, subtotal - discount);

                    const waybillPaper = document.getElementById('waybillPaper');
                    waybillPaper.innerHTML = `
                        <div class="waybill-header">
                            <div class="waybill-title-banner">Invoice</div>
                            <div class="waybill-order-number">
                                Order No.
                                <span>#${String(order.order_id).padStart(6, '0')}</span>
                            </div>
                        </div>

                        <div class="waybill-brand">Bloom &amp; Scentz</div>

                        <div class="waybill-billing">
                            <div class="waybill-billing-title">Billing Details:</div>
                            <div><strong>Customer:</strong> ${order.customer_name}</div>
                            ${order.contact_number ? `<div><strong>Phone:</strong> ${order.contact_number}</div>` : ''}
                            <div><strong>Sales Representative:</strong> ${order.rep_name}</div>
                            <div><strong>Date:</strong> ${dateStr}</div>
                        </div>

                        <table class="waybill-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHTML}
                            </tbody>
                        </table>

                        <div class="waybill-totals" style="page-break-inside: avoid;">
                            <div class="waybill-totals-line">
                                <span>Subtotal:</span>
                                <span>\u20b1${subtotal.toFixed(2)}</span>
                            </div>
                            ${discount > 0 ? `
                            <div class="waybill-totals-line">
                                <span>Discount:</span>
                                <span>-\u20b1${discount.toFixed(2)}</span>
                            </div>
                            ` : ''}
                            <div class="waybill-totals-line grand-total">
                                <span>Total:</span>
                                <span>\u20b1${grandTotal.toFixed(2)}</span>
                            </div>
                        </div>

                        <div class="waybill-footer-banner" style="page-break-inside: avoid;">
                            <span>Facebook: Bloom &amp; Scentz</span>
                            <span>Instagram: @bloom.scentz</span>
                        </div>
                    `;

                    // Open Waybill Modal
                    document.getElementById('waybillModal').classList.add('active');
                } else {
                    showToast('Failed to fetch waybill details: ' + res.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Error loading waybill details', true);
            });
    }

    // --- Event Setup ---
    function setupEventListeners() {
        // Theme Toggle
        themeToggleBtn.addEventListener('click', toggleTheme);

        // Search Input
        searchInput.addEventListener('input', (e) => {
            state.searchQuery = e.target.value;
            renderCatalog();
        });

        // Size Filter
        const sizeFilter = document.getElementById('sizeFilter');
        if (sizeFilter) {
            sizeFilter.addEventListener('change', (e) => {
                state.activeSize = e.target.value;
                renderCatalog();
            });
        }

        // Audience Filters
        audienceBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                audienceBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                state.activeAudience = btn.getAttribute('data-audience');
                renderCatalog();
            });
        });

        // Category Tab clicks (delegation)
        categoryTabs.addEventListener('click', (e) => {
            const tab = e.target.closest('.category-tab');
            if (!tab) return;

            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            state.activeCategory = tab.getAttribute('data-category');
            renderCatalog();
        });

        // Pricing Mode toggles
        tierRetailBtn.addEventListener('click', () => selectPricingTier('retail'));
        tierResellerBtn.addEventListener('click', () => selectPricingTier('reseller'));
        tierSpecialBtn.addEventListener('click', () => {
            if (tierSpecialBtn.disabled) return;
            selectPricingTier('special');
        });

        function selectPricingTier(tier) {
            if (state.pricingMode === tier) return;
            state.pricingMode = tier;

            document.querySelectorAll('.pricing-tier-btn').forEach(btn => {
                if (btn.getAttribute('data-tier') === tier) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
            renderCatalog();
            renderCart();
        }

        // Customer Selection change (loads special prices)
        customerSelect.addEventListener('change', () => {
            const customerId = customerSelect.value;
            if (customerId) {
                fetch(`../apis/people/get_special_prices.php?customer_id=${customerId}`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            state.customerSpecialPrices = {};
                            res.data.prices.forEach(sp => {
                                const key = `${sp.category_id}_${sp.size_id}`;
                                state.customerSpecialPrices[key] = parseFloat(sp.special_price);
                            });

                            const custType = res.data.customer_type;
                            tierSpecialBtn.disabled = (custType !== 'Special');

                            if (custType === 'Reseller') {
                                selectPricingTier('reseller');
                            } else if (custType === 'Special') {
                                selectPricingTier('special');
                            } else {
                                selectPricingTier('retail');
                            }
                        }
                    });
            } else {
                state.customerSpecialPrices = {};
                tierSpecialBtn.disabled = true;
                selectPricingTier('retail');
            }
        });

        // Payment Status toggles
        statusPaid.addEventListener('click', () => {
            if (state.paymentStatus === 'Paid') return;
            state.paymentStatus = 'Paid';
            statusPaid.classList.add('active');
            statusUnpaid.classList.remove('active');
            paymentModesSection.style.display = 'block';
        });

        statusUnpaid.addEventListener('click', () => {
            if (state.paymentStatus === 'Unpaid') return;
            state.paymentStatus = 'Unpaid';
            statusUnpaid.classList.add('active');
            statusPaid.classList.remove('active');
            paymentModesSection.style.display = 'none';
        });

        // Discount input
        discountInput.addEventListener('input', (e) => {
            const val = parseFloat(e.target.value);
            state.discount = isNaN(val) || val < 0 ? 0.00 : val;
            calculateTotals();
        });

        // Payment Mode chip click
        paymentChips.forEach(chip => {
            chip.addEventListener('click', () => {
                paymentChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                state.paymentMode = chip.getAttribute('data-mode');
            });
        });

        // Rep & Customer Modal Triggers
        addRepBtn.addEventListener('click', () => openModal(addRepModal));
        addCustomerBtn.addEventListener('click', () => openModal(addCustomerModal));

        // Modal Closers
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                closeModal(modal);
            });
        });

        // Close modal on escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    closeModal(activeModal);
                }
            }
        });

        // Close modal on clicking backdrop
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        });

        // Modal Action submissions
        saveRepSubmitBtn.addEventListener('click', submitNewRep);
        saveCustomerSubmitBtn.addEventListener('click', submitNewCustomer);

        // Print Waybill Button
        const printWaybillBtn = document.getElementById('printWaybillBtn');
        if (printWaybillBtn) {
            printWaybillBtn.addEventListener('click', () => {
                window.print();
            });
        }

        // Checkout Button
        checkoutSubmitBtn.addEventListener('click', submitCheckout);
    }

    // Modal helpers
    function openModal(modal) {
        modal.classList.add('active');
    }

    function closeModal(modal) {
        modal.classList.remove('active');
        // Clear inputs inside modal
        const form = modal.querySelector('form');
        if (form) form.reset();
    }

    // --- Modal AJAX forms ---
    function submitNewRep() {
        const name = newRepName.value.trim();
        if (!name) {
            showToast('Representative name is required', true);
            return;
        }

        saveRepSubmitBtn.disabled = true;
        saveRepSubmitBtn.textContent = 'Saving...';

        fetch('../apis/pos/create_rep.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rep_name: name })
        })
            .then(res => res.json())
            .then(res => {
                saveRepSubmitBtn.disabled = false;
                saveRepSubmitBtn.textContent = 'Save Representative';

                if (res.status === 'success') {
                    state.reps.push(res.data);
                    document.getElementById('addRepForm').reset();
                    renderRepsSelect(res.data.rep_id);
                    showToast('Sales Representative registered!');
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                saveRepSubmitBtn.disabled = false;
                saveRepSubmitBtn.textContent = 'Save Representative';
                showToast('Failed to communicate with server', true);
            });
    }

    function submitNewCustomer() {
        const name = newCustName.value.trim();
        const contact = newCustContact.value.trim();
        const email = newCustEmail.value.trim();
        const type = document.getElementById('newCustType').value;

        if (!name) {
            showToast('Customer name is required', true);
            return;
        }

        saveCustomerSubmitBtn.disabled = true;
        saveCustomerSubmitBtn.textContent = 'Saving...';

        fetch('../apis/pos/create_customer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_name: name,
                contact_number: contact || null,
                email: email || null,
                customer_type: type
            })
        })
            .then(res => res.json())
            .then(res => {
                saveCustomerSubmitBtn.disabled = false;
                saveCustomerSubmitBtn.textContent = 'Save Customer';

                if (res.status === 'success') {
                    state.customers.push(res.data);
                    document.getElementById('addCustomerForm').reset();
                    renderCustomersSelect(res.data.customer_id);

                    // Trigger the customer change selection logic to fetch special prices if any
                    customerSelect.value = res.data.customer_id;
                    customerSelect.dispatchEvent(new Event('change'));

                    showToast('Customer account created!');
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                saveCustomerSubmitBtn.disabled = false;
                saveCustomerSubmitBtn.textContent = 'Save Customer';
                showToast('Failed to communicate with server', true);
            });
    }

    // --- Checkout Submit ---
    function submitCheckout() {
        const repId = repSelect.value;
        const custId = customerSelect.value;

        if (!repId) {
            showToast('Please select a Sales Representative', true);
            return;
        }
        if (!custId) {
            showToast('Please select a Customer', true);
            return;
        }
        if (state.cart.length === 0) {
            showToast('Cart is currently empty', true);
            return;
        }

        checkoutSubmitBtn.disabled = true;
        checkoutSubmitBtn.textContent = 'Processing transaction...';

        const payload = {
            rep_id: repId,
            customer_id: custId,
            payment_mode: state.paymentMode,
            payment_status: state.paymentStatus,
            pricing_mode: state.pricingMode,
            discount: state.discount,
            items: state.cart
        };

        fetch('../apis/pos/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(res => {
                checkoutSubmitBtn.disabled = false;
                checkoutSubmitBtn.textContent = 'Confirm & Process Checkout';

                if (res.status === 'success') {
                    showToast(`Order #${res.data.order_id} checked out successfully!`);
                    // Clear cart
                    state.cart = [];
                    state.discount = 0.00;
                    discountInput.value = '0.00';

                    // Reload Catalog to fetch updated stocks from ledger
                    loadCatalog();
                    renderCart();

                    // Show waybill print preview modal
                    openWaybillModal(res.data.order_id);
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(err => {
                console.error(err);
                checkoutSubmitBtn.disabled = false;
                checkoutSubmitBtn.textContent = 'Confirm & Process Checkout';
                showToast('An unexpected network error occurred.', true);
            });
    }
});

// js/orders.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Application State ---
    let state = {
        orders: [],
        currentDetailOrderId: null,
        currentCustomerId: null,
        currentCustomerType: 'Retailer',
        filters: {
            search: '',
            status: 'All',
            payment: 'All',
            paymentStatus: 'All',
            commissionStatus: 'All',
            size: 'All',
            dateFrom: '',
            dateTo: ''
        }
    };

    // --- DOM Elements ---
    const ordersTableBody = document.getElementById('ordersTableBody');
    const searchInput = document.getElementById('ordersSearch');
    const statusFilter = document.getElementById('statusFilter');
    const paymentFilter = document.getElementById('paymentFilter');
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const toast = document.getElementById('notificationToast');

    // Detail Modal Elements
    const orderDetailsModal = document.getElementById('orderDetailsModal');
    const detailOrderTitle = document.getElementById('detailOrderTitle');
    const detailCustName = document.getElementById('detailCustName');
    const detailCustContact = document.getElementById('detailCustContact');
    const detailTimestamp = document.getElementById('detailTimestamp');
    const detailRepName = document.getElementById('detailRepName');
    const detailPaymentMode = document.getElementById('detailPaymentMode');
    const detailPaymentTimestamp = document.getElementById('detailPaymentTimestamp');
    const invoiceTableBody = document.getElementById('invoiceTableBody');
    const detailTotalCommission = document.getElementById('detailTotalCommission');
    const detailSubtotal = document.getElementById('detailSubtotal');
    const detailDiscount = document.getElementById('detailDiscount');
    const detailGrandTotal = document.getElementById('detailGrandTotal');

    // Pay order modal elements
    const paymentStatusFilter = document.getElementById('paymentStatusFilter');
    const commissionStatusFilter = document.getElementById('commissionStatusFilter');

    // --- Initial Load ---
    initTheme();
    loadOrders();
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

    // --- Toast Alerts ---
    function showToast(message, isError = false) {
        toast.textContent = message;
        toast.style.backgroundColor = isError ? '#ff3b30' : 'var(--accent-color)';
        toast.style.color = isError ? '#ffffff' : 'var(--accent-text)';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // --- Load Data Functions ---
    function loadOrders() {
        ordersTableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center">Loading orders database...</td>
            </tr>
        `;

        fetch('../apis/orders/get_orders.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.orders = res.data;
                    populateSizeFilter();
                    renderOrdersTable();
                } else {
                    showToast('Failed to load transaction history: ' + res.message, true);
                }
            })
            .catch(() => {
                showToast('Failed to connect to database server', true);
            });
    }

    function populateSizeFilter() {
        const sizeSet = new Set();
        state.orders.forEach(o => {
            if (o.item_sizes) {
                o.item_sizes.split(',').forEach(sz => {
                    if (sz) sizeSet.add(sz.trim());
                });
            }
        });
        const sortedSizes = Array.from(sizeSet).map(Number).sort((a, b) => a - b);
        const sizeFilterSelect = document.getElementById('sizeFilter');
        if (sizeFilterSelect) {
            sizeFilterSelect.innerHTML = '<option value="All">All Sizes</option>';
            sortedSizes.forEach(sz => {
                const opt = document.createElement('option');
                opt.value = sz;
                opt.textContent = `${sz} ml`;
                sizeFilterSelect.appendChild(opt);
            });
            sizeFilterSelect.value = state.filters.size;
        }
    }

    // --- Renderers ---
    function renderOrdersTable() {
        const filtered = state.orders.filter(o => {
            // Customer or Rep Name Search
            const nameQuery = state.filters.search.toLowerCase();
            const matchesSearch = o.customer_name.toLowerCase().includes(nameQuery) ||
                o.rep_name.toLowerCase().includes(nameQuery) ||
                o.order_id.toString().includes(nameQuery);

            // Status Filter
            const matchesStatus = state.filters.status === 'All' || o.delivery_status === state.filters.status;

            // Payment Mode Filter
            const matchesPayment = state.filters.payment === 'All' || o.payment_mode === state.filters.payment;

            // Payment Status Filter
            const matchesPaymentStatus = state.filters.paymentStatus === 'All' || o.payment_status === state.filters.paymentStatus;

            // Commission Status Filter
            const matchesCommissionStatus = state.filters.commissionStatus === 'All' || o.commission_status === state.filters.commissionStatus;

            // Size Filter
            let matchesSize = true;
            if (state.filters.size !== 'All') {
                if (!o.item_sizes) {
                    matchesSize = false;
                } else {
                    const sizesArray = o.item_sizes.split(',').map(s => s.trim());
                    matchesSize = sizesArray.includes(state.filters.size);
                }
            }

            // Date Range Filter
            let matchesDate = true;
            if (state.filters.dateFrom || state.filters.dateTo) {
                const orderDate = new Date(o.order_timestamp);
                const orderDateStr = orderDate.toISOString().slice(0, 10);
                if (state.filters.dateFrom && orderDateStr < state.filters.dateFrom) matchesDate = false;
                if (state.filters.dateTo && orderDateStr > state.filters.dateTo) matchesDate = false;
            }

            return matchesSearch && matchesStatus && matchesPayment && matchesPaymentStatus && matchesCommissionStatus && matchesSize && matchesDate;
        });

        renderOrdersStats(filtered);

        if (filtered.length === 0) {
            ordersTableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">No orders match your current filter parameters.</td>
                </tr>
            `;
            return;
        }

        ordersTableBody.innerHTML = '';
        filtered.forEach(o => {
            const row = document.createElement('tr');
            row.style.cursor = 'pointer';

            // Format timestamp
            const date = new Date(o.order_timestamp);
            const dateStr = date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
                date.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });

            row.innerHTML = `
                <td><strong>#${o.order_id}</strong></td>
                <td>${dateStr}</td>
                <td>${o.rep_name}</td>
                <td>${o.customer_name}</td>
                <td>${o.total_items} items</td>
                <td class="text-right"><strong>₱${parseFloat(o.total_amount).toFixed(2)}</strong></td>
                <td class="text-center">
                    <select class="payment-status-select customer-profile-select" data-id="${o.order_id}">
                        <option value="Unpaid" ${o.payment_status === 'Unpaid' ? 'selected' : ''}>Unpaid</option>
                        <option value="Partial" ${o.payment_status === 'Partial' ? 'selected' : ''}>Partial</option>
                        <option value="Cash" ${o.payment_status === 'Paid' && o.payment_mode === 'Cash' ? 'selected' : ''}>Paid — Cash</option>
                        <option value="GCash" ${o.payment_status === 'Paid' && o.payment_mode === 'GCash' ? 'selected' : ''}>Paid — GCash</option>
                        <option value="Bank Transfer" ${o.payment_status === 'Paid' && o.payment_mode === 'Bank Transfer' ? 'selected' : ''}>Paid — Bank Transfer</option>
                        <option value="Other" ${o.payment_status === 'Paid' && o.payment_mode === 'Other' ? 'selected' : ''}>Paid — Other</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="commission-status-select customer-profile-select" data-id="${o.order_id}">
                        <option value="Unpaid" ${o.commission_status === 'Unpaid' ? 'selected' : ''}>Unpaid</option>
                        <option value="Paid" ${o.commission_status === 'Paid' ? 'selected' : ''}>Paid</option>
                    </select>
                </td>
                <td class="text-center">
                    <select class="status-select" data-id="${o.order_id}">
                        <option value="Preparing" ${o.delivery_status === 'Preparing' ? 'selected' : ''}>Preparing</option>
                        <option value="Delivered" ${o.delivery_status === 'Delivered' ? 'selected' : ''}>Delivered</option>
                    </select>
                </td>
                <td class="text-center">
                    <button class="btn btn-icon view-btn" data-id="${o.order_id}" title="View Invoice Breakdown">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </td>
            `;

            // Setup Payment Status dropdown change event
            const paymentSelect = row.querySelector('.payment-status-select');
            paymentSelect.addEventListener('change', (e) => {
                const newValue = e.target.value;
                if (newValue === 'Unpaid') {
                    updatePaymentStatus(o.order_id, 'Unpaid', '');
                } else if (newValue === 'Partial') {
                    const remaining = parseFloat(o.total_amount) - parseFloat(o.amount_paid);
                    openAddPaymentModal(o.order_id, remaining);
                    paymentSelect.value = o.payment_status; // Reset visually until logged
                } else {
                    updatePaymentStatus(o.order_id, 'Paid', newValue);
                }
            });

            // Setup Commission Status dropdown change event
            const commissionSelect = row.querySelector('.commission-status-select');
            commissionSelect.addEventListener('change', (e) => {
                updateCommissionStatus(o.order_id, e.target.value);
            });

            // Row click event (opens modal), but ignores if dropdown is clicked
            row.addEventListener('click', (e) => {
                if (e.target.closest('.status-select') || e.target.closest('.view-btn') || e.target.closest('.payment-status-select') || e.target.closest('.commission-status-select')) return;
                openOrderDetails(o.order_id);
            });

            // Status select dropdown change event
            const select = row.querySelector('.status-select');
            select.addEventListener('change', (e) => {
                updateDeliveryStatus(o.order_id, e.target.value);
            });

            // Action button click
            row.querySelector('.view-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                openOrderDetails(o.order_id);
            });

            ordersTableBody.appendChild(row);
        });
    }

    // --- Stats Bar ---
    function renderOrdersStats(filtered) {
        const count = filtered.length;
        let totalRevenue = 0;
        let totalCommission = 0;
        let totalRaw = 0;

        filtered.forEach(o => {
            totalRevenue += parseFloat(o.total_amount || 0);
            totalCommission += parseFloat(o.total_commission || 0);
            totalRaw += parseFloat(o.total_raw_cost || 0);
        });

        const netProfit = totalRevenue - totalRaw - totalCommission;

        document.getElementById('statOrderCount').textContent = count;
        document.getElementById('statRevenue').textContent = '₱' + totalRevenue.toFixed(2);
        document.getElementById('statRawCost').textContent = '₱' + totalRaw.toFixed(2);
        document.getElementById('statCommission').textContent = '₱' + totalCommission.toFixed(2);
        document.getElementById('statGrossProfit').textContent = '₱' + netProfit.toFixed(2);
    }

    function openOrderDetails(orderId) {
        state.currentDetailOrderId = orderId;
        invoiceTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">Fetching invoice items...</td>
            </tr>
        `;
        orderDetailsModal.classList.add('active');

        fetch(`../apis/orders/get_orders.php?order_id=${orderId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const { order, items } = res.data;

                    // Render Metadata
                    detailOrderTitle.textContent = `Invoice Breakdown: Order #${order.order_id}`;
                    detailCustName.textContent = order.customer_name;
                    detailCustContact.textContent = (order.contact_number || '') + (order.email ? ` | ${order.email}` : '');

                    // Store customer reference for edit
                    state.currentCustomerId = order.customer_id || null;
                    state.currentCustomerType = order.customer_type || 'Retailer';

                    const date = new Date(order.order_timestamp);
                    detailTimestamp.textContent = date.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) + ' ' +
                        date.toLocaleTimeString();
                    detailRepName.textContent = order.rep_name;
                    detailPaymentMode.textContent = order.payment_status === 'Paid' ? order.payment_mode : 'Unpaid';

                    if (order.payment_status === 'Paid' && order.payment_timestamp) {
                        const pDate = new Date(order.payment_timestamp);
                        detailPaymentTimestamp.textContent = pDate.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) + ' ' +
                            pDate.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
                    } else {
                        detailPaymentTimestamp.textContent = '';
                    }

                    // Render Commission
                    const detailCommissionStatus = document.getElementById('detailCommissionStatus');
                    const detailCommissionTimestamp = document.getElementById('detailCommissionTimestamp');
                    if (detailCommissionStatus) {
                        detailCommissionStatus.textContent = order.commission_status;
                    }
                    if (detailCommissionTimestamp) {
                        if (order.commission_status === 'Paid' && order.commission_timestamp) {
                            const cDate = new Date(order.commission_timestamp);
                            detailCommissionTimestamp.textContent = cDate.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) + ' ' +
                                cDate.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
                        } else {
                            detailCommissionTimestamp.textContent = '';
                        }
                    }

                    // Render Table Items
                    invoiceTableBody.innerHTML = '';
                    let subtotal = 0;
                    let totalCommission = 0;

                    items.forEach(item => {
                        const unitPrice = parseFloat(item.price_applied);
                        const unitCommission = parseFloat(item.commission_applied);
                        const qty = parseInt(item.quantity);
                        const lineTotal = unitPrice * qty;

                        subtotal += lineTotal;
                        totalCommission += unitCommission * qty;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <strong>${item.scent_name}</strong><br>
                                <small style="color: var(--text-secondary)">${item.category_name} (${item.target_audience})</small>
                            </td>
                            <td>${item.volume_ml} ml</td>
                            <td class="text-right">₱${unitPrice.toFixed(2)}</td>
                            <td class="text-center">${qty}</td>
                            <td class="text-right"><strong>₱${lineTotal.toFixed(2)}</strong></td>
                            <td class="text-right" style="border-left: 1px dashed var(--border-color)">
                                ₱${(unitCommission * qty).toFixed(2)} <small style="color: var(--text-secondary)">($${unitCommission.toFixed(2)} ea)</small>
                            </td>
                        `;
                        invoiceTableBody.appendChild(row);
                    });

                    // Set summary counts
                    detailSubtotal.textContent = `₱${subtotal.toFixed(2)}`;
                    detailDiscount.textContent = `₱0.00 (Dist. in Price)`; // Note on distributed discounts
                    detailGrandTotal.textContent = `₱${subtotal.toFixed(2)}`;
                    detailTotalCommission.textContent = `₱${totalCommission.toFixed(2)}`;

                    // Render Payments
                    state.currentDetailOrderId = orderId;
                    const paidSoFar = parseFloat(order.amount_paid || 0);
                    const balance = Math.max(0, subtotal - paidSoFar);

                    document.getElementById('detailTotalPaidLabel').textContent = `₱${paidSoFar.toFixed(2)}`;
                    document.getElementById('detailBalanceLabel').textContent = `₱${balance.toFixed(2)}`;
                    document.getElementById('detailBalanceLabel').style.color = balance > 0.01 ? '#ff3b30' : 'var(--accent-color)';

                    const detailAddPaymentBtn = document.getElementById('detailAddPaymentBtn');
                    if (detailAddPaymentBtn) {
                        detailAddPaymentBtn.disabled = balance <= 0.01;
                        detailAddPaymentBtn.style.display = balance <= 0.01 ? 'none' : 'block';
                    }

                    const paymentsTableBody = document.getElementById('detailPaymentsTableBody');
                    paymentsTableBody.innerHTML = '';
                    if (res.data.payments && res.data.payments.length > 0) {
                        res.data.payments.forEach(p => {
                            const pDate = new Date(p.payment_timestamp);
                            const pDateStr = pDate.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
                                pDate.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${pDateStr}</td>
                                <td><span class="badge badge-outline" style="padding: 2px 6px; font-size: 10px; border-radius: 4px; border: 1px solid var(--border-color);">${p.payment_mode}</span></td>
                                <td class="text-right" style="font-weight:600;">₱${parseFloat(p.amount_paid).toFixed(2)}</td>
                            `;
                            paymentsTableBody.appendChild(row);
                        });
                    } else {
                        paymentsTableBody.innerHTML = `
                            <tr>
                                <td colspan="3" class="text-center" style="color:var(--text-muted); padding: 12px 0;">No payments recorded yet.</td>
                            </tr>
                        `;
                    }

                } else {
                    showToast('Failed to pull order details: ' + res.message, true);
                    orderDetailsModal.classList.remove('active');
                }
            })
            .catch(() => {
                showToast('Failed to load transaction breakdown', true);
                orderDetailsModal.classList.remove('active');
            });
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
                                    <div style="font-size: 7px; color: #555;">${item.target_audience || item.category_name} &bull; ${item.volume_ml}ml</div>
                                </td>
                                <td>&#8369;${price.toFixed(2)}</td>
                                <td style="text-align: center;">${qty}</td>
                                <td style="text-align: right; font-weight: 700;">&#8369;${lineTotal.toFixed(2)}</td>
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
                                <span>&#8369;${subtotal.toFixed(2)}</span>
                            </div>
                            ${discount > 0 ? `
                            <div class="waybill-totals-line">
                                <span>Discount:</span>
                                <span>-&#8369;${discount.toFixed(2)}</span>
                            </div>
                            ` : ''}
                            <div class="waybill-totals-line grand-total">
                                <span>Total:</span>
                                <span>&#8369;${grandTotal.toFixed(2)}</span>
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

    // --- Update Status AJAX ---

    function updateDeliveryStatus(orderId, newStatus) {
        fetch('../apis/orders/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: orderId,
                delivery_status: newStatus
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(`Order #${orderId} status set to ${newStatus}.`);
                    // Update local state status to prevent complete re-load
                    const localOrder = state.orders.find(o => o.order_id == orderId);
                    if (localOrder) localOrder.delivery_status = newStatus;
                } else {
                    showToast('Failed: ' + res.message, true);
                    loadOrders(); // Reload on failure to restore actual state
                }
            })
            .catch(() => {
                showToast('Network error while updating delivery status', true);
                loadOrders();
            });
    }

    // --- Setup Listeners ---
    function setupEventListeners() {
        themeToggleBtn.addEventListener('click', toggleTheme);

        searchInput.addEventListener('input', (e) => {
            state.filters.search = e.target.value;
            renderOrdersTable();
        });

        statusFilter.addEventListener('change', (e) => {
            state.filters.status = e.target.value;
            renderOrdersTable();
        });

        paymentFilter.addEventListener('change', (e) => {
            state.filters.payment = e.target.value;
            renderOrdersTable();
        });

        paymentStatusFilter.addEventListener('change', (e) => {
            state.filters.paymentStatus = e.target.value;
            renderOrdersTable();
        });

        if (commissionStatusFilter) {
            commissionStatusFilter.addEventListener('change', (e) => {
                state.filters.commissionStatus = e.target.value;
                renderOrdersTable();
            });
        }

        const sizeFilter = document.getElementById('sizeFilter');
        if (sizeFilter) {
            sizeFilter.addEventListener('change', (e) => {
                state.filters.size = e.target.value;
                renderOrdersTable();
            });
        }

        const ordersDateFrom = document.getElementById('ordersDateFrom');
        const ordersDateTo = document.getElementById('ordersDateTo');
        if (ordersDateFrom) ordersDateFrom.addEventListener('change', e => { state.filters.dateFrom = e.target.value; renderOrdersTable(); });
        if (ordersDateTo) ordersDateTo.addEventListener('change', e => { state.filters.dateTo = e.target.value; renderOrdersTable(); });

        const resetBtn = document.getElementById('resetOrderFiltersBtn');
        if (resetBtn) resetBtn.addEventListener('click', () => {
            document.getElementById('ordersSearch').value = '';
            document.getElementById('statusFilter').value = 'All';
            document.getElementById('paymentFilter').value = 'All';
            document.getElementById('paymentStatusFilter').value = 'All';
            if (commissionStatusFilter) commissionStatusFilter.value = 'All';
            if (sizeFilter) sizeFilter.value = 'All';
            if (ordersDateFrom) ordersDateFrom.value = '';
            if (ordersDateTo) ordersDateTo.value = '';
            state.filters = { search: '', status: 'All', payment: 'All', paymentStatus: 'All', commissionStatus: 'All', size: 'All', dateFrom: '', dateTo: '' };
            renderOrdersTable();
        });

        // Close details modals
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) modal.classList.remove('active');
            });
        });

        // Close modal on escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                    const form = activeModal.querySelector('form');
                    if (form) form.reset();
                }
            }
        });

        // Close modal on clicking backdrop
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    const form = modal.querySelector('form');
                    if (form) form.reset();
                }
            });
        });

        const detailAddPaymentBtn = document.getElementById('detailAddPaymentBtn');
        if (detailAddPaymentBtn) {
            detailAddPaymentBtn.addEventListener('click', () => {
                const order = state.orders.find(o => o.order_id == state.currentDetailOrderId);
                if (order) {
                    const grandTotal = parseFloat(order.total_amount);
                    const paidSoFar = parseFloat(order.amount_paid);
                    const remaining = grandTotal - paidSoFar;
                    openAddPaymentModal(order.order_id, remaining);
                }
            });
        }

        const paySubmitBtn = document.getElementById('paySubmitBtn');
        if (paySubmitBtn) {
            paySubmitBtn.addEventListener('click', submitAddPayment);
        }

        const detailPrintWaybillBtn = document.getElementById('detailPrintWaybillBtn');
        if (detailPrintWaybillBtn) {
            detailPrintWaybillBtn.addEventListener('click', () => {
                if (state.currentDetailOrderId) {
                    openWaybillModal(state.currentDetailOrderId);
                }
            });
        }

        const printWaybillBtn = document.getElementById('printWaybillBtn');
        if (printWaybillBtn) {
            printWaybillBtn.addEventListener('click', () => {
                window.print();
            });
        }
    }

    function openAddPaymentModal(orderId, remaining) {
        document.getElementById('payOrderId').value = orderId;
        document.getElementById('payRemainingLabel').textContent = `₱${remaining.toFixed(2)}`;
        document.getElementById('payAmountInput').value = remaining.toFixed(2);
        document.getElementById('payAmountInput').max = remaining;
        document.getElementById('addPaymentModal').classList.add('active');
    }

    function submitAddPayment() {
        const orderId = parseInt(document.getElementById('payOrderId').value);
        const amount = parseFloat(document.getElementById('payAmountInput').value);
        const mode = document.getElementById('payModeSelect').value;

        if (isNaN(amount) || amount <= 0) {
            showToast('Please enter a valid payment amount', true);
            return;
        }

        const submitBtn = document.getElementById('paySubmitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        fetch('../apis/orders/add_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: orderId,
                amount_paid: amount,
                payment_mode: mode
            })
        })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Payment';
                if (res.status === 'success') {
                    showToast('Payment transaction logged successfully.');
                    document.getElementById('addPaymentModal').classList.remove('active');
                    document.getElementById('addPaymentForm').reset();
                    if (orderDetailsModal.classList.contains('active')) {
                        openOrderDetails(orderId);
                    }
                    loadOrders();
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Payment';
                showToast('Network error while logging payment', true);
            });
    }

    function updatePaymentStatus(orderId, status, mop) {
        fetch('../apis/orders/pay_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: orderId,
                payment_status: status,
                payment_mode: mop
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(`Order #${orderId} payment status set to ${status}.`);

                    // Update local model
                    const local = state.orders.find(o => o.order_id == orderId);
                    if (local) {
                        local.payment_status = status;
                        if (status === 'Paid') {
                            local.payment_mode = mop;
                            const now = new Date();
                            local.payment_timestamp = now.toISOString().slice(0, 19).replace('T', ' ');
                        } else {
                            local.payment_mode = '';
                            local.payment_timestamp = null;
                        }
                    }
                    renderOrdersTable();
                } else {
                    showToast('Failed to update payment status: ' + res.message, true);
                    renderOrdersTable();
                }
            })
            .catch(() => {
                showToast('Network error while updating payment status', true);
                renderOrdersTable();
            });
    }

    function updateCommissionStatus(orderId, status) {
        fetch('../apis/orders/update_commission.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: orderId,
                commission_status: status
            })
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    showToast(`Order #${orderId} commission status set to ${status}.`);

                    // Update local model
                    const local = state.orders.find(o => o.order_id == orderId);
                    if (local) {
                        local.commission_status = status;
                        if (status === 'Paid') {
                            const now = new Date();
                            local.commission_timestamp = now.toISOString().slice(0, 19).replace('T', ' ');
                        } else {
                            local.commission_timestamp = null;
                        }
                    }
                    renderOrdersTable();
                } else {
                    showToast('Failed to update commission status: ' + res.message, true);
                    renderOrdersTable();
                }
            })
            .catch(() => {
                showToast('Network error while updating commission status', true);
                renderOrdersTable();
            });
    }

    // --- Edit Customer for a Specific Order (Sales History) ---
    const editCustomerModal = document.getElementById('editCustomerModal');
    const editCustomerSelect = document.getElementById('editCustomerSelect');
    const editCustomerNewInput = document.getElementById('editCustomerNewInput');
    const editCustomerSaveBtn = document.getElementById('editCustomerSaveBtn');
    const editCustomerCancelBtn = document.getElementById('editCustomerCancelBtn');
    const editCustomerCloseBtn = document.getElementById('editCustomerCloseBtn');
    const editCustomerModalTitle = document.getElementById('editCustomerModalTitle');

    window._openEditCustomerName = function() {
        if (!state.currentDetailOrderId) {
            showToast('Order ID not available.', true);
            return;
        }

        if (editCustomerModalTitle) {
            editCustomerModalTitle.textContent = `Reassign Order Customer #${state.currentDetailOrderId}`;
        }
        editCustomerNewInput.value = '';
        editCustomerSelect.innerHTML = '<option value="">Loading customers...</option>';

        editCustomerModal.classList.add('active');

        // Fetch latest customers list
        fetch('../apis/pos/get_customers.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    editCustomerSelect.innerHTML = '<option value="">-- Select Existing Customer --</option>';
                    res.data.forEach(cust => {
                        const opt = document.createElement('option');
                        opt.value = cust.customer_id;
                        opt.textContent = `${cust.customer_name} (${cust.customer_type})`;
                        if (cust.customer_id == state.currentCustomerId) {
                            opt.selected = true;
                        }
                        editCustomerSelect.appendChild(opt);
                    });
                } else {
                    editCustomerSelect.innerHTML = '<option value="">Failed to load customers</option>';
                }
            })
            .catch(() => {
                editCustomerSelect.innerHTML = '<option value="">Error loading customers</option>';
            });
    };

    function closeEditCustomerModal() {
        editCustomerModal.classList.remove('active');
    }

    editCustomerCancelBtn.addEventListener('click', closeEditCustomerModal);
    editCustomerCloseBtn.addEventListener('click', closeEditCustomerModal);
    editCustomerModal.addEventListener('click', (e) => {
        if (e.target === editCustomerModal) closeEditCustomerModal();
    });

    editCustomerSaveBtn.addEventListener('click', () => {
        const selectedCustId = editCustomerSelect.value;
        const newCustName = editCustomerNewInput.value.trim();

        if (!selectedCustId && !newCustName) {
            showToast('Please select a customer or type a new customer name.', true);
            return;
        }

        editCustomerSaveBtn.disabled = true;
        editCustomerSaveBtn.textContent = 'Updating...';

        fetch('../apis/orders/update_order_customer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                order_id: state.currentDetailOrderId,
                customer_id: selectedCustId ? parseInt(selectedCustId) : 0,
                new_customer_name: newCustName
            })
        })
            .then(res => res.json())
            .then(res => {
                editCustomerSaveBtn.disabled = false;
                editCustomerSaveBtn.textContent = 'Update Customer';
                if (res.status === 'success') {
                    const cust = res.data;
                    showToast(`Order #${state.currentDetailOrderId} customer set to ${cust.customer_name}.`);

                    // Update UI elements in open details modal
                    detailCustName.textContent = cust.customer_name;
                    detailCustContact.textContent = (cust.contact_number || '') + (cust.email ? ` | ${cust.email}` : '');
                    state.currentCustomerId = cust.customer_id;

                    // Sync in-memory list ONLY for this order_id
                    const targetOrder = state.orders.find(o => o.order_id == state.currentDetailOrderId);
                    if (targetOrder) {
                        targetOrder.customer_id = cust.customer_id;
                        targetOrder.customer_name = cust.customer_name;
                    }

                    renderOrdersTable();
                    closeEditCustomerModal();
                } else {
                    showToast(res.message || 'Failed to update order customer.', true);
                }
            })
            .catch(() => {
                editCustomerSaveBtn.disabled = false;
                editCustomerSaveBtn.textContent = 'Update Customer';
                showToast('Network error while saving.', true);
            });
    });
});


// js/people.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Application State ---
    let state = {
        activeTab: 'reps', // 'reps' or 'customers'
        reps: [],
        customers: [],
        products: [], // loaded for select price override dropdown
        filters: {
            search: '',
            customerType: 'All'
        },
        currentCustomerId: null // currently active customer for modals
    };

    // --- DOM Elements ---
    const tabRepsBtn = document.getElementById('tabRepsBtn');
    const tabCustomersBtn = document.getElementById('tabCustomersBtn');
    const directorySearch = document.getElementById('directorySearch');
    const customerTypeFilter = document.getElementById('customerTypeFilter');
    const repsTableContainer = document.getElementById('repsTableContainer');
    const customersTableContainer = document.getElementById('customersTableContainer');
    const repsTableBody = document.getElementById('repsTableBody');
    const customersTableBody = document.getElementById('customersTableBody');
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const toast = document.getElementById('notificationToast');

    // Header buttons
    const addRepDirectBtn = document.getElementById('addRepDirectBtn');
    const addCustomerDirectBtn = document.getElementById('addCustomerDirectBtn');

    // Stats Elements
    const directoryStatsBar = document.getElementById('directoryStatsBar');

    // Customer History Modal
    const customerHistoryModal = document.getElementById('customerHistoryModal');
    const historyCustomerName = document.getElementById('historyCustomerName');
    const historyCustomerType = document.getElementById('historyCustomerType');
    const customerHistoryTableBody = document.getElementById('customerHistoryTableBody');

    // Special Prices Modal
    const specialPricesModal = document.getElementById('specialPricesModal');
    const specialCustomerName = document.getElementById('specialCustomerName');
    const specialCategorySelect = document.getElementById('specialCategorySelect');
    const specialSizeSelect = document.getElementById('specialSizeSelect');
    const specialPriceInput = document.getElementById('specialPriceInput');
    const saveSpecialPriceBtn = document.getElementById('saveSpecialPriceBtn');
    const specialPricesTableBody = document.getElementById('specialPricesTableBody');

    // Add Rep Modal
    const addRepModal = document.getElementById('addRepModal');
    const saveRepDirectBtn = document.getElementById('saveRepDirectBtn');
    const addRepForm = document.getElementById('addRepForm');
    const addRepName = document.getElementById('addRepName');

    // Edit Rep Modal
    const editRepModal = document.getElementById('editRepModal');
    const updateRepDirectBtn = document.getElementById('updateRepDirectBtn');
    const editRepForm = document.getElementById('editRepForm');
    const editRepId = document.getElementById('editRepId');
    const editRepName = document.getElementById('editRepName');

    // Add Customer Modal
    const addCustomerModal = document.getElementById('addCustomerModal');
    const saveCustomerDirectBtn = document.getElementById('saveCustomerDirectBtn');
    const addCustomerForm = document.getElementById('addCustomerForm');
    const addCustomerName = document.getElementById('addCustomerName');
    const addCustomerContact = document.getElementById('addCustomerContact');
    const addCustomerEmail = document.getElementById('addCustomerEmail');
    const addCustomerType = document.getElementById('addCustomerType');

    // Edit Customer Modal
    const editCustomerModal = document.getElementById('editCustomerModal');
    const updateCustomerDirectBtn = document.getElementById('updateCustomerDirectBtn');
    const editCustomerForm = document.getElementById('editCustomerForm');
    const editCustomerId = document.getElementById('editCustomerId');
    const editCustomerName = document.getElementById('editCustomerName');
    const editCustomerContact = document.getElementById('editCustomerContact');
    const editCustomerEmail = document.getElementById('editCustomerEmail');
    const editCustomerType = document.getElementById('editCustomerType');

    // --- Initial Load ---
    initTheme();
    loadDirectory();
    loadProducts();
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
        themeToggleBtn.innerHTML = isDark
            ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>`;
    }

    function showToast(message, isError = false) {
        toast.textContent = message;
        toast.style.backgroundColor = isError ? '#ff3b30' : 'var(--accent-color)';
        toast.style.color = isError ? '#ffffff' : 'var(--accent-text)';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // --- Load Data ---
    function loadDirectory() {
        fetch('../apis/people/get_directory.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.reps = res.data.reps;
                    state.customers = res.data.customers;
                    applyFilters();
                } else {
                    showToast('Failed to load directory: ' + res.message, true);
                }
            })
            .catch(() => showToast('Server communication failure.', true));
    }

    function loadProducts() {
        fetch('../apis/pos/get_products.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.products = res.data;
                    renderCategoryAndSizeDropdowns();
                }
            });
    }

    function renderCategoryAndSizeDropdowns() {
        const catMap = {};
        const sizeMap = {};
        state.products.forEach(p => {
            catMap[p.category_id] = p.category_name;
            sizeMap[p.size_id] = p.volume_ml;
        });

        specialCategorySelect.innerHTML = '<option value="">Choose category...</option>';
        Object.keys(catMap).forEach(catId => {
            const opt = document.createElement('option');
            opt.value = catId;
            opt.textContent = catMap[catId];
            specialCategorySelect.appendChild(opt);
        });

        specialSizeSelect.innerHTML = '<option value="">Choose size...</option>';
        Object.keys(sizeMap).forEach(sizeId => {
            const opt = document.createElement('option');
            opt.value = sizeId;
            opt.textContent = `${sizeMap[sizeId]} ml`;
            specialSizeSelect.appendChild(opt);
        });
    }

    // --- Filter & Stats Controller ---
    function applyFilters() {
        const query = state.filters.search.toLowerCase();

        if (state.activeTab === 'reps') {
            const filteredReps = state.reps.filter(r => r.rep_name.toLowerCase().includes(query) || r.rep_id.toString().includes(query));
            renderRepsTable(filteredReps);
            updateStatsReps(filteredReps);
        } else {
            const filteredCusts = state.customers.filter(c => {
                const matchesSearch = c.customer_name.toLowerCase().includes(query) || 
                                      (c.contact_number && c.contact_number.includes(query)) ||
                                      (c.email && c.email.toLowerCase().includes(query)) ||
                                      c.customer_id.toString().includes(query);
                
                const matchesType = state.filters.customerType === 'All' || c.customer_type === state.filters.customerType;
                return matchesSearch && matchesType;
            });
            renderCustomersTable(filteredCusts);
            updateStatsCustomers(filteredCusts);
        }
    }

    // --- Stats Strip Updates ---
    function updateStatsReps(repsList) {
        const count = repsList.length;
        const totalSales = repsList.reduce((sum, r) => sum + parseFloat(r.total_sales), 0);
        const totalComm = repsList.reduce((sum, r) => sum + parseFloat(r.total_commission), 0);

        directoryStatsBar.innerHTML = `
            <div class="stat-chip">
                <span class="stat-label">Total Representatives</span>
                <span class="stat-value">${count}</span>
            </div>
            <div class="stat-chip">
                <span class="stat-label">Cumulative Rep Sales</span>
                <span class="stat-value">₱${totalSales.toFixed(2)}</span>
            </div>
            <div class="stat-chip">
                <span class="stat-label">Total Accumulated Commissions</span>
                <span class="stat-value">₱${totalComm.toFixed(2)}</span>
            </div>
        `;
    }

    function updateStatsCustomers(custList) {
        const count = custList.length;
        const totalSpent = custList.reduce((sum, c) => sum + parseFloat(c.total_spent), 0);
        const totalUnits = custList.reduce((sum, c) => sum + parseInt(c.total_units), 0);

        directoryStatsBar.innerHTML = `
            <div class="stat-chip">
                <span class="stat-label">Total Customer Profiles</span>
                <span class="stat-value">${count}</span>
            </div>
            <div class="stat-chip">
                <span class="stat-label">Cumulative Customer Spend</span>
                <span class="stat-value">₱${totalSpent.toFixed(2)}</span>
            </div>
            <div class="stat-chip">
                <span class="stat-label">Total Purchased Units</span>
                <span class="stat-value">${totalUnits} units</span>
            </div>
        `;
    }

    // --- Renderers ---
    function renderRepsTable(repsList) {
        if (repsList.length === 0) {
            repsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No representative records found.</td></tr>';
            return;
        }

        repsTableBody.innerHTML = '';
        repsList.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>#${r.rep_id}</strong></td>
                <td><strong>${r.rep_name}</strong></td>
                <td class="text-right">${parseInt(r.total_units)} units</td>
                <td class="text-right"><strong>₱${parseFloat(r.total_sales).toFixed(2)}</strong></td>
                <td class="text-right"><strong>₱${parseFloat(r.total_commission).toFixed(2)}</strong></td>
                <td class="text-center">
                    <button class="action-btn action-btn-outline edit-rep-btn" data-id="${r.rep_id}" data-name="${r.rep_name}">
                        Edit Details
                    </button>
                </td>
            `;

            tr.querySelector('.edit-rep-btn').addEventListener('click', (e) => {
                const btn = e.target;
                editRepId.value = btn.getAttribute('data-id');
                editRepName.value = btn.getAttribute('data-name');
                editRepModal.classList.add('active');
            });

            repsTableBody.appendChild(tr);
        });
    }

    function renderCustomersTable(custList) {
        if (custList.length === 0) {
            customersTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No customer records found.</td></tr>';
            return;
        }

        customersTableBody.innerHTML = '';
        custList.forEach(c => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="customer-name-field">${c.customer_name}</div>
                    <div class="customer-meta-field">
                        ${c.contact_number ? c.contact_number : 'No phone'} · ${c.email ? c.email : 'No email'}
                    </div>
                </td>
                <td class="text-center">
                    <select class="customer-profile-select" data-id="${c.customer_id}">
                        <option value="Retailer" ${c.customer_type === 'Retailer' ? 'selected' : ''}>Retailer</option>
                        <option value="Reseller" ${c.customer_type === 'Reseller' ? 'selected' : ''}>Reseller</option>
                        <option value="Special" ${c.customer_type === 'Special' ? 'selected' : ''}>Special Price</option>
                    </select>
                </td>
                <td class="text-center">${c.total_units} units</td>
                <td class="text-right"><strong>₱${parseFloat(c.total_spent).toFixed(2)}</strong></td>
                <td class="text-center">
                    <div class="action-btn-group">
                        <button class="action-btn action-btn-outline edit-cust-btn" data-id="${c.customer_id}">
                            Edit Details
                        </button>
                        <button class="action-btn action-btn-outline edit-prices-btn" data-id="${c.customer_id}" ${c.customer_type !== 'Special' ? 'disabled' : ''}>
                            Manage Custom Prices
                        </button>
                        <button class="action-btn action-btn-outline view-history-btn" data-id="${c.customer_id}">
                            Order History
                        </button>
                    </div>
                </td>
            `;

            // Change Type Handler
            tr.querySelector('.customer-profile-select').addEventListener('change', (e) => {
                updateCustomerType(c.customer_id, e.target.value);
            });

            // Action triggers
            tr.querySelector('.view-history-btn').addEventListener('click', () => {
                openHistoryModal(c.customer_id);
            });

            const editPricesBtn = tr.querySelector('.edit-prices-btn');
            editPricesBtn.addEventListener('click', () => {
                openSpecialPricesModal(c.customer_id);
            });

            tr.querySelector('.edit-cust-btn').addEventListener('click', () => {
                editCustomerId.value = c.customer_id;
                editCustomerName.value = c.customer_name;
                editCustomerContact.value = c.contact_number || '';
                editCustomerEmail.value = c.email || '';
                editCustomerType.value = c.customer_type;
                editCustomerModal.classList.add('active');
            });

            customersTableBody.appendChild(tr);
        });
    }

    // --- Customer Type Handler ---
    function updateCustomerType(customerId, newType) {
        fetch('../apis/people/update_customer_type.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: customerId,
                customer_type: newType
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(`Customer type set to ${newType}.`);
                
                // Update local model
                const cust = state.customers.find(c => c.customer_id == customerId);
                if (cust) cust.customer_type = newType;
                
                // Apply filter to refresh list state (enables/disables special pricing buttons)
                applyFilters();
            } else {
                showToast('Failed to update type: ' + res.message, true);
                loadDirectory();
            }
        })
        .catch(() => {
            showToast('Network error while updating customer type.', true);
            loadDirectory();
        });
    }

    // --- History Modal Operations ---
    function openHistoryModal(customerId) {
        state.currentCustomerId = customerId;
        customerHistoryTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading purchase details...</td></tr>';
        
        fetch(`../apis/people/get_customer_history.php?customer_id=${customerId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const cust = res.data.customer;
                    historyCustomerName.textContent = cust.customer_name + ' — Invoice History';
                    historyCustomerType.textContent = `Profile: ${cust.customer_type}`;
                    
                    const orders = res.data.orders;
                    if (orders.length === 0) {
                        customerHistoryTableBody.innerHTML = '<tr><td colspan="7" class="text-center" style="padding:24px 0;">This customer has not placed any orders yet.</td></tr>';
                    } else {
                        customerHistoryTableBody.innerHTML = '';
                        orders.forEach(o => {
                            const date = new Date(o.order_timestamp);
                            const dateStr = date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
                            
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><strong>#${o.order_id}</strong></td>
                                <td>${dateStr}</td>
                                <td>${o.rep_name}</td>
                                <td class="text-center">${o.total_items} items</td>
                                <td class="text-right"><strong>₱${parseFloat(o.total_amount).toFixed(2)}</strong></td>
                                <td class="text-center">
                                    <span class="badge ${o.payment_status === 'Paid' ? 'badge-success' : 'badge-outline'}" style="font-size:11px;">
                                        ${o.payment_status === 'Paid' ? `Paid (${o.payment_mode})` : 'Unpaid'}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge" style="font-size:11px;">${o.delivery_status}</span>
                                </td>
                            `;
                            customerHistoryTableBody.appendChild(tr);
                        });
                    }
                    customerHistoryModal.classList.add('active');
                } else {
                    showToast('Failed to load purchase records', true);
                }
            })
            .catch(() => showToast('Error communicating with logs API', true));
    }

    // --- Special Prices Modal Operations ---
    function openSpecialPricesModal(customerId) {
        state.currentCustomerId = customerId;
        const cust = state.customers.find(c => c.customer_id == customerId);
        specialCustomerName.textContent = `Overrides for ${cust ? cust.customer_name : 'Customer'}`;
        
        loadCustomerSpecialPrices(customerId);
        document.getElementById('addSpecialPriceForm').reset();
        specialPricesModal.classList.add('active');
    }

    function loadCustomerSpecialPrices(customerId) {
        specialPricesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading price overrides...</td></tr>';
        
        fetch(`../apis/people/get_special_prices.php?customer_id=${customerId}`)
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    const prices = res.data.prices;
                    if (prices.length === 0) {
                        specialPricesTableBody.innerHTML = '<tr><td colspan="3" class="text-center" style="padding:20px 0;">No active custom pricing overrides.</td></tr>';
                    } else {
                        specialPricesTableBody.innerHTML = '';
                        prices.forEach(p => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>
                                    <strong>${p.category_name}</strong>
                                    <div style="font-size:11px; color:var(--text-secondary);">${p.volume_ml}ml</div>
                                </td>
                                <td class="text-right"><strong>₱${parseFloat(p.special_price).toFixed(2)}</strong></td>
                                <td class="text-center">
                                    <button class="btn delete-price-btn" data-category-id="${p.category_id}" data-size-id="${p.size_id}" style="padding:4px 8px; font-size:11px; color:#ff3b30; border-color:#ff3b30; background:transparent;">
                                        Delete
                                    </button>
                                </td>
                            `;

                            tr.querySelector('.delete-price-btn').addEventListener('click', () => {
                                deleteSpecialPrice(customerId, p.category_id, p.size_id);
                            });

                            specialPricesTableBody.appendChild(tr);
                        });
                    }
                } else {
                    showToast('Failed to load pricing overrides', true);
                }
            })
            .catch(() => showToast('Error communicating with prices API', true));
    }

    function saveSpecialPrice() {
        const catId = specialCategorySelect.value;
        const sizeId = specialSizeSelect.value;
        const price = parseFloat(specialPriceInput.value);

        if (!catId) { showToast('Please select a category', true); return; }
        if (!sizeId) { showToast('Please select a bottle size', true); return; }
        if (isNaN(price) || price <= 0) { showToast('Please enter a valid price greater than zero', true); return; }

        saveSpecialPriceBtn.disabled = true;
        saveSpecialPriceBtn.textContent = 'Applying...';

        fetch('../apis/people/save_special_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: state.currentCustomerId,
                category_id: catId,
                size_id: sizeId,
                special_price: price
            })
        })
        .then(res => res.json())
        .then(res => {
            saveSpecialPriceBtn.disabled = false;
            saveSpecialPriceBtn.textContent = 'Apply Override';
            if (res.status === 'success') {
                showToast('Custom price saved!');
                document.getElementById('addSpecialPriceForm').reset();
                loadCustomerSpecialPrices(state.currentCustomerId);
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => {
            saveSpecialPriceBtn.disabled = false;
            saveSpecialPriceBtn.textContent = 'Apply Override';
            showToast('Network error while saving special price', true);
        });
    }

    function deleteSpecialPrice(customerId, categoryId, sizeId) {
        fetch('../apis/people/save_special_price.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: customerId,
                category_id: categoryId,
                size_id: sizeId,
                action: 'delete'
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                showToast('Pricing override removed.');
                loadCustomerSpecialPrices(customerId);
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => showToast('Network error while deleting pricing override', true));
    }

    // --- Event Listeners ---
    function setupEventListeners() {
        themeToggleBtn.addEventListener('click', toggleTheme);

        // Tab switches
        tabRepsBtn.addEventListener('click', () => {
            if (state.activeTab === 'reps') return;
            state.activeTab = 'reps';
            tabRepsBtn.classList.add('active');
            tabCustomersBtn.classList.remove('active');
            
            directorySearch.placeholder = 'Search representatives...';
            customerTypeFilter.style.display = 'none';
            repsTableContainer.style.display = 'block';
            customersTableContainer.style.display = 'none';
            addRepDirectBtn.style.display = 'inline-block';
            addCustomerDirectBtn.style.display = 'none';

            applyFilters();
        });

        tabCustomersBtn.addEventListener('click', () => {
            if (state.activeTab === 'customers') return;
            state.activeTab = 'customers';
            tabCustomersBtn.classList.add('active');
            tabRepsBtn.classList.remove('active');

            directorySearch.placeholder = 'Search customer name or phone...';
            customerTypeFilter.style.display = 'block';
            repsTableContainer.style.display = 'none';
            customersTableContainer.style.display = 'block';
            addRepDirectBtn.style.display = 'none';
            addCustomerDirectBtn.style.display = 'inline-block';

            applyFilters();
        });

        // Search inputs
        directorySearch.addEventListener('input', e => {
            state.filters.search = e.target.value;
            applyFilters();
        });

        customerTypeFilter.addEventListener('change', e => {
            state.filters.customerType = e.target.value;
            applyFilters();
        });

        // Direct Add Handlers
        addRepDirectBtn.addEventListener('click', () => {
            addRepForm.reset();
            addRepModal.classList.add('active');
        });

        addCustomerDirectBtn.addEventListener('click', () => {
            addCustomerForm.reset();
            addCustomerModal.classList.add('active');
        });

        saveRepDirectBtn.addEventListener('click', submitCreateRep);
        saveCustomerDirectBtn.addEventListener('click', submitCreateCustomer);

        // Direct Edit Handlers
        updateRepDirectBtn.addEventListener('click', submitEditRep);
        updateCustomerDirectBtn.addEventListener('click', submitEditCustomer);

        // Add special price button click handler
        saveSpecialPriceBtn.addEventListener('click', saveSpecialPrice);

        // Modals closers
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.target.closest('.modal').classList.remove('active');
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
    }

    // --- Submissions for CRUD ---
    function submitCreateRep() {
        const name = addRepName.value.trim();
        if (!name) { showToast('Representative name cannot be empty.', true); return; }

        saveRepDirectBtn.disabled = true;
        saveRepDirectBtn.textContent = 'Registering...';

        fetch('../apis/pos/create_rep.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rep_name: name })
        })
        .then(res => res.json())
        .then(res => {
            saveRepDirectBtn.disabled = false;
            saveRepDirectBtn.textContent = 'Register';
            if (res.status === 'success') {
                showToast('Sales representative registered successfully.');
                addRepForm.reset();
                loadDirectory();
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => {
            saveRepDirectBtn.disabled = false;
            saveRepDirectBtn.textContent = 'Register';
            showToast('Network error while registering representative.', true);
        });
    }

    function submitCreateCustomer() {
        const name = addCustomerName.value.trim();
        const contact = addCustomerContact.value.trim();
        const email = addCustomerEmail.value.trim();
        const type = addCustomerType.value;

        if (!name) { showToast('Customer name cannot be empty.', true); return; }

        saveCustomerDirectBtn.disabled = true;
        saveCustomerDirectBtn.textContent = 'Registering...';

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
            saveCustomerDirectBtn.disabled = false;
            saveCustomerDirectBtn.textContent = 'Register';
            if (res.status === 'success') {
                showToast('Customer registered successfully.');
                addCustomerForm.reset();
                loadDirectory();
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => {
            saveCustomerDirectBtn.disabled = false;
            saveCustomerDirectBtn.textContent = 'Register';
            showToast('Network error while registering customer.', true);
        });
    }

    function submitEditRep() {
        const id = parseInt(editRepId.value);
        const name = editRepName.value.trim();

        if (isNaN(id) || !name) { showToast('Representative name cannot be empty.', true); return; }

        updateRepDirectBtn.disabled = true;
        updateRepDirectBtn.textContent = 'Saving...';

        fetch('../apis/people/update_rep.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ rep_id: id, rep_name: name })
        })
        .then(res => res.json())
        .then(res => {
            updateRepDirectBtn.disabled = false;
            updateRepDirectBtn.textContent = 'Save Changes';
            if (res.status === 'success') {
                showToast('Representative renamed successfully.');
                editRepForm.reset();
                loadDirectory();
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => {
            updateRepDirectBtn.disabled = false;
            updateRepDirectBtn.textContent = 'Save Changes';
            showToast('Network error while renaming representative.', true);
        });
    }

    function submitEditCustomer() {
        const id = parseInt(editCustomerId.value);
        const name = editCustomerName.value.trim();
        const contact = editCustomerContact.value.trim();
        const email = editCustomerEmail.value.trim();
        const type = editCustomerType.value;

        if (isNaN(id) || !name) { showToast('Customer name cannot be empty.', true); return; }

        updateCustomerDirectBtn.disabled = true;
        updateCustomerDirectBtn.textContent = 'Saving...';

        fetch('../apis/people/update_customer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: id,
                customer_name: name,
                contact_number: contact || null,
                email: email || null,
                customer_type: type
            })
        })
        .then(res => res.json())
        .then(res => {
            updateCustomerDirectBtn.disabled = false;
            updateCustomerDirectBtn.textContent = 'Save Changes';
            if (res.status === 'success') {
                showToast('Customer details saved successfully.');
                editCustomerForm.reset();
                loadDirectory();
            } else {
                showToast(res.message, true);
            }
        })
        .catch(() => {
            updateCustomerDirectBtn.disabled = false;
            updateCustomerDirectBtn.textContent = 'Save Changes';
            showToast('Network error while updating customer details.', true);
        });
    }
});

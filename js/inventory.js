// js/inventory.js

document.addEventListener('DOMContentLoaded', () => {
    let state = {
        products: [],
        categories: [],
        filters: {
            search: '',
            category: 'All',
            audience: 'All',
            size: 'All',
            stock: 'All'
        },
        // Track which product to quick-restock
        quickRestockProductId: null
    };

    // --- DOM Elements ---
    const inventoryTableBody = document.getElementById('inventoryTableBody');
    const searchInput = document.getElementById('inventorySearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const audienceFilter = document.getElementById('audienceFilter');
    const stockLevelFilter = document.getElementById('stockLevelFilter');
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const toast = document.getElementById('notificationToast');

    // Modals - Adjust Stock
    const adjustStockModal = document.getElementById('adjustStockModal');
    const openAdjustModalBtn = document.getElementById('openAdjustModalBtn');
    const saveAdjustmentSubmitBtn = document.getElementById('saveAdjustmentSubmitBtn');
    const adjustProductSelect = document.getElementById('adjustProductSelect');
    const adjustMovementSelect = document.getElementById('adjustMovementSelect');
    const adjustQuantity = document.getElementById('adjustQuantity');
    const adjustNotes = document.getElementById('adjustNotes');

    // Modals - Add Product
    const addProductModal = document.getElementById('addProductModal');
    const openAddProductModalBtn = document.getElementById('openAddProductModalBtn');
    const saveProductSubmitBtn = document.getElementById('saveProductSubmitBtn');
    const addProductForm = document.getElementById('addProductForm');
    const addScentName = document.getElementById('addScentName');
    const addTargetAudience = document.getElementById('addTargetAudience');
    const addCategorySelect = document.getElementById('addCategorySelect');
    const newCategoryGroup = document.getElementById('newCategoryGroup');
    const addNewCategoryName = document.getElementById('addNewCategoryName');
    const addSizeSelect = document.getElementById('addSizeSelect');
    const newSizeGroup = document.getElementById('newSizeGroup');
    const addNewVolumeMl = document.getElementById('addNewVolumeMl');
    const addRetailPrice = document.getElementById('addRetailPrice');
    const addResellerPrice = document.getElementById('addResellerPrice');
    const addRawPrice = document.getElementById('addRawPrice');
    const addCommissionRetail    = document.getElementById('addCommissionRetail');
    const addCommissionReseller  = document.getElementById('addCommissionReseller');
    const addInitialStock = document.getElementById('addInitialStock');

    // Modals - Bulk Price Adjustment
    const bulkPriceModal = document.getElementById('bulkPriceModal');
    const openBulkPriceModalBtn = document.getElementById('openBulkPriceModalBtn');
    const saveBulkPriceSubmitBtn = document.getElementById('saveBulkPriceSubmitBtn');
    const bulkPriceForm = document.getElementById('bulkPriceForm');
    const bulkCategorySelect = document.getElementById('bulkCategorySelect');
    const bulkSizeSelect = document.getElementById('bulkSizeSelect');
    const bulkRetailPrice = document.getElementById('bulkRetailPrice');
    const bulkResellerPrice = document.getElementById('bulkResellerPrice');
    const bulkCommissionRetail = document.getElementById('bulkCommissionRetail');
    const bulkCommissionReseller = document.getElementById('bulkCommissionReseller');

    // --- Init ---
    initTheme();
    loadInventory();
    setupEventListeners();

    // --- Theme ---
    function initTheme() {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark') {
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

    // --- Load ---
    function loadInventory() {
        inventoryTableBody.innerHTML = `<tr><td colspan="8" class="text-center">Loading stock details...</td></tr>`;

        fetch('../apis/inventory/get_products.php')
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    state.products = res.data;

                    const catMap = {};
                    const sizeMap = {};
                    res.data.forEach(p => {
                        catMap[p.category_id] = p.category_name;
                        sizeMap[p.size_id] = p.volume_ml;
                    });
                    state.categories = Object.keys(catMap).map(id => ({
                        category_id: id,
                        category_name: catMap[id]
                    }));
                    state.sizes = Object.keys(sizeMap).map(id => ({
                        size_id: id,
                        volume_ml: sizeMap[id]
                    }));

                    renderCategoryFilters();
                    renderSizeFilters();
                    renderInventoryStats();
                    renderInventoryTable();
                    renderProductAdjustSelect();
                    populateProductCreationSelects();
                } else {
                    showToast('Failed to load stock: ' + res.message, true);
                }
            })
            .catch(() => showToast('Database server error', true));
    }

    function renderSizeFilters() {
        const sizeFilterSelect = document.getElementById('sizeFilter');
        if (sizeFilterSelect) {
            sizeFilterSelect.innerHTML = '<option value="All">All Sizes</option>';
            const sortedSizes = [...state.sizes].sort((a,b) => a.volume_ml - b.volume_ml);
            sortedSizes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.size_id;
                opt.textContent = `${s.volume_ml} ml`;
                sizeFilterSelect.appendChild(opt);
            });
            sizeFilterSelect.value = state.filters.size;
        }
    }

    // --- Renderers ---
    function renderCategoryFilters() {
        categoryFilter.innerHTML = '<option value="All">All Categories</option>';
        state.categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat.category_id;
            opt.textContent = cat.category_name;
            categoryFilter.appendChild(opt);
        });
    }

    function renderInventoryStats() {
        let inCount = 0, lowCount = 0, outCount = 0;
        state.products.forEach(p => {
            const stock = parseInt(p.stock);
            if (stock <= 0) outCount++;
            else if (stock <= 5) lowCount++;
            else inCount++;
        });
        document.getElementById('statInStock').textContent = inCount;
        document.getElementById('statLowStock').textContent = lowCount;
        document.getElementById('statOutOfStock').textContent = outCount;
        document.getElementById('statTotalSkus').textContent = state.products.length;

        // Active chip highlight
        ['chipInStock', 'chipLowStock', 'chipOutOfStock'].forEach(id => {
            document.getElementById(id).classList.remove('active');
        });
        if (state.filters.stock === 'In') document.getElementById('chipInStock').classList.add('active');
        else if (state.filters.stock === 'Low') document.getElementById('chipLowStock').classList.add('active');
        else if (state.filters.stock === 'Out') document.getElementById('chipOutOfStock').classList.add('active');
    }

    function renderProductAdjustSelect(preselect = null, filterQuery = '') {
        adjustProductSelect.innerHTML = '<option value="">Choose product...</option>';
        const sorted = [...state.products].sort((a, b) => a.scent_name.localeCompare(b.scent_name));
        const q = filterQuery.toLowerCase();
        sorted.forEach(p => {
            if (q && !p.scent_name.toLowerCase().includes(q)) return;
            const opt = document.createElement('option');
            opt.value = p.product_id;
            opt.textContent = `${p.scent_name} — ${p.volume_ml}ml [${p.category_name}]`;
            if (preselect && p.product_id == preselect) opt.selected = true;
            adjustProductSelect.appendChild(opt);
        });
    }

    function renderInventoryTable() {
        const filtered = state.products.filter(p => {
            const matchSearch = p.scent_name.toLowerCase().includes(state.filters.search.toLowerCase());
            const matchCat = state.filters.category === 'All' || p.category_id == state.filters.category;
            const matchAudience = state.filters.audience === 'All' || p.target_audience === state.filters.audience;
            const matchSize = state.filters.size === 'All' || p.size_id == state.filters.size;
            const stock = parseInt(p.stock);
            let matchStock = true;
            if (state.filters.stock === 'Out') matchStock = stock <= 0;
            else if (state.filters.stock === 'Low') matchStock = stock > 0 && stock <= 5;
            else if (state.filters.stock === 'In') matchStock = stock > 0;
            return matchSearch && matchCat && matchAudience && matchSize && matchStock;
        });

        if (filtered.length === 0) {
            inventoryTableBody.innerHTML = `<tr><td colspan="8" class="text-center">No products match your filters.</td></tr>`;
            return;
        }

        inventoryTableBody.innerHTML = '';
        filtered.forEach(p => {
            const stock = parseInt(p.stock);
            let stockClass = 'stock-indicator';
            let stockLabel = stock;
            if (stock <= 0) { stockClass += ' out-of-stock'; stockLabel = 'Out of Stock'; }
            else if (stock <= 5) { stockClass += ' low-stock'; stockLabel = `${stock} (Low)`; }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="product-row-name">${p.scent_name}</div>
                    <div class="product-row-sku">${p.target_audience} · ID #${p.product_id}</div>
                </td>
                <td>${p.category_name}</td>
                <td>${p.volume_ml} ml</td>
                <td><span class="badge badge-outline">${p.target_audience}</span></td>
                <td class="text-right">₱${parseFloat(p.retail_price).toFixed(2)}</td>
                <td class="text-right">₱${parseFloat(p.reseller_price).toFixed(2)}</td>
                <td class="text-center"><span class="${stockClass}">${stockLabel}</span></td>
                <td class="text-center">
                    <button class="quick-restock-btn" data-id="${p.product_id}">+ Adjust</button>
                </td>
            `;

            row.querySelector('.quick-restock-btn').addEventListener('click', () => {
                renderProductAdjustSelect(p.product_id);
                adjustMovementSelect.value = '1';
                adjustQuantity.value = '';
                adjustNotes.value = '';
                const rawCostInput = document.getElementById('adjustRawCost');
                if (rawCostInput) {
                    rawCostInput.value = '';
                    rawCostInput.placeholder = `Current: ₱${parseFloat(p.raw_price).toFixed(2)}`;
                }
                adjustStockModal.classList.add('active');
            });

            inventoryTableBody.appendChild(row);
        });
    }

    // --- Events ---
    function setupEventListeners() {
        themeToggleBtn.addEventListener('click', toggleTheme);

        searchInput.addEventListener('input', e => { state.filters.search = e.target.value; renderInventoryTable(); });
        categoryFilter.addEventListener('change', (e) => {
            state.filters.category = e.target.value;
            renderInventoryTable();
        });

        const sizeFilter = document.getElementById('sizeFilter');
        if (sizeFilter) {
            sizeFilter.addEventListener('change', (e) => {
                state.filters.size = e.target.value;
                renderInventoryTable();
            });
        }
        audienceFilter.addEventListener('change', e => { state.filters.audience = e.target.value; renderInventoryTable(); });
        stockLevelFilter.addEventListener('change', e => { state.filters.stock = e.target.value; renderInventoryStats(); renderInventoryTable(); });

        // Clickable stat chips toggle stock filter
        document.querySelectorAll('.stat-chip-clickable').forEach(chip => {
            chip.addEventListener('click', () => {
                const val = chip.dataset.stock;
                if (state.filters.stock === val) {
                    state.filters.stock = 'All';
                    stockLevelFilter.value = 'All';
                } else {
                    state.filters.stock = val;
                    stockLevelFilter.value = val;
                }
                renderInventoryStats();
                renderInventoryTable();
            });
        });

        openAdjustModalBtn.addEventListener('click', () => {
            renderProductAdjustSelect();
            document.getElementById('adjustStockForm').reset();
            const rawCostInput = document.getElementById('adjustRawCost');
            if (rawCostInput) {
                rawCostInput.placeholder = `Keep current / Enter new cost`;
            }
            adjustStockModal.classList.add('active');
        });

        openAddProductModalBtn.addEventListener('click', () => {
            addProductForm.reset();
            newCategoryGroup.style.display = 'none';
            newSizeGroup.style.display = 'none';
            addProductModal.classList.add('active');
        });

        openBulkPriceModalBtn.addEventListener('click', () => {
            bulkPriceForm.reset();
            bulkPriceModal.classList.add('active');
        });

        addCategorySelect.addEventListener('change', (e) => {
            newCategoryGroup.style.display = e.target.value === 'new' ? 'block' : 'none';
        });

        addSizeSelect.addEventListener('change', (e) => {
            newSizeGroup.style.display = e.target.value === 'new' ? 'block' : 'none';
        });

        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.target.closest('.modal').classList.remove('active');
                document.getElementById('adjustStockForm').reset();
                addProductForm.reset();
                bulkPriceForm.reset();
            });
        });

        // Close modal on escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                    document.getElementById('adjustStockForm').reset();
                    addProductForm.reset();
                    bulkPriceForm.reset();
                }
            }
        });

        // Close modal on clicking backdrop
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.getElementById('adjustStockForm').reset();
                    addProductForm.reset();
                    bulkPriceForm.reset();
                }
            });
        });

        saveAdjustmentSubmitBtn.addEventListener('click', submitStockAdjustment);
        saveProductSubmitBtn.addEventListener('click', submitNewProduct);
        saveBulkPriceSubmitBtn.addEventListener('click', submitBulkPriceAdjustment);

        const adjustProductSearch = document.getElementById('adjustProductSearch');
        if (adjustProductSearch) {
            adjustProductSearch.addEventListener('input', (e) => {
                renderProductAdjustSelect(adjustProductSelect.value, e.target.value);
            });
        }

        adjustProductSelect.addEventListener('change', () => {
            const selectedProdId = adjustProductSelect.value;
            const selectedProd = state.products.find(p => p.product_id == selectedProdId);
            const rawCostInput = document.getElementById('adjustRawCost');
            if (selectedProd && rawCostInput) {
                rawCostInput.placeholder = `Current: ₱${parseFloat(selectedProd.raw_price).toFixed(2)}`;
            } else if (rawCostInput) {
                rawCostInput.placeholder = `Keep current / Enter new cost`;
            }
        });
    }

    function submitStockAdjustment() {
        const prodId = adjustProductSelect.value;
        const qty = parseInt(adjustQuantity.value);
        const movId = parseInt(adjustMovementSelect.value);
        const notes = adjustNotes.value.trim();

        if (!prodId) { showToast('Please select a product', true); return; }
        if (isNaN(qty) || qty === 0) { showToast('Quantity cannot be zero', true); return; }

        saveAdjustmentSubmitBtn.disabled = true;
        saveAdjustmentSubmitBtn.textContent = 'Logging...';

        const rawCostInput = document.getElementById('adjustRawCost');
        const rawCostVal = rawCostInput ? parseFloat(rawCostInput.value) : NaN;
        const newRawCost = isNaN(rawCostVal) ? null : rawCostVal;

        fetch('../apis/inventory/adjust_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                product_id: prodId, 
                quantity_change: qty, 
                movement_id: movId, 
                notes: notes || null,
                new_raw_cost: newRawCost
            })
        })
            .then(res => res.json())
            .then(res => {
                saveAdjustmentSubmitBtn.disabled = false;
                saveAdjustmentSubmitBtn.textContent = 'Log Stock Movement';
                if (res.status === 'success') {
                    showToast('Stock adjustment logged successfully.');
                    document.getElementById('adjustStockForm').reset();
                    loadInventory();
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                saveAdjustmentSubmitBtn.disabled = false;
                saveAdjustmentSubmitBtn.textContent = 'Log Stock Movement';
                showToast('Network error', true);
            });
    }

    function populateProductCreationSelects() {
        // Category select
        addCategorySelect.innerHTML = '<option value="">Select Category...</option>';
        state.categories.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.category_id;
            opt.textContent = c.category_name;
            addCategorySelect.appendChild(opt);
        });
        const newCatOpt = document.createElement('option');
        newCatOpt.value = 'new';
        newCatOpt.textContent = 'Add New Category...';
        addCategorySelect.appendChild(newCatOpt);

        // Size select
        addSizeSelect.innerHTML = '<option value="">Select Size...</option>';
        state.sizes.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.size_id;
            opt.textContent = `${s.volume_ml} ml`;
            addSizeSelect.appendChild(opt);
        });
        const newSizeOpt = document.createElement('option');
        newSizeOpt.value = 'new';
        newSizeOpt.textContent = 'Add New Size...';
        addSizeSelect.appendChild(newSizeOpt);

        // Bulk edit category
        bulkCategorySelect.innerHTML = '<option value="">Select Category...</option>';
        state.categories.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.category_id;
            opt.textContent = c.category_name;
            bulkCategorySelect.appendChild(opt);
        });

        // Bulk edit size
        bulkSizeSelect.innerHTML = '<option value="">Select Size...</option>';
        state.sizes.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.size_id;
            opt.textContent = `${s.volume_ml} ml`;
            bulkSizeSelect.appendChild(opt);
        });
    }

    function submitNewProduct() {
        const scent = addScentName.value.trim();
        const aud = addTargetAudience.value;
        const cat = addCategorySelect.value;
        const newCat = addNewCategoryName.value.trim();
        const size = addSizeSelect.value;
        const newSize = parseInt(addNewVolumeMl.value);
        const retail = parseFloat(addRetailPrice.value);
        const reseller = parseFloat(addResellerPrice.value);
        const raw = parseFloat(addRawPrice.value) || 0.00;
        const commRetail    = addCommissionRetail.value    !== '' ? parseFloat(addCommissionRetail.value)    : null;
        const commReseller  = addCommissionReseller.value  !== '' ? parseFloat(addCommissionReseller.value)  : null;
        const stock = parseInt(addInitialStock.value) || 0;

        if (!scent) { showToast('Please enter the perfume scent name', true); return; }
        if (!cat) { showToast('Please select a category', true); return; }
        if (cat === 'new' && !newCat) { showToast('Please enter the new category name', true); return; }
        if (!size) { showToast('Please select a size', true); return; }
        if (size === 'new' && (isNaN(newSize) || newSize <= 0)) { showToast('Please enter a valid bottle volume size', true); return; }
        if (isNaN(retail) || retail <= 0) { showToast('Retail price must be greater than zero', true); return; }
        if (isNaN(reseller) || reseller <= 0) { showToast('Reseller price must be greater than zero', true); return; }

        saveProductSubmitBtn.disabled = true;
        saveProductSubmitBtn.textContent = 'Saving...';

        fetch('../apis/inventory/create_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                scent_name: scent,
                target_audience: aud,
                category_id: cat,
                new_category_name: newCat,
                size_id: size,
                new_volume_ml: newSize,
                retail_price: retail,
                reseller_price: reseller,
                raw_price: raw,
                commission_retail:    commRetail,
                commission_reseller:  commReseller,
                initial_stock: stock
            })
        })
            .then(res => res.json())
            .then(res => {
                saveProductSubmitBtn.disabled = false;
                saveProductSubmitBtn.textContent = 'Create Product';
                if (res.status === 'success') {
                    showToast('Product registered and added to catalog.');
                    addProductForm.reset();
                    loadInventory();
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                saveProductSubmitBtn.disabled = false;
                saveProductSubmitBtn.textContent = 'Create Product';
                showToast('Network error while creating product', true);
            });
    }

    function submitBulkPriceAdjustment() {
        const cat = bulkCategorySelect.value;
        const size = bulkSizeSelect.value;
        const retail = parseFloat(bulkRetailPrice.value) || 0.00;
        const reseller = parseFloat(bulkResellerPrice.value) || 0.00;
        const raw = -1.00;
        const commRetail = bulkCommissionRetail.value !== '' ? parseFloat(bulkCommissionRetail.value) : -1.00;
        const commReseller = bulkCommissionReseller.value !== '' ? parseFloat(bulkCommissionReseller.value) : -1.00;

        if (!cat) { showToast('Please select a category', true); return; }
        if (!size) { showToast('Please select a bottle size', true); return; }
        if (retail <= 0 && reseller <= 0 && raw < 0 && commRetail < 0 && commReseller < 0) {
            showToast('Enter at least one price adjustment value', true);
            return;
        }

        saveBulkPriceSubmitBtn.disabled = true;
        saveBulkPriceSubmitBtn.textContent = 'Applying...';

        fetch('../apis/inventory/update_prices_bulk.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                category_id: cat,
                size_id: size,
                retail_price: retail,
                reseller_price: reseller,
                raw_price: raw,
                commission_retail: commRetail,
                commission_reseller: commReseller
            })
        })
            .then(res => res.json())
            .then(res => {
                saveBulkPriceSubmitBtn.disabled = false;
                saveBulkPriceSubmitBtn.textContent = 'Apply Price Adjustment';
                if (res.status === 'success') {
                    showToast(res.message);
                    bulkPriceForm.reset();
                    loadInventory();
                } else {
                    showToast(res.message, true);
                }
            })
            .catch(() => {
                saveBulkPriceSubmitBtn.disabled = false;
                saveBulkPriceSubmitBtn.textContent = 'Apply Price Adjustment';
                showToast('Network error while executing bulk price update', true);
            });
    }
});

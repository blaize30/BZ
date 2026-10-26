// js/ledger.js

document.addEventListener('DOMContentLoaded', () => {

    let state = {
        allEntries: [],      // raw data from API
        filtered: [],        // currently visible after all filters
        categories: [],
        filters: {
            search: '',
            movement: 'All',
            category: 'All',
            direction: 'All',
            size: 'All',
            dateFrom: '',
            dateTo: ''
        }
    };

    // --- DOM ---
    const ledgerTableBody  = document.getElementById('ledgerTableBody');
    const ledgerSearch     = document.getElementById('ledgerSearch');
    const movementFilter   = document.getElementById('movementFilter');
    const categoryFilter   = document.getElementById('categoryFilter');
    const directionFilter  = document.getElementById('directionFilter');
    const dateFrom         = document.getElementById('dateFrom');
    const dateTo           = document.getElementById('dateTo');
    const resetFiltersBtn  = document.getElementById('resetFiltersBtn');
    const themeToggleBtn   = document.getElementById('themeToggleBtn');
    const toast            = document.getElementById('notificationToast');

    // Stats chips
    const statEntries = document.getElementById('statEntries');
    const statIn      = document.getElementById('statIn');
    const statOut     = document.getElementById('statOut');
    const statNet     = document.getElementById('statNet');

    // --- Init ---
    initTheme();
    loadLedger();
    setupEventListeners();

    // --- Theme ---
    function initTheme() {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark') { document.body.classList.add('dark'); updateThemeButton(true); }
        else { updateThemeButton(false); }
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

    function showToast(msg, isError = false) {
        toast.textContent = msg;
        toast.style.backgroundColor = isError ? '#ff3b30' : 'var(--accent-color)';
        toast.style.color = isError ? '#fff' : 'var(--accent-text)';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // --- Load ---
    function loadLedger() {
        ledgerTableBody.innerHTML = `<tr><td colspan="8" class="text-center">Loading ledger entries...</td></tr>`;

        fetch('../apis/inventory/get_ledger.php')
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    state.allEntries = res.data;

                    // Build category and size lists from entries
                    const catMap = {};
                    const sizeMap = {};
                    res.data.forEach(e => { 
                        catMap[e.category_name] = true;
                        if (e.volume_ml) sizeMap[e.volume_ml] = true;
                    });
                    state.categories = Object.keys(catMap).sort();
                    state.sizes = Object.keys(sizeMap).map(Number).sort((a,b) => a-b);
                    
                    renderCategoryFilter();
                    renderSizeFilter();

                    applyFilters();
                } else {
                    showToast('Failed to load ledger: ' + res.message, true);
                }
            })
            .catch(() => showToast('Server connection failed', true));
    }

    // --- Build category dropdown from actual data ---
    function renderCategoryFilter() {
        categoryFilter.innerHTML = '<option value="All">All Categories</option>';
        state.categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            categoryFilter.appendChild(opt);
        });
    }

    function renderSizeFilter() {
        const sizeFilter = document.getElementById('sizeFilter');
        if (sizeFilter) {
            sizeFilter.innerHTML = '<option value="All">All Sizes</option>';
            state.sizes.forEach(sz => {
                const opt = document.createElement('option');
                opt.value = sz;
                opt.textContent = `${sz} ml`;
                sizeFilter.appendChild(opt);
            });
            sizeFilter.value = state.filters.size;
        }
    }

    // --- Core filter logic ---
    function applyFilters() {
        const { search, movement, category, direction, dateFrom: dfrom, dateTo: dto } = state.filters;

        state.filtered = state.allEntries.filter(entry => {
            // Text search — scent name or notes
            const hay = (entry.scent_name + ' ' + (entry.notes || '')).toLowerCase();
            if (search && !hay.includes(search.toLowerCase())) return false;

            // Movement type
            if (movement !== 'All' && entry.movement_name !== movement) return false;

            // Category
            if (category !== 'All' && entry.category_name !== category) return false;

            // Direction
            const qty = parseInt(entry.quantity_change);
            if (direction === 'in'  && qty <= 0) return false;
            if (direction === 'out' && qty >= 0) return false;

            // Size
            if (state.filters.size !== 'All' && entry.volume_ml != state.filters.size) return false;

            // Date range — compare date portion only
            if (dfrom || dto) {
                const entryDate = entry.timestamp.substring(0, 10); // "YYYY-MM-DD"
                if (dfrom && entryDate < dfrom) return false;
                if (dto   && entryDate > dto)   return false;
            }

            return true;
        });

        renderTable();
        renderStats();
    }

    // --- Render the ledger table ---
    function renderTable() {
        if (state.filtered.length === 0) {
            ledgerTableBody.innerHTML = `<tr><td colspan="8" class="text-center" style="padding:48px 0; color:var(--text-muted)">No ledger entries match your filters.</td></tr>`;
            return;
        }

        ledgerTableBody.innerHTML = '';
        state.filtered.forEach(entry => {
            const qty     = parseInt(entry.quantity_change);
            const isPos   = qty > 0;
            const qtyStr  = isPos ? `+${qty}` : `${qty}`;
            const movName = entry.movement_name.toLowerCase();

            // Formatted timestamp
            const d = new Date(entry.timestamp);
            const dateStr = d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
            const timeStr = d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div style="font-size:13px; font-weight:600">${dateStr}</div>
                    <div style="font-size:11px; color:var(--text-secondary)">${timeStr}</div>
                </td>
                <td>
                    <div style="font-family:var(--font-serif); font-size:15px; font-weight:600">${entry.scent_name}</div>
                </td>
                <td style="font-size:13px">${entry.category_name}</td>
                <td style="font-size:13px">${entry.volume_ml} ml</td>
                <td class="text-center">
                    <span class="movement-chip ${movName}">${entry.movement_name}</span>
                </td>
                <td class="text-center">
                    <span class="qty-badge ${isPos ? 'positive' : 'negative'}">${qtyStr}</span>
                </td>
                <td class="order-ref">
                    ${entry.order_id ? `#${entry.order_id}` : '—'}
                </td>
                <td class="notes-cell" title="${entry.notes || ''}">
                    ${entry.notes || '<span style="color:var(--text-muted)">—</span>'}
                </td>
            `;
            ledgerTableBody.appendChild(row);
        });
    }

    // --- Stats strip ---
    function renderStats() {
        const count = state.filtered.length;
        let totalIn = 0, totalOut = 0;
        state.filtered.forEach(e => {
            const q = parseInt(e.quantity_change);
            if (q > 0) totalIn += q;
            else totalOut += q;
        });
        const net = totalIn + totalOut;

        statEntries.textContent = count;
        statIn.textContent = `+${totalIn}`;
        statOut.textContent = totalOut === 0 ? '0' : `${totalOut}`;
        statNet.textContent = net >= 0 ? `+${net}` : `${net}`;
        statNet.className = 'stat-value ' + (net >= 0 ? 'positive' : 'negative');
    }

    // --- Events ---
    function setupEventListeners() {
        themeToggleBtn.addEventListener('click', toggleTheme);

        ledgerSearch.addEventListener('input', e => {
            state.filters.search = e.target.value;
            applyFilters();
        });

        movementFilter.addEventListener('change', e => {
            state.filters.movement = e.target.value;
            applyFilters();
        });

        categoryFilter.addEventListener('change', e => {
            state.filters.category = e.target.value;
            applyFilters();
        });

        directionFilter.addEventListener('change', e => {
            state.filters.direction = e.target.value;
            applyFilters();
        });

        const sizeFilter = document.getElementById('sizeFilter');
        if (sizeFilter) {
            sizeFilter.addEventListener('change', (e) => {
                state.filters.size = e.target.value;
                applyFilters();
            });
        }

        dateFrom.addEventListener('change', e => {
            state.filters.dateFrom = e.target.value;
            applyFilters();
        });

        dateTo.addEventListener('change', e => {
            state.filters.dateTo = e.target.value;
            applyFilters();
        });

        resetFiltersBtn.addEventListener('click', () => {
            state.filters = { search: '', movement: 'All', category: 'All', direction: 'All', size: 'All', dateFrom: '', dateTo: '' };
            ledgerSearch.value = '';
            movementFilter.value = 'All';
            categoryFilter.value = 'All';
            directionFilter.value = 'All';
            if (sizeFilter) sizeFilter.value = 'All';
            dateFrom.value = '';
            dateTo.value = '';
            applyFilters();
        });
    }
});

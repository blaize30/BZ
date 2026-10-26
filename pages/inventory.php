<?php
// pages/inventory.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/inventory.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="pos.php" class="sidebar-logo">BZ</a>
            <nav class="sidebar-menu">
                <a href="pos.php" class="sidebar-link" data-tooltip="POS Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </a>
                <a href="orders.php" class="sidebar-link" data-tooltip="Sales History">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 0A48.536 48.536 0 0 1 12 3m0 0c-2.906 0-5.742.194-8.52.57M12 3v15m0-15a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                    </svg>
                </a>
                <a href="inventory.php" class="sidebar-link active" data-tooltip="Stock Management">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </a>
                <a href="ledger.php" class="sidebar-link" data-tooltip="Stock Ledger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                    </svg>
                </a>
                <a href="people.php" class="sidebar-link" data-tooltip="Reps & Customers">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 12 20.25a11.38 11.38 0 0 1-3-.113V19.13m0 0a9.38 9.38 0 0 1-2.625.372 9.337 9.337 0 0 1-4.121-.952 4.125 4.125 0 0 1 7.533-2.493M9 19.13v-.003c0-1.113.285-2.16.786-3.07m0 0A10.97 10.97 0 0 1 12 15c.668 0 1.32.06 1.954.177m-4.168 0A10.97 10.97 0 0 0 12 15c.668 0 1.32.06 1.954.177M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5 1.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm-15 0a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" />
                    </svg>
                </a>
            </nav>
            <div class="sidebar-footer">
                <button class="theme-toggle" id="themeToggleBtn" aria-label="Toggle Theme">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
            </div>
        </aside>

        <!-- Inventory Workspace (full width now) -->
        <main class="content-wrapper">
            <section class="inventory-section">
                <div class="section-header">
                    <div>
                        <span class="subtitle">Bloom & Scentz</span>
                        <h1 class="section-title">Product Stock</h1>
                    </div>
                    <div class="header-actions">
                        <a href="ledger.php" class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            View Ledger Log
                        </a>
                        <button class="btn" id="openBulkPriceModalBtn">
                            ₱ Price Adjustment
                        </button>
                        <button class="btn btn-primary" id="openAddProductModalBtn">
                            + Add Product
                        </button>
                        <button class="btn btn-primary" id="openAdjustModalBtn">
                            Adjust / Restock
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="table-filters">
                    <div class="search-box">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="inventorySearch" class="form-control search-input" placeholder="Filter by scent name...">
                    </div>

                    <select id="categoryFilter" class="form-control select-filter">
                        <option value="All">All Categories</option>
                    </select>

                    <select id="audienceFilter" class="form-control select-filter">
                        <option value="All">All Fragrance Style</option>
                        <option value="Him">Him</option>
                        <option value="Her">Her</option>
                        <option value="Unisex">Unisex</option>
                    </select>

                    <select id="stockLevelFilter" class="form-control select-filter">
                        <option value="All">All Stock Levels</option>
                        <option value="Out">Out of Stock</option>
                        <option value="Low">Low Stock (≤ 5)</option>
                        <option value="In">In Stock</option>
                    </select>
                    
                    <select id="sizeFilter" class="form-control select-filter">
                        <option value="All">All Sizes</option>
                    </select>
                </div>

                <!-- Inventory Stats Bar -->
                <div class="inventory-stats-bar" id="inventoryStatsBar">
                    <div class="stat-chip stat-chip-clickable" id="chipInStock" data-stock="In">
                        <span class="stat-label">In Stock</span>
                        <span class="stat-value positive" id="statInStock">—</span>
                    </div>
                    <div class="stat-chip stat-chip-clickable" id="chipLowStock" data-stock="Low">
                        <span class="stat-label">Low Stock</span>
                        <span class="stat-value warning" id="statLowStock">—</span>
                    </div>
                    <div class="stat-chip stat-chip-clickable" id="chipOutOfStock" data-stock="Out">
                        <span class="stat-label">Out of Stock</span>
                        <span class="stat-value muted" id="statOutOfStock">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total SKUs</span>
                        <span class="stat-value" id="statTotalSkus">—</span>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Scent Name</th>
                                <th>Category</th>
                                <th>Size</th>
                                <th>Target</th>
                                <th class="text-right">Retail Price</th>
                                <th class="text-right">Reseller Price</th>
                                <th class="text-center">Stock Qty</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <tr>
                                <td colspan="8" class="text-center">Loading stock details...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Stock Adjust / Restock Modal -->
    <div class="modal" id="adjustStockModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Inventory Stock Adjustment</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="adjustStockForm">
                    <div class="form-group">
                        <label class="form-label" for="adjustProductSelect">Select Perfume Product</label>
                        <input type="text" id="adjustProductSearch" class="form-control" placeholder="Search scent name..." style="margin-bottom: 8px;">
                        <select id="adjustProductSelect" class="form-control" required>
                            <option value="">Choose product...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="adjustMovementSelect">Movement Type</label>
                        <select id="adjustMovementSelect" class="form-control" required>
                            <option value="1">Restock (+)</option>
                            <option value="3">Manual Correction (+/-)</option>
                            <option value="4">Customer Return (+)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="adjustQuantity">Quantity Change</label>
                        <input type="number" id="adjustQuantity" class="form-control" required placeholder="e.g. 50" min="-1000" max="1000">
                        <span class="field-hint">Enter positive integers for additions, or negative for stock write-offs.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="adjustRawCost">New Raw Cost (Optional)</label>
                        <input type="number" id="adjustRawCost" class="form-control" placeholder="Keep current / Enter new cost" min="0.00" step="0.01">
                        <span class="field-hint">Leave blank to keep the current raw cost.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="adjustNotes">Log Notes</label>
                        <textarea id="adjustNotes" class="form-control" rows="3" placeholder="e.g. Received batch 2 shipment / damaged bottle write-off"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveAdjustmentSubmitBtn">Log Stock Movement</button>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title">Add New Perfume</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" style="flex: 2; margin-bottom: 0;">
                            <label class="form-label" for="addScentName">Scent Name</label>
                            <input type="text" id="addScentName" class="form-control" placeholder="e.g. Dior Sauvage" required>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addTargetAudience">Target Audience</label>
                            <select id="addTargetAudience" class="form-control" required>
                                <option value="Him">Him</option>
                                <option value="Her">Her</option>
                                <option value="Unisex" selected>Unisex</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addCategorySelect">Category</label>
                            <select id="addCategorySelect" class="form-control" required>
                                <option value="">Select Category...</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addSizeSelect">Bottle Size</label>
                            <select id="addSizeSelect" class="form-control" required>
                                <option value="">Select Size...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row" id="newCategorySizeRow" style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" id="newCategoryGroup" style="display: none; flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addNewCategoryName">New Category Name</label>
                            <input type="text" id="addNewCategoryName" class="form-control" placeholder="e.g. Oil-Based Perfume">
                        </div>
                        <div class="form-group" id="newSizeGroup" style="display: none; flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addNewVolumeMl">New Size Volume (ml)</label>
                            <input type="number" id="addNewVolumeMl" class="form-control" placeholder="e.g. 100" min="1" max="5000">
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 12px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addRetailPrice">Retail Price (₱)</label>
                            <input type="number" id="addRetailPrice" class="form-control" placeholder="0.00" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addResellerPrice">Reseller Price (₱)</label>
                            <input type="number" id="addResellerPrice" class="form-control" placeholder="0.00" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-row" style="display: flex; gap: 10px; margin-bottom: 4px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addRawPrice">Raw Material Cost (₱)</label>
                            <input type="number" id="addRawPrice" class="form-control" placeholder="0.00" min="0" step="0.01" value="0.00">
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addCommissionRetail">Commission — Retail (₱)</label>
                            <input type="number" id="addCommissionRetail" class="form-control" placeholder="auto" min="0" step="0.01">
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="addCommissionReseller">Commission — Reseller (₱)</label>
                            <input type="number" id="addCommissionReseller" class="form-control" placeholder="auto" min="0" step="0.01">
                        </div>
                    </div>
                    <span class="field-hint" style="margin-bottom: 12px; display: block;">Leave commission fields blank to auto-inherit from the category/size defaults.</span>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="addInitialStock">Initial Stock Level</label>
                        <input type="number" id="addInitialStock" class="form-control" placeholder="e.g. 50" min="0" value="0">
                        <span class="field-hint" style="margin-top: 4px; display: block;">Entering a stock quantity will seed the ledger with a restock entry.</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveProductSubmitBtn">Create Product</button>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Prices Modal -->
    <div class="modal" id="bulkPriceModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title">Bulk Price Adjustment</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bulkPriceForm">
                    <p style="font-size: 13px; margin-bottom: 12px; color: var(--text-secondary); line-height: 1.4;">Apply bulk pricing changes to all products matching a specific category and bottle size. Historical invoices will remain unchanged.</p>
                    
                    <div style="display: flex; gap: 12px; margin-bottom: 10px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkCategorySelect">Select Category</label>
                            <select id="bulkCategorySelect" class="form-control" required>
                                <option value="">Select Category...</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkSizeSelect">Select Bottle Size</label>
                            <select id="bulkSizeSelect" class="form-control" required>
                                <option value="">Select Size...</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 10px;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkRetailPrice">New Retail Price</label>
                            <input type="number" id="bulkRetailPrice" class="form-control" placeholder="Keep current" min="0.01" step="0.01">
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkResellerPrice">New Reseller Price</label>
                            <input type="number" id="bulkResellerPrice" class="form-control" placeholder="Keep current" min="0.01" step="0.01">
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-bottom: 0;">
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkCommissionRetail">New Commission — Retail</label>
                            <input type="number" id="bulkCommissionRetail" class="form-control" placeholder="Keep current" min="0.00" step="0.01">
                        </div>
                        <div class="form-group" style="flex: 1; margin-bottom: 0;">
                            <label class="form-label" for="bulkCommissionReseller">New Commission — Reseller</label>
                            <input type="number" id="bulkCommissionReseller" class="form-control" placeholder="Keep current" min="0.00" step="0.01">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveBulkPriceSubmitBtn">Apply Price Adjustment</button>
            </div>
        </div>
    </div>

    <div id="notificationToast" class="toast"></div>
    <script src="../js/inventory.js"></script>
</body>
</html>

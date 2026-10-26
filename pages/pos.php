<?php
// pages/pos.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom & Scentz</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/pos.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="pos.php" class="sidebar-logo">BZ</a>
            <nav class="sidebar-menu">
                <a href="pos.php" class="sidebar-link active" data-tooltip="POS Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                </a>
                <a href="orders.php" class="sidebar-link" data-tooltip="Sales History">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 0A48.536 48.536 0 0 1 12 3m0 0c-2.906 0-5.742.194-8.52.57M12 3v15m0-15a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                    </svg>
                </a>
                <a href="inventory.php" class="sidebar-link" data-tooltip="Stock Management">
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

        <!-- POS Workspace -->
        <main class="content-wrapper">
            <!-- Products Catalog Panel (Middle) -->
            <section class="catalog-panel">
                <!-- Header Controls -->
                <div class="catalog-header">
                    <div class="brand-title-area">
                        <span class="subtitle">Bloom & Scentz</span>
                        <h1 class="catalog-title">Perfumes</h1>
                    </div>
                    
                    <div class="filter-controls">
                        <!-- Search Scent -->
                        <div class="search-box">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="searchInput" class="form-control search-input" placeholder="Search scent...">
                        </div>

                        <!-- Size Filter -->
                        <select id="sizeFilter" class="form-control select-filter">
                            <option value="All">All Sizes</option>
                        </select>
                        
                        <!-- Target Audience Filter -->
                        <div class="audience-filters">
                            <button class="audience-btn active" data-audience="All">All</button>
                            <button class="audience-btn" data-audience="Him">Him</button>
                            <button class="audience-btn" data-audience="Her">Her</button>
                            <button class="audience-btn" data-audience="Unisex">Unisex</button>
                        </div>
                    </div>
                </div>

                <!-- Category Navigation (Tabs) -->
                <div class="category-tabs-container">
                    <div class="category-tabs" id="categoryTabs">
                        <!-- Dynamic Categories will go here -->
                        <button class="category-tab active" data-category="All">All Categories</button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid-container">
                    <div class="products-grid" id="productsGrid">
                        <!-- Dynamic Product Cards will be rendered here -->
                        <div class="loading-state">
                            <div class="spinner"></div>
                            <p>Curating catalog...</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cart / Checkout Panel (Right) -->
            <section class="checkout-panel">
                <div class="panel-header">
                    <h2>Current Order</h2>
                </div>

                <!-- Metadata Selection -->
                <div class="order-metadata">
                    <!-- Sales Representative Selection -->
                    <div class="meta-field">
                        <label class="form-label" for="repSelect">Sales Representative</label>
                        <div class="flex gap-8">
                            <select id="repSelect" class="form-control">
                                <option value="">Select Representative</option>
                            </select>
                            <button class="btn btn-icon" id="addRepBtn" title="Add Representative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Customer Selection -->
                    <div class="meta-field">
                        <label class="form-label" for="customerSelect">Customer</label>
                        <div class="flex gap-8">
                            <select id="customerSelect" class="form-control">
                                <option value="">Select Customer</option>
                            </select>
                            <button class="btn btn-icon" id="addCustomerBtn" title="Add Customer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Pricing Mode & Theme Toggles -->
                    <div class="pricing-toggle-area">
                        <span class="toggle-label">Pricing Tier</span>
                        <div class="toggle-switch-container">
                            <button class="pricing-tier-btn active" id="tierRetail" data-tier="retail">Retail</button>
                            <button class="pricing-tier-btn" id="tierReseller" data-tier="reseller">Reseller</button>
                            <button class="pricing-tier-btn" id="tierSpecial" data-tier="special" disabled>Special</button>
                        </div>
                    </div>
                </div>

                <!-- Scrollable Order Items List -->
                <div class="cart-items-container">
                    <div class="cart-items" id="cartItemsList">
                        <!-- Empty Cart View -->
                        <div class="empty-cart-state">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="48" height="48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <p>No items added yet</p>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary and Payment Mode -->
                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summarySubtotal">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Discount (₱)</span>
                        <input type="number" id="discountInput" class="form-control discount-input text-right" placeholder="0.00" min="0" value="0.00">
                    </div>
                    <div class="summary-row total-row">
                        <span>Total Due</span>
                        <span id="summaryTotal">₱0.00</span>
                    </div>

                    <!-- Payment Status Toggle -->
                    <div class="payment-status-section" style="margin-bottom: 4px; display: flex; justify-content: space-between; align-items: center;">
                        <span class="form-label" style="margin-bottom: 0;">Payment Status</span>
                        <div class="toggle-switch-container" style="display: flex; padding: 2px;">
                            <button class="pricing-tier-btn active" id="statusPaid" data-status="Paid" style="font-size: 10px; padding: 3px 10px;">Paid</button>
                            <button class="pricing-tier-btn" id="statusUnpaid" data-status="Unpaid" style="font-size: 10px; padding: 3px 10px;">Unpaid</button>
                        </div>
                    </div>

                    <!-- Payment Mode -->
                    <div class="payment-modes-section" id="paymentModesSection">
                        <span class="form-label">Payment Mode</span>
                        <div class="payment-chips">
                            <button class="payment-chip active" data-mode="Cash">Cash</button>
                            <button class="payment-chip" data-mode="GCash">GCash</button>
                            <button class="payment-chip" data-mode="Bank Transfer">Bank Transfer</button>
                            <button class="payment-chip" data-mode="Other">Other</button>
                        </div>
                    </div>

                    <button class="btn btn-primary checkout-action-btn" id="checkoutSubmitBtn">
                        Confirm & Process Checkout
                    </button>
                </div>
            </section>
        </main>
    </div>

    <!-- --- Modals --- -->

    <!-- Add Sales Rep Modal -->
    <div class="modal" id="addRepModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">New Sales Representative</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addRepForm">
                    <div class="form-group">
                        <label class="form-label" for="newRepName">Representative Name</label>
                        <input type="text" id="newRepName" class="form-control" required placeholder="e.g. Emma Wang">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveRepSubmitBtn">Save Representative</button>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal" id="addCustomerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">New Customer Details</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="form-group">
                        <label class="form-label" for="newCustName">Customer Name</label>
                        <input type="text" id="newCustName" class="form-control" required placeholder="e.g. John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="newCustContact">Contact Number</label>
                        <input type="text" id="newCustContact" class="form-control" placeholder="e.g. +63 917 123 4567">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="newCustEmail">Email Address</label>
                        <input type="email" id="newCustEmail" class="form-control" placeholder="e.g. customer@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="newCustType">Customer Type</label>
                        <select id="newCustType" class="form-control">
                            <option value="Retailer">Retailer</option>
                            <option value="Reseller">Reseller</option>
                            <option value="Special">Special Price Profile</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveCustomerSubmitBtn">Save Customer</button>
            </div>
        </div>
    </div>

    <!-- Waybill Preview Modal -->
    <div class="modal" id="waybillModal">
        <div class="modal-content" style="max-width: 4.8in; background: var(--bg-secondary);">
            <div class="modal-header">
                <h3 class="modal-title">Waybill Print Preview</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px; display: flex; justify-content: center; overflow-y: auto; background-color: var(--bg-secondary);">
                <!-- Physical paper dimensions representation (4.25 x 6.5 inches) -->
                <div id="waybillPaper" class="waybill-paper">
                    <!-- Dynamic content will go here -->
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-primary);">
                <button class="btn close-modal-btn">Close</button>
                <button class="btn btn-primary" id="printWaybillBtn">Print Waybill</button>
            </div>
        </div>
    </div>

    <!-- Floating Notification -->
    <div id="notificationToast" class="toast"></div>

    <script src="../js/pos.js"></script>
</body>
</html>

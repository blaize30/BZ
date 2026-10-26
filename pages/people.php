<?php
// pages/people.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory — Bloom & Scentz</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/people.css">
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
                <a href="people.php" class="sidebar-link active" data-tooltip="Reps & Customers">
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

        <!-- Directory Workspace -->
        <main class="content-wrapper">
            <section class="directory-section">
                
                <!-- Page Header -->
                <div class="section-header">
                    <div>
                        <span class="subtitle">Bloom & Scentz</span>
                        <h1 class="section-title">Directory</h1>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-primary" id="addRepDirectBtn">Register Representative</button>
                        <button class="btn btn-primary" id="addCustomerDirectBtn" style="display: none;">Register Customer</button>
                    </div>
                </div>

                <!-- Directory Selector Tabs & Filter Bar -->
                <div class="directory-toolbar">
                    <div class="directory-tabs">
                        <button class="directory-tab active" id="tabRepsBtn">Sales Representatives</button>
                        <button class="directory-tab" id="tabCustomersBtn">Customers</button>
                    </div>

                    <div class="filter-controls">
                        <!-- Search Box -->
                        <div class="search-box">
                            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="directorySearch" class="form-control search-input" placeholder="Search representatives...">
                        </div>

                        <!-- Customer Type Filter (hidden when Reps tab is active) -->
                        <select id="customerTypeFilter" class="form-control select-filter" style="display: none;">
                            <option value="All">All Types</option>
                            <option value="Retailer">Retailer</option>
                            <option value="Reseller">Reseller</option>
                            <option value="Special">Special Price</option>
                        </select>
                    </div>
                </div>

                <!-- Grid Summary Stats Strip -->
                <div class="directory-stats-bar" id="directoryStatsBar">
                    <!-- Rep stats default -->
                    <div class="stat-chip">
                        <span class="stat-label">Total Representatives</span>
                        <span class="stat-value" id="statCount">0</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Cumulative Rep Sales</span>
                        <span class="stat-value" id="statSales">₱0.00</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Unpaid Commissions</span>
                        <span class="stat-value" id="statCommission">₱0.00</span>
                    </div>
                </div>

                <!-- Reps Table Container -->
                <div class="table-container" id="repsTableContainer">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Representative ID</th>
                                <th>Representative Name</th>
                                <th class="text-right">Total Items Sold</th>
                                <th class="text-right">Accumulated Sales</th>
                                <th class="text-right">Accumulated Commission</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="repsTableBody">
                            <tr>
                                <td colspan="6" class="text-center">Loading representatives...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Customers Table Container (hidden initially) -->
                <div class="table-container" id="customersTableContainer" style="display: none;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer Details</th>
                                <th class="text-center">Pricing Profile</th>
                                <th class="text-center">Total Units Bought</th>
                                <th class="text-right">Total Purchases</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTableBody">
                            <tr>
                                <td colspan="5" class="text-center">Loading customers...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </section>
        </main>
    </div>

    <!-- Customer Order History Modal -->
    <div class="modal" id="customerHistoryModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="historyCustomerName">Customer Order History</h3>
                    <span class="subtitle" id="historyCustomerType" style="text-transform: uppercase; font-size: 11px;">Profile: Retailer</span>
                </div>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 450px; overflow-y: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Sales Rep</th>
                            <th class="text-center">Items</th>
                            <th class="text-right">Total Invoice</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">Delivery Status</th>
                        </tr>
                    </thead>
                    <tbody id="customerHistoryTableBody">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Close Logs</button>
            </div>
        </div>
    </div>

    <!-- Manage Special Prices Modal -->
    <div class="modal" id="specialPricesModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title" id="specialCustomerName">Manage Custom Prices</h3>
                    <span class="subtitle">Set unique item prices for this customer</span>
                </div>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Add Price Form -->
                <form id="addSpecialPriceForm" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px dashed var(--border-color);">
                    <div style="flex: 1.5;">
                        <label class="form-label" for="specialCategorySelect">Select Category</label>
                        <select id="specialCategorySelect" class="form-control" required>
                            <option value="">Choose category...</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" for="specialSizeSelect">Select Size</label>
                        <select id="specialSizeSelect" class="form-control" required>
                            <option value="">Choose size...</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label class="form-label" for="specialPriceInput">Custom Price (₱)</label>
                        <input type="number" id="specialPriceInput" class="form-control" placeholder="0.00" min="0.01" step="0.01" required>
                    </div>
                    <button class="btn btn-primary" id="saveSpecialPriceBtn" type="button" style="height: 38px;">Apply Override</button>
                </form>

                <!-- Current Override Table -->
                <div style="font-size: 13px; font-weight: 600; margin-bottom: 8px;">Active Custom Price Overrides</div>
                <div style="max-height: 250px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 6px;">
                    <table class="table" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th>Category & Size</th>
                                <th class="text-right">Custom Price</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="specialPricesTableBody">
                            <!-- Filled by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Done</button>
            </div>
        </div>
    </div>

    <!-- Add Representative Modal -->
    <div class="modal" id="addRepModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Register Representative</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addRepForm">
                    <div class="form-group">
                        <label class="form-label" for="addRepName">Representative Name</label>
                        <input type="text" id="addRepName" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveRepDirectBtn">Register</button>
            </div>
        </div>
    </div>

    <!-- Edit Representative Modal -->
    <div class="modal" id="editRepModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Edit Representative</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editRepForm">
                    <input type="hidden" id="editRepId">
                    <div class="form-group">
                        <label class="form-label" for="editRepName">Representative Name</label>
                        <input type="text" id="editRepName" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="updateRepDirectBtn">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal" id="addCustomerModal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">Register Customer</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    <div class="form-group">
                        <label class="form-label" for="addCustomerName">Customer Name</label>
                        <input type="text" id="addCustomerName" class="form-control" placeholder="e.g. Maria Clara" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="addCustomerContact">Contact Number</label>
                        <input type="text" id="addCustomerContact" class="form-control" placeholder="e.g. 09171234567">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="addCustomerEmail">Email Address</label>
                        <input type="email" id="addCustomerEmail" class="form-control" placeholder="e.g. maria@gmail.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="addCustomerType">Profile Pricing Type</label>
                        <select id="addCustomerType" class="form-control" required>
                            <option value="Retailer" selected>Retailer</option>
                            <option value="Reseller">Reseller</option>
                            <option value="Special">Special Price Profile</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="saveCustomerDirectBtn">Register</button>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal" id="editCustomerModal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">Edit Customer Details</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm">
                    <input type="hidden" id="editCustomerId">
                    <div class="form-group">
                        <label class="form-label" for="editCustomerName">Customer Name</label>
                        <input type="text" id="editCustomerName" class="form-control" placeholder="e.g. Maria Clara" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editCustomerContact">Contact Number</label>
                        <input type="text" id="editCustomerContact" class="form-control" placeholder="e.g. 09171234567">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editCustomerEmail">Email Address</label>
                        <input type="email" id="editCustomerEmail" class="form-control" placeholder="e.g. maria@gmail.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editCustomerType">Profile Pricing Type</label>
                        <select id="editCustomerType" class="form-control" required>
                            <option value="Retailer">Retailer</option>
                            <option value="Reseller">Reseller</option>
                            <option value="Special">Special Price Profile</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn">Cancel</button>
                <button class="btn btn-primary" id="updateCustomerDirectBtn">Save Changes</button>
            </div>
        </div>
    </div>

    <div id="notificationToast" class="toast"></div>

    <script src="../js/people.js"></script>
</body>
</html>

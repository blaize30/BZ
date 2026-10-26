<?php
// pages/orders.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History & Sales Logs</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/orders.css">
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
                <a href="orders.php" class="sidebar-link active" data-tooltip="Sales History">
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

        <!-- Orders Workspace -->
        <main class="content-wrapper">
            <section class="orders-section">
                <div class="section-header">
                    <div>
                        <span class="subtitle">Bloom & Scentz</span>
                        <h1 class="section-title">Transactions</h1>
                    </div>
                </div>

                <!-- Filters -->
                <div class="table-filters">
                    <div class="search-box">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="ordersSearch" class="form-control search-input" placeholder="Search customer or rep name...">
                    </div>
                    
                    <select id="statusFilter" class="form-control select-filter">
                        <option value="All">All Delivery Statuses</option>
                        <option value="Preparing">Preparing</option>
                        <option value="Delivered">Delivered</option>
                    </select>

                    <select id="paymentFilter" class="form-control select-filter">
                        <option value="All">All Payment Modes</option>
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Other">Other</option>
                    </select>

                    <select id="paymentStatusFilter" class="form-control select-filter">
                        <option value="All">All Payment Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Partial">Partial</option>
                    </select>

                    <select id="commissionStatusFilter" class="form-control select-filter">
                        <option value="All">All Commission Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>

                    <div class="date-field">
                        <label class="date-label">From</label>
                        <input type="date" id="ordersDateFrom" class="form-control date-input">
                    </div>

                    <div class="date-field">
                        <label class="date-label">To</label>
                        <input type="date" id="ordersDateTo" class="form-control date-input">
                    </div>

                    <button class="btn" id="resetOrderFiltersBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset
                    </button>
                </div>

                <!-- Sales Summary Stats Bar -->
                <div class="orders-stats-bar" id="ordersStatsBar">
                    <div class="stat-chip">
                        <span class="stat-label">Orders</span>
                        <span class="stat-value" id="statOrderCount">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Revenue</span>
                        <span class="stat-value" id="statRevenue">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Raw Cost</span>
                        <span class="stat-value" id="statRawCost">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Commission</span>
                        <span class="stat-value" id="statCommission">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Net Profit</span>
                        <span class="stat-value positive" id="statGrossProfit">—</span>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="table-container">
                    <table class="table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Timestamp</th>
                                <th>Representative</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th class="text-right">Total Invoice</th>
                                <th class="text-center">Payment Status</th>
                                <th class="text-center">Commission Status</th>
                                <th class="text-center">Delivery Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr>
                                <td colspan="9" class="text-center">Loading orders database...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- View Order Details Modal -->
    <div class="modal" id="orderDetailsModal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 class="modal-title" id="detailOrderTitle">Order Details</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Metadata Details Grid -->
                <div class="order-details-meta-grid">
                    <div class="detail-meta-box">
                        <span class="detail-label">Customer Profile</span>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <div class="detail-value" id="detailCustName">-</div>
                            <button id="editCustomerNameBtn" title="Edit customer name" style="background: none; border: none; cursor: pointer; color: var(--text-secondary); padding: 2px; line-height: 0; flex-shrink: 0;" onclick="window._openEditCustomerName()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                        </div>
                        <div class="detail-value-sub" id="detailCustContact">-</div>
                    </div>
                    <div class="detail-meta-box">
                        <span class="detail-label">Billing Date</span>
                        <div class="detail-value" id="detailTimestamp">-</div>
                    </div>
                    <div class="detail-meta-box">
                        <span class="detail-label">Sales Attributor</span>
                        <div class="detail-value" id="detailRepName">-</div>
                    </div>
                    <div class="detail-meta-box">
                        <span class="detail-label">Payment Method</span>
                        <div class="detail-value">
                            <span id="detailPaymentMode">-</span>
                            <div id="detailPaymentTimestamp" style="font-size: 11px; margin-top: 4px; color: var(--text-secondary); font-weight: normal; line-height: 1.2;"></div>
                        </div>
                    </div>
                    <div class="detail-meta-box">
                        <span class="detail-label">Commission Status</span>
                        <div class="detail-value">
                            <span id="detailCommissionStatus">-</span>
                            <div id="detailCommissionTimestamp" style="font-size: 11px; margin-top: 4px; color: var(--text-secondary); font-weight: normal; line-height: 1.2;"></div>
                        </div>
                    </div>
                </div>

                <!-- Order items table -->
                <div class="invoice-table-wrapper">
                    <table class="table invoice-table">
                        <thead>
                            <tr>
                                <th>Product Scent & Category</th>
                                <th>Volume</th>
                                <th class="text-right">Price Applied</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Total</th>
                                <th class="text-right" style="border-left: 1px dashed var(--border-color)">Commission (Unit)</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <!-- Items populated dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Summary section -->
                <div class="invoice-summary-grid">
                    <div class="commission-highlight-box">
                        <span class="detail-label">Total Commission Earned</span>
                        <div class="commission-value" id="detailTotalCommission">₱0.00</div>
                    </div>
                    <div class="invoice-totals-box">
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span id="detailSubtotal">₱0.00</span>
                        </div>
                        <div class="summary-line">
                            <span>Calculated Discount</span>
                            <span id="detailDiscount">₱0.00</span>
                        </div>
                        <div class="summary-line grand-total-line">
                            <span>Grand Total</span>
                            <span id="detailGrandTotal">₱0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Payments & Balance Breakdown -->
                <div class="payments-breakdown-section" style="margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h4 style="font-family:var(--font-serif); font-size:16px; font-weight:600; margin:0;">Payment History</h4>
                        <button class="btn btn-primary" id="detailAddPaymentBtn" style="font-size:11px; padding:6px 12px;">+ Record Payment</button>
                    </div>
                    <div style="display:flex; gap:16px; margin-bottom:16px;">
                        <div class="detail-meta-box" style="flex:1; padding:10px;">
                            <span class="detail-label">Total Paid So Far</span>
                            <div class="detail-value" id="detailTotalPaidLabel" style="font-size:18px; color:var(--text-primary); font-weight:700;">₱0.00</div>
                        </div>
                        <div class="detail-meta-box" style="flex:1; padding:10px;">
                            <span class="detail-label">Outstanding Balance</span>
                            <div class="detail-value" id="detailBalanceLabel" style="font-size:18px; color:#ff3b30; font-weight:700;">₱0.00</div>
                        </div>
                    </div>
                    <table class="table" style="font-size:12px; margin-bottom:0;">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Payment Mode</th>
                                <th class="text-right">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody id="detailPaymentsTableBody">
                            <tr>
                                <td colspan="3" class="text-center" style="color:var(--text-muted);">No payments recorded yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="detailPrintWaybillBtn" style="border-color: var(--accent-color); color: var(--accent-color);">Print Waybill</button>
                <button class="btn btn-primary close-modal-btn">Close Details</button>
            </div>
        </div>
    </div>



    <!-- Add Payment Modal -->
    <div class="modal" id="addPaymentModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Log Payment Transaction</h3>
                <button class="modal-close close-modal-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addPaymentForm">
                    <input type="hidden" id="payOrderId">
                    
                    <div class="form-group" style="margin-bottom: 12px;">
                        <label class="form-label" style="font-weight:600; font-size: 13px;">Remaining Balance: <span id="payRemainingLabel" style="color:var(--accent-color); font-weight:700;">₱0.00</span></label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payAmountInput">Payment Amount (₱)</label>
                        <input type="number" id="payAmountInput" class="form-control" required placeholder="0.00" min="0.01" step="0.01">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="payModeSelect">Payment Mode</label>
                        <select id="payModeSelect" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn close-modal-btn" id="payCancelBtn">Cancel</button>
                <button class="btn btn-primary" id="paySubmitBtn">Submit Payment</button>
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

    <div id="notificationToast" class="toast"></div>

    <!-- Edit Customer Modal (Order specific) -->
    <div class="modal" id="editCustomerModal">
        <div class="modal-content" style="max-width: 420px;">
            <div class="modal-header">
                <h3 class="modal-title" id="editCustomerModalTitle">Reassign Order Customer</h3>
                <button class="modal-close" id="editCustomerCloseBtn">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 14px;">
                    This will update the customer assigned to <strong>this specific order only</strong>.
                </p>
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" for="editCustomerSelect">Select Existing Customer</label>
                    <select id="editCustomerSelect" class="form-control">
                        <option value="">Loading customers...</option>
                    </select>
                </div>
                <div style="text-align: center; font-size: 11px; color: var(--text-secondary); margin: 8px 0; position: relative;">
                    <span style="background: var(--bg-secondary); padding: 0 8px; position: relative; z-index: 1;">OR</span>
                    <div style="border-top: 1px dashed var(--border-color); position: absolute; top: 50%; width: 100%; left: 0; z-index: 0;"></div>
                </div>
                <div class="form-group" style="margin-top: 14px;">
                    <label class="form-label" for="editCustomerNewInput">Enter New Customer Name</label>
                    <input type="text" id="editCustomerNewInput" class="form-control" placeholder="Type a new customer name" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" id="editCustomerCancelBtn">Cancel</button>
                <button class="btn btn-primary" id="editCustomerSaveBtn">Update Customer</button>
            </div>
        </div>
    </div>


    <script src="../js/orders.js"></script>
</body>
</html>

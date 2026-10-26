<?php
// pages/ledger.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Ledger Log</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/ledger.css">
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
                <a href="ledger.php" class="sidebar-link active" data-tooltip="Stock Ledger">
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

        <!-- Ledger Workspace -->
        <main class="content-wrapper">
            <section class="ledger-section">

                <!-- Page Header -->
                <div class="section-header">
                    <div>
                        <span class="subtitle">Inventory Audit Trail</span>
                        <h1 class="section-title">Stock Ledger</h1>
                    </div>
                    <div class="header-meta" id="ledgerMeta">
                        <!-- Entry count + totals populated by JS -->
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="ledger-filter-bar">
                    <!-- Search -->
                    <div class="search-box">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="ledgerSearch" class="form-control search-input" placeholder="Search scent or notes...">
                    </div>

                    <!-- Movement Type -->
                    <select id="movementFilter" class="form-control select-filter">
                        <option value="All">All Movement Types</option>
                        <option value="Restock">Restock</option>
                        <option value="Sale">Sale</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Return">Return</option>
                    </select>

                    <!-- Category -->
                    <select id="categoryFilter" class="form-control select-filter">
                        <option value="All">All Categories</option>
                    </select>

                    <!-- Direction (positive / negative) -->
                    <select id="directionFilter" class="form-control select-filter">
                        <option value="All">All Directions</option>
                        <option value="in">Stock In (+)</option>
                        <option value="out">Stock Out (−)</option>
                    </select>

                    <!-- Size -->
                    <select id="sizeFilter" class="form-control select-filter">
                        <option value="All">All Sizes</option>
                    </select>

                    <!-- Date From -->
                    <div class="date-field">
                        <label class="date-label">From</label>
                        <input type="date" id="dateFrom" class="form-control date-input">
                    </div>

                    <!-- Date To -->
                    <div class="date-field">
                        <label class="date-label">To</label>
                        <input type="date" id="dateTo" class="form-control date-input">
                    </div>

                    <!-- Reset Button -->
                    <button class="btn" id="resetFiltersBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="14" height="14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset
                    </button>
                </div>

                <!-- Summary Stats Bar -->
                <div class="ledger-stats-bar" id="ledgerStatsBar">
                    <div class="stat-chip">
                        <span class="stat-label">Entries</span>
                        <span class="stat-value" id="statEntries">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Stock In</span>
                        <span class="stat-value positive" id="statIn">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Total Stock Out</span>
                        <span class="stat-value negative" id="statOut">—</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-label">Net Change</span>
                        <span class="stat-value" id="statNet">—</span>
                    </div>
                </div>

                <!-- Ledger Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Scent / Product</th>
                                <th>Category</th>
                                <th>Size</th>
                                <th class="text-center">Movement Type</th>
                                <th class="text-center">Qty Change</th>
                                <th>Order Ref.</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerTableBody">
                            <tr>
                                <td colspan="8" class="text-center">Loading ledger entries...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </section>
        </main>
    </div>

    <div id="notificationToast" class="toast"></div>
    <script src="../js/ledger.js"></script>
</body>
</html>

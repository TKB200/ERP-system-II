<?php
session_start();
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System - Manage Your Business</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons (Simple & Modern) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="sidebar">
            <div class="navbar-brand" style="color: white; margin-bottom: 30px; display: block;">ERP SYSTEM</div>
            <nav>
                <a href="index.php" class="sidebar-link active"><i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span></a>
                <a href="inventory.php" class="sidebar-link"><i data-lucide="package"></i> <span>Inventory</span></a>
                <a href="sales.php" class="sidebar-link"><i data-lucide="shopping-cart"></i> <span>Sales</span></a>
                <a href="purchases.php" class="sidebar-link"><i data-lucide="shopping-bag"></i> <span>Purchases</span></a>
                <a href="customers.php" class="sidebar-link"><i data-lucide="users"></i> <span>Customers</span></a>
                <a href="suppliers.php" class="sidebar-link"><i data-lucide="truck"></i> <span>Suppliers</span></a>
                <a href="hr.php" class="sidebar-link"><i data-lucide="user-plus"></i> <span>HR / Employees</span></a>
                <a href="finance.php" class="sidebar-link"><i data-lucide="dollar-sign"></i> <span>Finance</span></a>
                <a href="reports.php" class="sidebar-link"><i data-lucide="bar-chart-3"></i> <span>Reports</span></a>
                <a href="logout.php" class="sidebar-link" style="margin-top: 50px; color: var(--danger-color);"><i
                        data-lucide="log-out"></i> <span>Logout</span></a>
            </nav>
        </div>
        <div class="main-content">
            <header class="navbar">
                <div class="header-title">
                    <h2>Dashboard</h2>
                </div>
                <div class="user-profile">
                    <span>Welcome, <strong>
                            <?php echo $_SESSION['user_name']; ?>
                        </strong></span>
                </div>
            </header>
        <?php endif; ?>
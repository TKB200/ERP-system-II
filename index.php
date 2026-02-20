<?php
include 'includes/header.php';
include 'config/db.php';

// Fetch some real stats
$total_sales = $pdo->query("SELECT SUM(total_amount) FROM sales")->fetchColumn() ?: 0;
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn() ?: 0;
$total_expenses = $pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?: 0;
?>

<div class="dashboard-header" style="margin-bottom: 30px;">
    <h1>Business Overview</h1>
    <p style="color: var(--text-muted);">Quick summary of your company's performance.</p>
</div>

<div class="dashboard-grid">
    <div class="stat-card" style="border-left-color: var(--primary-color);">
        <h3>Total Sales</h3>
        <p>$
            <?php echo number_format($total_sales, 2); ?>
        </p>
    </div>
    <div class="stat-card" style="border-left-color: var(--success-color);">
        <h3>Products in Stock</h3>
        <p>
            <?php echo $total_products; ?>
        </p>
    </div>
    <div class="stat-card" style="border-left-color: var(--accent-color);">
        <h3>Active Customers</h3>
        <p>
            <?php echo $total_customers; ?>
        </p>
    </div>
    <div class="stat-card" style="border-left-color: var(--danger-color);">
        <h3>Monthly Expenses</h3>
        <p>$
            <?php echo number_format($total_expenses, 2); ?>
        </p>
    </div>
</div>

<div style="margin-top: 40px; display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Recent Sales</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Customer</th>
                    <th style="padding: 12px;">Amount</th>
                    <th style="padding: 12px;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_sales = $pdo->query("SELECT s.*, c.name as customer_name FROM sales s JOIN customers c ON s.customer_id = c.id ORDER BY s.sale_date DESC LIMIT 5")->fetchAll();
                if ($recent_sales) {
                    foreach ($recent_sales as $sale) {
                        echo "<tr style='border-bottom: 1px solid var(--light-bg);'>";
                        echo "<td style='padding: 12px;'>#{$sale['id']}</td>";
                        echo "<td style='padding: 12px;'>{$sale['customer_name']}</td>";
                        echo "<td style='padding: 12px;'>$" . number_format($sale['total_amount'], 2) . "</td>";
                        echo "<td style='padding: 12px;'>" . date('M d, Y', strtotime($sale['sale_date'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: var(--text-muted);'>No sales recorded yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Low Stock Alerts</h3>
        <div style="margin-top: 20px;">
            <?php
            $low_stock = $pdo->query("SELECT * FROM products WHERE stock_quantity < 10 LIMIT 5")->fetchAll();
            if ($low_stock) {
                foreach ($low_stock as $product) {
                    echo "<div style='display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--light-bg);'>";
                    echo "<span>{$product['name']}</span>";
                    echo "<span style='color: var(--danger-color); font-weight: bold;'>{$product['stock_quantity']} left</span>";
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align: center; color: var(--success-color); padding: 20px;'>All items are well stocked!</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
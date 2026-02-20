<?php 
include 'includes/header.php'; 
include 'config/db.php';

// Simple reporting data
$sales_by_month = $pdo->query("SELECT DATE_FORMAT(sale_date, '%Y-%m') as month, SUM(total_amount) as total FROM sales GROUP BY month ORDER BY month DESC LIMIT 12")->fetchAll();
$popular_products = $pdo->query("SELECT p.name, SUM(si.quantity) as sold FROM sale_items si JOIN products p ON si.product_id = p.id GROUP BY p.id ORDER BY sold DESC LIMIT 5")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Reports & Analytics</h1>
    <button onclick="window.print()" class="btn btn-primary" style="width: auto;"><i data-lucide="printer"></i> Print Report</button>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Sales by Month -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Monthly Revenue</h3>
        <div style="margin-top: 20px;">
            <?php if ($sales_by_month): ?>
                <?php foreach ($sales_by_month as $row): ?>
                    <div style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="font-weight: 600;"><?php echo date('F Y', strtotime($row['month'].'-01')); ?></span>
                            <span style="color: var(--success-color); font-weight: bold;">$<?php echo number_format($row['total'], 2); ?></span>
                        </div>
                        <div style="height: 10px; background: #eee; border-radius: 5px; overflow: hidden;">
                            <?php 
                            $max = max(array_column($sales_by_month, 'total'));
                            $width = ($row['total'] / $max) * 100;
                            ?>
                            <div style="height: 100%; width: <?php echo $width; ?>%; background: var(--primary-color);"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-muted); padding: 40px;">Not enough data to generate chart.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Top Selling Products</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                    <th style="padding: 12px;">Product</th>
                    <th style="padding: 12px; text-align: right;">Units Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($popular_products): ?>
                    <?php foreach ($popular_products as $prod): ?>
                    <tr style="border-bottom: 1px solid var(--light-bg);">
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($prod['name']); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo $prod['sold']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="padding: 20px; text-align: center; color: var(--text-muted);">No sales recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 30px; background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h3>Stock Value Summary</h3>
    <?php 
    $stock_value = $pdo->query("SELECT SUM(price * stock_quantity) FROM products")->fetchColumn();
    $total_items = $pdo->query("SELECT SUM(stock_quantity) FROM products")->fetchColumn();
    ?>
    <div style="display: flex; gap: 40px; margin-top: 20px;">
        <div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Total Items in Stock</p>
            <p style="font-size: 1.5rem; font-weight: 700;"><?php echo number_format($total_items); ?></p>
        </div>
        <div>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Estimated Inventory Value</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">$<?php echo number_format($stock_value, 2); ?></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

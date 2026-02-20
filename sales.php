<?php
include 'includes/header.php';
include 'config/db.php';

// Handle New Sale
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_sale'])) {
    $customer_id = $_POST['customer_id'];
    $product_ids = $_POST['product_ids']; // Array of product IDs
    $quantities = $_POST['quantities']; // Array of quantities

    try {
        $pdo->beginTransaction();

        $total_amount = 0;

        // 1. Create Sales Record
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, total_amount) VALUES (?, 0)");
        $stmt->execute([$customer_id]);
        $sale_id = $pdo->lastInsertId();

        // 2. Add Sale Items and Calculate Total
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = $product_ids[$i];
            $qty = (int) $quantities[$i];

            if ($qty <= 0)
                continue;

            // Get product price and check stock
            $stmt = $pdo->prepare("SELECT price, stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$pid]);
            $product = $stmt->fetch();

            if (!$product || $product['stock_quantity'] < $qty) {
                throw new Exception("Product ID $pid is out of stock or does not exist.");
            }

            $price = $product['price'];
            $item_total = $price * $qty;
            $total_amount += $item_total;

            // Record item
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sale_id, $pid, $qty, $price]);

            // Update stock
            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$qty, $pid]);
        }

        // 3. Update Sales Record with Final Total
        $stmt = $pdo->prepare("UPDATE sales SET total_amount = ? WHERE id = ?");
        $stmt->execute([$total_amount, $sale_id]);

        $pdo->commit();
        header('Location: sales.php?msg=success');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$sales = $pdo->query("SELECT s.*, c.name as customer_name FROM sales s JOIN customers c ON s.customer_id = c.id ORDER BY s.sale_date DESC")->fetchAll();
$customers = $pdo->query("SELECT * FROM customers")->fetchAll();
$products = $pdo->query("SELECT * FROM products WHERE stock_quantity > 0")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Sales Management</h1>
    <div>
        <button onclick="document.getElementById('saleForm').style.display='block'; this.style.display='none';"
            class="btn btn-primary" style="width: auto;">Create New Sale</button>
    </div>
</div>

<?php if (isset($error)): ?>
    <div
        style="background: var(--danger-color); color: white; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
        Error:
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg'])): ?>
    <div
        style="background: var(--success-color); color: white; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
        Sale recorded successfully!
    </div>
<?php endif; ?>

<!-- New Sale Form (Hidden by default) -->
<div id="saleForm"
    style="display: none; background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px;">
    <h3>Create New Sale</h3>
    <form action="sales.php" method="POST" style="margin-top: 20px;">
        <div class="form-group">
            <label>Select Customer</label>
            <select name="customer_id" class="form-control" required style="appearance: auto;">
                <?php foreach ($customers as $c): ?>
                    <option value="<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="item-list">
            <div class="item-row"
                style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 10px;">
                <div>
                    <label>Product</label>
                    <select name="product_ids[]" class="form-control" required style="appearance: auto;">
                        <?php foreach ($products as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['name']); ?> ($
                                <?php echo $p['price']; ?>) -
                                <?php echo $p['stock_quantity']; ?> left
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Quantity</label>
                    <input type="number" name="quantities[]" class="form-control" min="1" required>
                </div>
            </div>
        </div>

        <p style="margin: 20px 0; font-size: 0.9rem; color: var(--text-muted);">For multiple items, you would typically
            add rows here in a full-scale app.</p>

        <div style="display: flex; gap: 10px;">
            <button type="submit" name="create_sale" class="btn btn-primary" style="flex: 2;">Confirm Sale</button>
            <button type="button" onclick="location.reload()" class="btn"
                style="flex: 1; border: 1px solid #ddd;">Cancel</button>
        </div>
    </form>
</div>

<div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h3>Sales History</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">Sale ID</th>
                <th style="padding: 12px;">Customer</th>
                <th style="padding: 12px;">Total Amount</th>
                <th style="padding: 12px;">Date</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $s): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px;">#
                        <?php echo $s['id']; ?>
                    </td>
                    <td style="padding: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($s['customer_name']); ?>
                    </td>
                    <td style="padding: 12px; color: var(--success-color); font-weight: bold;">$
                        <?php echo number_format($s['total_amount'], 2); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo date('M d, Y H:i', strtotime($s['sale_date'])); ?>
                    </td>
                    <td style="padding: 12px;">
                        <a href="#" style="color: var(--primary-color); text-decoration: none;">View Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
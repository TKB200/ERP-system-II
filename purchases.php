<?php
include 'includes/header.php';
include 'config/db.php';

// Handle New Purchase
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_purchase'])) {
    $supplier_id = $_POST['supplier_id'];
    $product_ids = $_POST['product_ids'];
    $quantities = $_POST['quantities'];
    $prices = $_POST['purchase_prices']; // Cost price may differ from selling price

    try {
        $pdo->beginTransaction();

        $total_amount = 0;

        // 1. Create Purchase Record
        $stmt = $pdo->prepare("INSERT INTO purchases (supplier_id, total_amount) VALUES (?, 0)");
        $stmt->execute([$supplier_id]);
        $purchase_id = $pdo->lastInsertId();

        // 2. Add Purchase Items and Calculate Total
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = $product_ids[$i];
            $qty = (int) $quantities[$i];
            $cost = (float) $prices[$i];

            if ($qty <= 0)
                continue;

            $item_total = $cost * $qty;
            $total_amount += $item_total;

            // Record item
            $stmt = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$purchase_id, $pid, $qty, $cost]);

            // Update stock (Increase)
            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
            $stmt->execute([$qty, $pid]);
        }

        // 3. Update Purchase Record with Final Total
        $stmt = $pdo->prepare("UPDATE purchases SET total_amount = ? WHERE id = ?");
        $stmt->execute([$total_amount, $purchase_id]);

        $pdo->commit();
        header('Location: purchases.php?msg=success');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$purchases = $pdo->query("SELECT p.*, s.name as supplier_name FROM purchases p JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.purchase_date DESC")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll();
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Purchase Management</h1>
    <div>
        <button onclick="document.getElementById('purchaseForm').style.display='block'; this.style.display='none';"
            class="btn btn-primary" style="width: auto;">Record New Purchase</button>
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
        Purchase recorded and stock updated successfully!
    </div>
<?php endif; ?>

<!-- New Purchase Form -->
<div id="purchaseForm"
    style="display: none; background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px;">
    <h3>Record New Purchase from Supplier</h3>
    <form action="purchases.php" method="POST" style="margin-top: 20px;">
        <div class="form-group">
            <label>Select Supplier</label>
            <select name="supplier_id" class="form-control" required style="appearance: auto;">
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?php echo $s['id']; ?>">
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="item-list">
            <div class="item-row"
                style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div>
                    <label>Product</label>
                    <select name="product_ids[]" class="form-control" required style="appearance: auto;">
                        <?php foreach ($products as $p): ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo htmlspecialchars($p['name']); ?> (Current Stock:
                                <?php echo $p['stock_quantity']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Quantity</label>
                    <input type="number" name="quantities[]" class="form-control" min="1" required>
                </div>
                <div>
                    <label>Cost per Unit ($)</label>
                    <input type="number" step="0.01" name="purchase_prices[]" class="form-control" required>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" name="create_purchase" class="btn btn-primary" style="flex: 2;">Record
                Purchase</button>
            <button type="button" onclick="location.reload()" class="btn"
                style="flex: 1; border: 1px solid #ddd;">Cancel</button>
        </div>
    </form>
</div>

<div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h3>Purchase Records</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">Purchase ID</th>
                <th style="padding: 12px;">Supplier</th>
                <th style="padding: 12px;">Total Bill</th>
                <th style="padding: 12px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $p): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px;">#
                        <?php echo $p['id']; ?>
                    </td>
                    <td style="padding: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($p['supplier_name']); ?>
                    </td>
                    <td style="padding: 12px; color: var(--primary-color); font-weight: bold;">$
                        <?php echo number_format($p['total_amount'], 2); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo date('M d, Y H:i', strtotime($p['purchase_date'])); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
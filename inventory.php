<?php
include 'includes/header.php';
include 'config/db.php';

// Handle Product Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: inventory.php?msg=deleted');
    exit();
}

// Handle Product Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $id = $_POST['product_id'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock_quantity = ? WHERE id = ?");
        $stmt->execute([$name, $category, $price, $stock, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock_quantity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $stock]);
    }
    header('Location: inventory.php?msg=saved');
    exit();
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_product = $stmt->fetch();
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Inventory Management</h1>
    <div>
        <button onclick="document.getElementById('productForm').scrollIntoView()" class="btn btn-primary"
            style="width: auto;">Add New Product</button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div
        style="background: var(--success-color); color: white; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
        Successfully updated the inventory.
    </div>
<?php endif; ?>

<div
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px;">
    <h3>All Products</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">ID</th>
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Category</th>
                <th style="padding: 12px;">Price</th>
                <th style="padding: 12px;">Stock</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px;">#
                        <?php echo $p['id']; ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo $p['name']; ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo $p['category']; ?>
                    </td>
                    <td style="padding: 12px;">$
                        <?php echo number_format($p['price'], 2); ?>
                    </td>
                    <td style="padding: 12px;">
                        <span
                            style="padding: 4px 8px; border-radius: 4px; <?php echo $p['stock_quantity'] < 10 ? 'background: #fee2e2; color: #dc2626;' : 'background: #dcfce7; color: #16a34a;'; ?>">
                            <?php echo $p['stock_quantity']; ?>
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        <a href="?edit=<?php echo $p['id']; ?>"
                            style="color: var(--primary-color); margin-right: 15px; text-decoration: none;">Edit</a>
                        <a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?')"
                            style="color: var(--danger-color); text-decoration: none;">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="productForm"
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); max-width: 600px;">
    <h3>
        <?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?>
    </h3>
    <form action="inventory.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="product_id" value="<?php echo $edit_product ? $edit_product['id'] : ''; ?>">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control"
                value="<?php echo $edit_product ? $edit_product['name'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <input type="text" name="category" class="form-control"
                value="<?php echo $edit_product ? $edit_product['category'] : ''; ?>"
                placeholder="Electronics, Furniture, etc.">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" step="0.01" name="price" class="form-control"
                    value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" class="form-control"
                    value="<?php echo $edit_product ? $edit_product['stock_quantity'] : ''; ?>" required>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" name="save_product" class="btn btn-primary" style="flex: 2;">Save Product</button>
            <?php if ($edit_product): ?>
                <a href="inventory.php" class="btn"
                    style="flex: 1; border: 1px solid #ddd; text-decoration: none; color: black; display: flex; align-items: center; justify-content: center;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
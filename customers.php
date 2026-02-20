<?php
include 'includes/header.php';
include 'config/db.php';

// Handle Customer Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: customers.php?msg=deleted');
    exit();
}

// Handle Customer Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_customer'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $id = $_POST['customer_id'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $address, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
        $stmt->execute([$name, $phone, $address]);
    }
    header('Location: customers.php?msg=saved');
    exit();
}

// Get customer for editing
$edit_customer = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_customer = $stmt->fetch();
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Customer Management</h1>
    <div>
        <button onclick="document.getElementById('customerForm').scrollIntoView()" class="btn btn-primary"
            style="width: auto;">Add New Customer</button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div
        style="background: var(--success-color); color: white; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
        Customer information updated successfully.
    </div>
<?php endif; ?>

<div
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px;">
    <h3>Customer List</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Phone</th>
                <th style="padding: 12px;">Address</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($c['name']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo htmlspecialchars($c['phone']); ?>
                    </td>
                    <td style="padding: 12px; max-width: 300px;">
                        <?php echo htmlspecialchars($c['address']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <a href="?edit=<?php echo $c['id']; ?>"
                            style="color: var(--primary-color); margin-right: 15px; text-decoration: none;">Edit</a>
                        <a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Are you sure?')"
                            style="color: var(--danger-color); text-decoration: none;">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="customerForm"
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); max-width: 600px;">
    <h3>
        <?php echo $edit_customer ? 'Edit Customer' : 'Add New Customer'; ?>
    </h3>
    <form action="customers.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="customer_id" value="<?php echo $edit_customer ? $edit_customer['id'] : ''; ?>">

        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" name="name" class="form-control"
                value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control"
                value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['phone']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control"
                rows="3"><?php echo $edit_customer ? htmlspecialchars($edit_customer['address']) : ''; ?></textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" name="save_customer" class="btn btn-primary" style="flex: 2;">Save Customer</button>
            <?php if ($edit_customer): ?>
                <a href="customers.php" class="btn"
                    style="flex: 1; border: 1px solid #ddd; text-decoration: none; color: black; display: flex; align-items: center; justify-content: center;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
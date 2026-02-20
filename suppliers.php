<?php
include 'includes/header.php';
include 'config/db.php';

// Handle Supplier Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: suppliers.php?msg=deleted');
    exit();
}

// Handle Supplier Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_supplier'])) {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $id = $_POST['supplier_id'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, contact = ? WHERE id = ?");
        $stmt->execute([$name, $contact, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact) VALUES (?, ?)");
        $stmt->execute([$name, $contact]);
    }
    header('Location: suppliers.php?msg=saved');
    exit();
}

// Get supplier for editing
$edit_supplier = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_supplier = $stmt->fetch();
}

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Supplier Management</h1>
    <div>
        <button onclick="document.getElementById('supplierForm').scrollIntoView()" class="btn btn-primary"
            style="width: auto;">Add New Supplier</button>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div
        style="background: var(--success-color); color: white; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
        Supplier information updated successfully.
    </div>
<?php endif; ?>

<div
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 40px;">
    <h3>Supplier List</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Contact Details</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $s): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($s['name']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo htmlspecialchars($s['contact']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <a href="?edit=<?php echo $s['id']; ?>"
                            style="color: var(--primary-color); margin-right: 15px; text-decoration: none;">Edit</a>
                        <a href="?delete=<?php echo $s['id']; ?>" onclick="return confirm('Are you sure?')"
                            style="color: var(--danger-color); text-decoration: none;">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="supplierForm"
    style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); max-width: 600px;">
    <h3>
        <?php echo $edit_supplier ? 'Edit Supplier' : 'Add New Supplier'; ?>
    </h3>
    <form action="suppliers.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="supplier_id" value="<?php echo $edit_supplier ? $edit_supplier['id'] : ''; ?>">

        <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="name" class="form-control"
                value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Information (Email/Phone)</label>
            <textarea name="contact" class="form-control" rows="3"
                placeholder="Email, Phone, etc."><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['contact']) : ''; ?></textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" name="save_supplier" class="btn btn-primary" style="flex: 2;">Save Supplier</button>
            <?php if ($edit_supplier): ?>
                <a href="suppliers.php" class="btn"
                    style="flex: 1; border: 1px solid #ddd; text-decoration: none; color: black; display: flex; align-items: center; justify-content: center;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
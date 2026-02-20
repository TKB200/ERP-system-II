<?php
include 'includes/header.php';
include 'config/db.php';

// In a real ERP, we'd have an employees table. Using the users table for simplicity here.
$employees = $pdo->query("SELECT * FROM users ORDER BY role, name")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>HR / Employee Management</h1>
    <button class="btn btn-primary" style="width: auto;">Add New Employee</button>
</div>

<div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
    <h3>Directory</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                <th style="padding: 12px;">Name</th>
                <th style="padding: 12px;">Email</th>
                <th style="padding: 12px;">Role</th>
                <th style="padding: 12px;">Since</th>
                <th style="padding: 12px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $emp): ?>
                <tr style="border-bottom: 1px solid var(--light-bg);">
                    <td style="padding: 12px; font-weight: 600;">
                        <?php echo htmlspecialchars($emp['name']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <?php echo htmlspecialchars($emp['email']); ?>
                    </td>
                    <td style="padding: 12px;">
                        <span
                            style="text-transform: capitalize; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; background: var(--light-bg); border: 1px solid #ddd;">
                            <?php echo $emp['role']; ?>
                        </span>
                    </td>
                    <td style="padding: 12px; color: var(--text-muted); font-size: 0.9rem;">
                        <?php echo date('M Y', strtotime($emp['created_at'])); ?>
                    </td>
                    <td style="padding: 12px;">
                        <span style="display: flex; align-items: center; gap: 5px; color: var(--success-color);">
                            <span
                                style="width: 8px; height: 8px; background: var(--success-color); border-radius: 50%;"></span>
                            Active
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
    <div
        style="background: #eef2ff; padding: 20px; border-radius: var(--border-radius); border-left: 4px solid var(--primary-color);">
        <h4 style="color: var(--secondary-color);">Attendance</h4>
        <p style="font-size: 1.2rem; font-weight: 700; margin-top: 10px;">98%</p>
        <small>Average this month</small>
    </div>
    <div
        style="background: #f0fdf4; padding: 20px; border-radius: var(--border-radius); border-left: 4px solid var(--success-color);">
        <h4 style="color: #166534;">Total Payroll</h4>
        <p style="font-size: 1.2rem; font-weight: 700; margin-top: 10px;">$12,450.00</p>
        <small>Next payout: March 1st</small>
    </div>
    <div
        style="background: #fff7ed; padding: 20px; border-radius: var(--border-radius); border-left: 4px solid var(--warning-color);">
        <h4 style="color: #9a3412;">Leave Requests</h4>
        <p style="font-size: 1.2rem; font-weight: 700; margin-top: 10px;">3 Pending</p>
        <small>Requires your approval</small>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
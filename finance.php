<?php 
include 'includes/header.php'; 
include 'config/db.php';

// Handle New Expense
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_expense'])) {
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    
    $stmt = $pdo->prepare("INSERT INTO expenses (title, amount, expense_date) VALUES (?, ?, ?)");
    $stmt->execute([$title, $amount, $date]);
    header('Location: finance.php?msg=success');
    exit();
}

// Financial Stats
$total_income = $pdo->query("SELECT SUM(total_amount) FROM sales")->fetchColumn() ?: 0;
$total_purchase_costs = $pdo->query("SELECT SUM(total_amount) FROM purchases")->fetchColumn() ?: 0;
$total_other_expenses = $pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?: 0;

$total_expenses = $total_purchase_costs + $total_other_expenses;
$net_profit = $total_income - $total_expenses;

$expenses_list = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC")->fetchAll();
?>

<div style="margin-bottom: 30px;">
    <h1>Financial Overview</h1>
    <p style="color: var(--text-muted);">Monitor your company's revenue, costs, and profit.</p>
</div>

<div class="dashboard-grid">
    <div class="stat-card" style="border-left-color: var(--success-color);">
        <h3>Gross Revenue</h3>
        <p>$<?php echo number_format($total_income, 2); ?></p>
        <small style="color: var(--text-muted);">From Sales</small>
    </div>
    <div class="stat-card" style="border-left-color: var(--danger-color);">
        <h3>Total Expenses</h3>
        <p>$<?php echo number_format($total_expenses, 2); ?></p>
        <small style="color: var(--text-muted);">Purchases + Other</small>
    </div>
    <div class="stat-card" style="border-left-color: <?php echo $net_profit >= 0 ? 'var(--primary-color)' : 'var(--danger-color)'; ?>;">
        <h3>Net Profit</h3>
        <p style="color: <?php echo $net_profit >= 0 ? 'var(--success-color)' : 'var(--danger-color)'; ?>;">
            $<?php echo number_format($net_profit, 2); ?>
        </p>
        <small style="color: var(--text-muted);">Revenue - All Expenses</small>
    </div>
</div>

<div style="margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Add Expense Form -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Record Business Expense</h3>
        <form action="finance.php" method="POST" style="margin-top: 20px;">
            <div class="form-group">
                <label>Expense Category / Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Rent, Electricity, Salaries" required>
            </div>
            <div class="form-group">
                <label>Amount ($)</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
        </form>
    </div>

    <!-- Expense History -->
    <div style="background: white; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow);">
        <h3>Expense History</h3>
        <div style="margin-top: 20px; max-height: 400px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid var(--light-bg);">
                        <th style="padding: 10px;">Date</th>
                        <th style="padding: 10px;">Title</th>
                        <th style="padding: 10px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($expenses_list): ?>
                        <?php foreach ($expenses_list as $exp): ?>
                        <tr style="border-bottom: 1px solid var(--light-bg);">
                            <td style="padding: 10px; font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($exp['expense_date'])); ?></td>
                            <td style="padding: 10px; font-weight: 500;"><?php echo htmlspecialchars($exp['title']); ?></td>
                            <td style="padding: 10px; text-align: right; color: var(--danger-color);">$<?php echo number_format($exp['amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="padding: 20px; text-align: center; color: var(--text-muted);">No recorded expenses yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

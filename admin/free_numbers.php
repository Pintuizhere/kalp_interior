<?php
require_once 'config/db.php';

$pageTitle = 'Manage Free Numbers';
$currentPage = 'free_numbers';
$success_msg = '';
$error_msg = '';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM free_numbers WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success_msg = "Number deleted successfully!";
    } else {
        $error_msg = "Error deleting number.";
    }
    $stmt->close();
}

// Handle Add Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_number'])) {
    $phone = trim($_POST['phone']);
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $error_msg = "Please enter a valid 10-digit phone number.";
    } else {
        $stmt = $conn->prepare("INSERT INTO free_numbers (phone) VALUES (?)");
        $stmt->bind_param("s", $phone);
        if ($stmt->execute()) {
            $success_msg = "Number added successfully!";
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "This number is already in the free numbers list.";
            } else {
                $error_msg = "Error adding number: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

// Fetch Numbers
$query = "SELECT * FROM free_numbers ORDER BY created_at DESC";
$numbers = $conn->query($query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Manage Free Numbers</h1>
                <p style="color: var(--text-muted); margin-top: 5px;">Numbers added here will bypass the WhatsApp OTP requirement on the calculator page.</p>
            </div>
        </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="row" style="display: flex; flex-wrap: wrap; gap: 24px; margin-top: 20px;">
        <div class="col-md-4" style="flex: 0 0 350px; max-width: 100%;">
            <div class="admin-card">
                <h3 style="margin-bottom: 20px;">Add New Free Number</h3>
                <form action="free_numbers.php" method="POST">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="phone" style="display: block; margin-bottom: 8px; color: #555; font-size: 14px;">Phone Number (10 digits) *</label>
                        <input type="text" id="phone" name="phone" class="form-control" style="width: 100%; padding: 12px 15px; background: #fff; border: 1px solid #ccc; border-radius: 6px; color: #333; outline: none;" required pattern="[0-9]{10}" placeholder="e.g. 9876543210">
                    </div>
                    <button type="submit" name="add_number" class="btn-primary" style="width: 100%; text-align: center; justify-content: center;">Add Number</button>
                </form>
            </div>
        </div>

        <div class="col-md-8" style="flex: 1; min-width: 300px;">
            <div class="admin-card" style="height: 100%;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Allowed Free Numbers</h3>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>SNO</th>
                                <th>Phone Number</th>
                                <th>Added On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($numbers && $numbers->num_rows > 0): ?>
                                <?php $sno = 1; while ($row = $numbers->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $sno++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['phone']); ?></strong></td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="free_numbers.php?delete=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to remove this number from the free list?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 20px; color: #777;">No free numbers found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #dcfce7; color: #166534; }
    .alert-danger { background: #fee2e2; color: #991b1b; }
    
    .btn-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #f3f4f6; color: #6b7280; text-decoration: none; transition: 0.3s; }
    .btn-icon:hover { background: #e5e7eb; color: #111827; }
    .btn-icon.delete:hover { background: #fee2e2; color: #ef4444; }
    
    @media (max-width: 768px) {
        .row { flex-direction: column; }
        .col-md-4 { flex: 0 0 100% !important; max-width: 100%; }
    }
</style>

<?php include 'includes/footer.php'; ?>

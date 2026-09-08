<?php
$pageTitle = 'Shop Orders';
$currentPage = 'shop_orders';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

// Handle Status Update
if (isset($_GET['update_status']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $conn->real_escape_string($_GET['update_status']);
    
    $stmt = $conn->prepare("UPDATE shop_orders SET status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $success_msg = "Order status updated!";
        }
        $stmt->close();
    }
}

// Fetch orders
$order_query = "SELECT * FROM shop_orders ORDER BY id DESC";
$order_result = $conn->query($order_query);
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1>Shop Orders</h1>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($order_result && $order_result->num_rows > 0): ?>
                        <?php while($order = $order_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                <small style="color:var(--text-muted);"><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                            </td>
                            <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                            <td><strong>₹<?php echo number_format($order['total_amount']); ?></strong></td>
                            <td>
                                <select onchange="window.location.href='manage_shop_orders.php?id=<?php echo $order['id']; ?>&update_status='+this.value" style="padding:5px; border-radius:4px; border:1px solid #ccc; font-size:12px;">
                                    <option value="Pending" <?php echo $order['status']=='Pending'?'selected':''; ?>>Pending</option>
                                    <option value="Confirmed" <?php echo $order['status']=='Confirmed'?'selected':''; ?>>Confirmed</option>
                                    <option value="Shipped" <?php echo $order['status']=='Shipped'?'selected':''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $order['status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo $order['status']=='Cancelled'?'selected':''; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="../invoice.php?order_id=<?php echo $order['order_number']; ?>" target="_blank" class="btn-icon view" title="View Invoice"><i class="fa-solid fa-file-invoice"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 20px;">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

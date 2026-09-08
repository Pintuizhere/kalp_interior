<?php
$pageTitle = 'Shop Products';
$currentPage = 'shop_products';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM shop_products WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success_msg = "Product deleted successfully!";
        } else {
            $error_msg = "Error deleting product.";
        }
        $stmt->close();
    }
}

// Fetch products
$prod_query = "SELECT p.*, c.name as category_name FROM shop_products p LEFT JOIN shop_categories c ON p.category_id = c.id ORDER BY p.id DESC";
$prod_result = $conn->query($prod_query);
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Shop Products</h1>
            <button class="btn-primary" onclick="alert('Add product modal would open here. For now, data is managed via DB directly.')">Add New Product</button>
        </div>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($prod_result && $prod_result->num_rows > 0): ?>
                        <?php while($prod = $prod_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong style="color:var(--text-dark);"><?php echo htmlspecialchars($prod['name']); ?></strong>
                                <br><small style="color:var(--text-muted);">SKU: <?php echo htmlspecialchars($prod['sku']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td>
                                <?php if($prod['sale_price']): ?>
                                    <span style="text-decoration:line-through; color:var(--text-muted); font-size:0.9em;">₹<?php echo number_format($prod['regular_price']); ?></span>
                                    <strong style="color:var(--accent-color);">₹<?php echo number_format($prod['sale_price']); ?></strong>
                                <?php else: ?>
                                    <strong>₹<?php echo number_format($prod['regular_price']); ?></strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="background: <?php echo $prod['stock_status'] == 'instock' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $prod['stock_status'] == 'instock' ? '#166534' : '#991b1b'; ?>; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight:600;">
                                    <?php echo htmlspecialchars(strtoupper($prod['stock_status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="#" class="btn-icon edit" onclick="alert('Edit product feature');"><i class="fa-solid fa-pen"></i></a>
                                    <a href="manage_shop_products.php?delete_id=<?php echo $prod['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px;">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

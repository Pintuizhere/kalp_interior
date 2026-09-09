<?php
$pageTitle = 'Shop Products';
$currentPage = 'shop_products';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;

if ($edit_id > 0) {
    $action = 'edit';
}

// Handle Delete Image
if (isset($_GET['delete_image_id']) && isset($_GET['product_id'])) {
    $img_id = (int)$_GET['delete_image_id'];
    $prod_id = (int)$_GET['product_id'];
    $conn->query("DELETE FROM shop_product_images WHERE id = $img_id AND product_id = $prod_id");
    
    // Check if we deleted the primary image, if so set a new one
    $check_prim = $conn->query("SELECT id FROM shop_product_images WHERE product_id = $prod_id AND is_primary = 1");
    if ($check_prim->num_rows == 0) {
        $conn->query("UPDATE shop_product_images SET is_primary = 1 WHERE product_id = $prod_id LIMIT 1");
    }
    
    header("Location: manage_shop_products.php?edit_id=" . $prod_id);
    exit;
}

// Handle POST Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_product']) || isset($_POST['edit_product']))) {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $category_id = (int)$_POST['category_id'];
    $sku = $conn->real_escape_string($_POST['sku']);
    $regular_price = (float)$_POST['regular_price'];
    $sale_price = (float)$_POST['sale_price'];
    $short_desc = $conn->real_escape_string($_POST['short_desc']);
    $description = $conn->real_escape_string($_POST['description']);
    $stock_status = $conn->real_escape_string($_POST['stock_status']);
    
    // Specs
    $spec_material = $conn->real_escape_string($_POST['spec_material']);
    $spec_color = $conn->real_escape_string($_POST['spec_color']);
    $spec_seater = $conn->real_escape_string($_POST['spec_seater']);
    $spec_dim_inches = $conn->real_escape_string($_POST['spec_dim_inches']);
    $spec_dim_cm = $conn->real_escape_string($_POST['spec_dim_cm']);
    $spec_pack_content = $conn->real_escape_string($_POST['spec_pack_content']);
    $spec_finish = $conn->real_escape_string($_POST['spec_finish']);

    if (isset($_POST['edit_product'])) {
        $update_id = (int)$_POST['edit_id'];
        $stmt = $conn->prepare("UPDATE shop_products SET 
            name=?, slug=?, category_id=?, sku=?, regular_price=?, sale_price=?, short_desc=?, description=?, stock_status=?,
            spec_material=?, spec_color=?, spec_seater=?, spec_dim_inches=?, spec_dim_cm=?, spec_pack_content=?, spec_finish=?
            WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("ssisddssssssssssi", 
                $name, $slug, $category_id, $sku, $regular_price, $sale_price, $short_desc, $description, $stock_status,
                $spec_material, $spec_color, $spec_seater, $spec_dim_inches, $spec_dim_cm, $spec_pack_content, $spec_finish,
                $update_id
            );
            if ($stmt->execute()) {
                $success_msg = "Product updated successfully!";
                $product_id = $update_id;
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO shop_products (
            name, slug, category_id, sku, regular_price, sale_price, short_desc, description, stock_status,
            spec_material, spec_color, spec_seater, spec_dim_inches, spec_dim_cm, spec_pack_content, spec_finish
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssisddssssssssss", 
                $name, $slug, $category_id, $sku, $regular_price, $sale_price, $short_desc, $description, $stock_status,
                $spec_material, $spec_color, $spec_seater, $spec_dim_inches, $spec_dim_cm, $spec_pack_content, $spec_finish
            );
            if ($stmt->execute()) {
                $success_msg = "Product added successfully!";
                $product_id = $stmt->insert_id;
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
    }

    // Handle Multiple Image Upload (Max 10)
    if (isset($product_id) && isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $upload_dir = '../assets/images/products/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        // Check current images count
        $img_count_query = $conn->query("SELECT COUNT(*) as count FROM shop_product_images WHERE product_id = $product_id");
        $current_count = $img_count_query->fetch_assoc()['count'];
        
        $files = $_FILES['images'];
        $uploaded_count = 0;
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($current_count + $uploaded_count >= 10) {
                $error_msg = "Maximum 10 images allowed. Some images were not uploaded.";
                break;
            }
            
            if ($files['error'][$i] == 0) {
                $filename = time() . '_' . $i . '_' . basename($files['name'][$i]);
                if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $filename)) {
                    
                    // First image is primary if no primary exists
                    $is_primary = 0;
                    $check_prim = $conn->query("SELECT id FROM shop_product_images WHERE product_id = $product_id AND is_primary = 1");
                    if ($check_prim->num_rows == 0) {
                        $is_primary = 1;
                    }
                    
                    $conn->query("INSERT INTO shop_product_images (product_id, image_url, is_primary) VALUES ($product_id, '$filename', $is_primary)");
                    $uploaded_count++;
                }
            }
        }
    }
    
    // Redirect back to list after success if it's add
    if (isset($_POST['add_product']) && empty($error_msg)) {
        echo "<script>window.location.href='manage_shop_products.php';</script>";
        exit;
    }
}

// Handle Delete Product
if (isset($_GET['delete_id']) && $action != 'add' && $action != 'edit') {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM shop_products WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $conn->query("DELETE FROM shop_product_images WHERE product_id = $delete_id");
            $success_msg = "Product deleted successfully!";
        } else {
            $error_msg = "Error deleting product.";
        }
        $stmt->close();
    }
}

// Fetch edit data
$edit_data = null;
$existing_images = [];
if ($action == 'edit' && $edit_id > 0) {
    $ed_query = $conn->query("SELECT * FROM shop_products WHERE id = $edit_id");
    if ($ed_query && $ed_query->num_rows > 0) {
        $edit_data = $ed_query->fetch_assoc();
        
        $img_q = $conn->query("SELECT * FROM shop_product_images WHERE product_id = $edit_id ORDER BY is_primary DESC, id ASC");
        while($row = $img_q->fetch_assoc()) {
            $existing_images[] = $row;
        }
    }
}
?>

<style>
    .admin-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        padding: 25px;
        margin-bottom: 25px;
        border: 1px solid #f1f5f9;
    }
    .admin-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #475569;
        font-size: 0.95rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: inherit;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: var(--brand-green);
        outline: none;
    }
    .img-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .img-thumb-box {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .img-thumb-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .img-delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 12px;
    }
    .img-primary-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: var(--brand-green);
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
    }
</style>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#dcfce7; color:#166534; padding:15px; border-radius:8px; margin-bottom:20px; border: 1px solid #bbf7d0;">
                <i class="fa-solid fa-check-circle" style="margin-right: 8px;"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; margin-bottom:20px; border: 1px solid #fecaca;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($action == 'add' || $action == 'edit'): ?>
            <!-- FORM SECTION -->
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1><?php echo $action == 'add' ? 'Add New Product' : 'Edit Product'; ?></h1>
                <a href="manage_shop_products.php" class="btn-secondary" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; color: var(--text-dark);"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
            </div>
            
            <form action="manage_shop_products.php<?php echo $action == 'edit' ? '?edit_id='.$edit_id : '?action=add'; ?>" method="POST" enctype="multipart/form-data">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="edit_product" value="1">
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                <?php else: ?>
                    <input type="hidden" name="add_product" value="1">
                <?php endif; ?>

                <div class="admin-card">
                    <h3 class="admin-card-title"><i class="fa-solid fa-box"></i> Basic Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Product Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>" required class="form-control" placeholder="e.g. Modern Wooden Sofa">
                        </div>
                        <div class="form-group">
                            <label>Category <span style="color:#ef4444;">*</span></label>
                            <select name="category_id" required class="form-control">
                                <option value="">Select Category</option>
                                <?php 
                                $cats = $conn->query("SELECT id, name FROM shop_categories WHERE type='category' ORDER BY name ASC");
                                while($cat = $cats->fetch_assoc()):
                                    $selected = ($edit_data && $edit_data['category_id'] == $cat['id']) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Short Description (for product card)</label>
                            <textarea name="short_desc" rows="2" class="form-control"><?php echo $edit_data ? htmlspecialchars($edit_data['short_desc']) : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="width: 100%;">
                            <label>Full Description (for product details page)</label>
                            <textarea name="description" rows="5" class="form-control"><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="admin-card-title"><i class="fa-solid fa-tag"></i> Pricing & Inventory</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Regular Price (₹) <span style="color:#ef4444;">*</span></label>
                            <input type="number" step="0.01" name="regular_price" value="<?php echo $edit_data ? htmlspecialchars($edit_data['regular_price']) : ''; ?>" required class="form-control" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label>Sale Price (₹)</label>
                            <input type="number" step="0.01" name="sale_price" value="<?php echo $edit_data ? htmlspecialchars($edit_data['sale_price']) : ''; ?>" class="form-control" placeholder="0.00">
                            <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">Leave blank if not on sale.</p>
                        </div>
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="text" name="sku" value="<?php echo $edit_data ? htmlspecialchars($edit_data['sku']) : ''; ?>" class="form-control" placeholder="Optional identifier">
                        </div>
                        <div class="form-group">
                            <label>Stock Status</label>
                            <select name="stock_status" class="form-control">
                                <option value="instock" <?php echo ($edit_data && $edit_data['stock_status'] == 'instock') ? 'selected' : ''; ?>>In Stock</option>
                                <option value="outofstock" <?php echo ($edit_data && $edit_data['stock_status'] == 'outofstock') ? 'selected' : ''; ?>>Out of Stock</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="admin-card-title"><i class="fa-solid fa-list-check"></i> Specifications</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Material</label>
                            <input type="text" name="spec_material" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_material']) : ''; ?>" class="form-control" placeholder="e.g. Teak Wood">
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" name="spec_color" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_color']) : ''; ?>" class="form-control" placeholder="e.g. Beige">
                        </div>
                        <div class="form-group">
                            <label>Seater</label>
                            <input type="text" name="spec_seater" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_seater']) : ''; ?>" class="form-control" placeholder="e.g. 3 Seater">
                        </div>
                        <div class="form-group">
                            <label>Finish / Texture</label>
                            <input type="text" name="spec_finish" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_finish']) : ''; ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Dimensions (inches)</label>
                            <input type="text" name="spec_dim_inches" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_dim_inches']) : ''; ?>" class="form-control" placeholder="L x W x H">
                        </div>
                        <div class="form-group">
                            <label>Dimensions (cm)</label>
                            <input type="text" name="spec_dim_cm" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_dim_cm']) : ''; ?>" class="form-control" placeholder="L x W x H">
                        </div>
                        <div class="form-group">
                            <label>Pack Content</label>
                            <input type="text" name="spec_pack_content" value="<?php echo $edit_data ? htmlspecialchars($edit_data['spec_pack_content']) : ''; ?>" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="admin-card-title"><i class="fa-solid fa-images"></i> Product Gallery (Max 10 Images)</h3>
                    
                    <?php if ($action == 'edit' && count($existing_images) > 0): ?>
                        <div style="margin-bottom: 20px;">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Existing Images (<?php echo count($existing_images); ?>/10)</label>
                            <div class="img-gallery-grid">
                                <?php foreach($existing_images as $img): ?>
                                    <div class="img-thumb-box">
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($img['image_url']); ?>">
                                        <a href="manage_shop_products.php?delete_image_id=<?php echo $img['id']; ?>&product_id=<?php echo $edit_id; ?>" class="img-delete-btn" onclick="return confirm('Delete this image?');"><i class="fa-solid fa-times"></i></a>
                                        <?php if($img['is_primary']): ?>
                                            <span class="img-primary-badge">Primary</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Upload New Images</label>
                        <input type="file" name="images[]" multiple accept="image/*" class="form-control" style="background: white;" <?php echo ($action == 'edit' && count($existing_images) >= 10) ? 'disabled' : ''; ?>>
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">You can select multiple images by holding CTRL or CMD. First uploaded image will be the primary one.</p>
                    </div>
                </div>
                
                <div style="margin-top: 30px; margin-bottom: 50px;">
                    <button type="submit" class="btn-primary" style="padding: 15px 40px; font-size: 1.1rem; border-radius: 8px;">
                        <i class="fa-solid fa-save"></i> <?php echo $action == 'edit' ? 'Update Product' : 'Add Product'; ?>
                    </button>
                </div>

            </form>

        <?php else: ?>
            <!-- LIST SECTION -->
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1>Shop Products</h1>
                <a href="manage_shop_products.php?action=add" class="btn-primary" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; color: white;"><i class="fa-solid fa-plus"></i> Add New Product</a>
            </div>
            
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $prod_query = "SELECT p.*, c.name as category_name, 
                                      (SELECT image_url FROM shop_product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image_url 
                                      FROM shop_products p 
                                      LEFT JOIN shop_categories c ON p.category_id = c.id 
                                      ORDER BY p.id DESC";
                        $prod_result = $conn->query($prod_query);
                        
                        if($prod_result && $prod_result->num_rows > 0): 
                        ?>
                            <?php while($prod = $prod_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($prod['image_url'])): ?>
                                        <img src="../assets/images/products/<?php echo htmlspecialchars($prod['image_url']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=100&q=80';">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color:var(--text-dark);"><?php echo htmlspecialchars($prod['name']); ?></strong>
                                    <br><small style="color:var(--text-muted);">SKU: <?php echo htmlspecialchars($prod['sku']); ?></small>
                                </td>
                                <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; color: #475569;"><?php echo htmlspecialchars($prod['category_name'] ?? '-'); ?></span></td>
                                <td>
                                    <?php if($prod['sale_price'] > 0): ?>
                                        <span style="text-decoration:line-through; color:var(--text-muted); font-size:0.9em;">₹<?php echo number_format($prod['regular_price']); ?></span>
                                        <strong style="color:var(--brand-green);">₹<?php echo number_format($prod['sale_price']); ?></strong>
                                    <?php else: ?>
                                        <strong>₹<?php echo number_format($prod['regular_price']); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="background: <?php echo $prod['stock_status'] == 'instock' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $prod['stock_status'] == 'instock' ? '#166534' : '#991b1b'; ?>; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight:600;">
                                        <?php echo htmlspecialchars(strtoupper($prod['stock_status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="manage_shop_products.php?edit_id=<?php echo $prod['id']; ?>" class="btn-icon edit" style="background: #f1f5f9; color: #475569; padding: 8px 12px; border-radius: 6px; margin-right: 5px;"><i class="fa-solid fa-pen"></i></a>
                                        <a href="manage_shop_products.php?delete_id=<?php echo $prod['id']; ?>" class="btn-icon delete" style="padding: 8px 12px; border-radius: 6px;" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 40px; color: var(--text-gray);">No products found. Add your first product!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

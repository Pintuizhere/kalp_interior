<?php
$pageTitle = 'Shop Categories';
$currentPage = 'shop_categories';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

// Handle POST request to add or edit a category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $type = isset($_POST['type']) ? $conn->real_escape_string($_POST['type']) : 'category';
    $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;
    
    $image = '';
    $image_update_sql = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/shop_categories/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $filename = time() . '_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image = 'uploads/shop_categories/' . $filename;
            $image_update_sql = ", image='$image'";
        }
    }

    if (!empty($name)) {
        if (isset($_POST['edit_category'])) {
            $edit_id = (int)$_POST['edit_id'];
            $stmt = $conn->prepare("UPDATE shop_categories SET name=?, slug=?, type=?, position=? $image_update_sql WHERE id=?");
            if ($stmt) {
                $stmt->bind_param("sssii", $name, $slug, $type, $position, $edit_id);
                if ($stmt->execute()) {
                    $success_msg = "Category updated successfully!";
                } else {
                    $error_msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            }
        } elseif (isset($_POST['add_category'])) {
            $stmt = $conn->prepare("INSERT INTO shop_categories (name, slug, type, image, position) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssi", $name, $slug, $type, $image, $position);
                if ($stmt->execute()) {
                    $success_msg = "Category added successfully!";
                } else {
                    $error_msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_msg = "Database error: " . $conn->error;
            }
        }
    }
}

// Handle GET request to delete a category
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM shop_categories WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            // Also optional: delete the image file if we want, but let's keep it simple for now.
            $success_msg = "Category deleted successfully!";
        } else {
            $error_msg = "Error deleting category.";
        }
        $stmt->close();
    }
}

// Fetch categories
$cat_query = "SELECT * FROM shop_categories ORDER BY position ASC, type ASC, name ASC";
$cat_result = $conn->query($cat_query);

// Fetch specific category for editing
$edit_cat = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $edit_query = $conn->query("SELECT * FROM shop_categories WHERE id = $edit_id");
    if ($edit_query && $edit_query->num_rows > 0) {
        $edit_cat = $edit_query->fetch_assoc();
    }
}
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

        <div class="page-header">
            <h1>Shop Categories</h1>
        </div>

        <div class="form-grid-1x2">
            
            <!-- ADD/EDIT CATEGORY FORM -->
            <div class="table-wrapper" style="align-self: start; padding: 25px; overflow: visible;">
                <h3 style="margin-top:0; margin-bottom: 20px; font-size: 16px;"><?php echo $edit_cat ? 'Edit Category' : 'Add New Category'; ?></h3>
                <form action="manage_shop_categories.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_cat): ?>
                        <input type="hidden" name="edit_category" value="1">
                        <input type="hidden" name="edit_id" value="<?php echo $edit_cat['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_category" value="1">
                    <?php endif; ?>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['name']) : ''; ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">The name is how it appears on your site.</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Type</label>
                        <select name="type" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                            <option value="category" <?php echo ($edit_cat && $edit_cat['type'] == 'category') ? 'selected' : ''; ?>>Category (e.g., Sofas, Beds)</option>
                            <option value="room" <?php echo ($edit_cat && $edit_cat['type'] == 'room') ? 'selected' : ''; ?>>Room (e.g., Living Room, Bedroom)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Position (Order)</label>
                        <input type="number" name="position" value="<?php echo $edit_cat ? htmlspecialchars($edit_cat['position']) : '0'; ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">Lower numbers appear first.</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Image</label>
                        <?php if ($edit_cat && !empty($edit_cat['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../<?php echo htmlspecialchars($edit_cat['image']); ?>" style="width: 80px; border-radius: 5px; border: 1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px; background: white;">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 20px; width:100%; justify-content:center;">
                        <?php echo $edit_cat ? 'Update Category' : 'Add New Category'; ?>
                    </button>
                    <?php if ($edit_cat): ?>
                        <a href="manage_shop_categories.php" class="btn-secondary" style="display: block; text-align: center; margin-top: 10px; padding: 10px; color: #666; text-decoration: none; border: 1px solid #ddd; border-radius: 5px;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- MANAGE CATEGORIES TABLE -->
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Pos.</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($cat_result && $cat_result->num_rows > 0): ?>
                            <?php while($cat = $cat_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($cat['image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($cat['image']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #aaa;"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color:var(--text-dark);"><?php echo htmlspecialchars($cat['name']); ?></strong><br>
                                    <span style="color:var(--text-muted); font-size: 12px;"><?php echo htmlspecialchars($cat['slug']); ?></span>
                                </td>
                                <td><span style="background: #e2e8f0; padding: 3px 8px; border-radius: 12px; font-size: 12px; text-transform: uppercase;"><?php echo htmlspecialchars($cat['type']); ?></span></td>
                                <td><?php echo (int)$cat['position']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="manage_shop_categories.php?edit_id=<?php echo $cat['id']; ?>" class="btn-icon edit" style="background: #f1f5f9; color: #475569; padding: 6px 10px; border-radius: 5px; margin-right: 5px;"><i class="fa-solid fa-pen"></i></a>
                                        <a href="manage_shop_categories.php?delete_id=<?php echo $cat['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this category?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding: 20px;">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

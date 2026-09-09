<?php
$pageTitle = 'Manage Brand Collections';
$currentPage = 'brand_collections';
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

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['add_collection']) || isset($_POST['edit_collection']))) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $btn_text = $conn->real_escape_string($_POST['btn_text']);
    $btn_icon = $conn->real_escape_string($_POST['btn_icon']);
    $btn_link = $conn->real_escape_string($_POST['btn_link']);
    $position = (int)$_POST['position'];

    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/';
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image);
    }

    if (isset($_POST['edit_collection'])) {
        $update_id = (int)$_POST['edit_id'];
        
        if ($image) {
            $stmt = $conn->prepare("UPDATE brand_collections SET title=?, description=?, image=?, btn_text=?, btn_icon=?, btn_link=?, position=? WHERE id=?");
            $stmt->bind_param("ssssssii", $title, $description, $image, $btn_text, $btn_icon, $btn_link, $position, $update_id);
        } else {
            $stmt = $conn->prepare("UPDATE brand_collections SET title=?, description=?, btn_text=?, btn_icon=?, btn_link=?, position=? WHERE id=?");
            $stmt->bind_param("sssssii", $title, $description, $btn_text, $btn_icon, $btn_link, $position, $update_id);
        }
        
        if ($stmt && $stmt->execute()) {
            $success_msg = "Collection updated successfully!";
        } else {
            $error_msg = "Error updating collection.";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO brand_collections (title, description, image, btn_text, btn_icon, btn_link, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $title, $description, $image, $btn_text, $btn_icon, $btn_link, $position);
        
        if ($stmt && $stmt->execute()) {
            $success_msg = "Collection added successfully!";
        } else {
            $error_msg = "Error adding collection.";
        }
    }
    
    if(empty($error_msg) && isset($_POST['add_collection'])){
        echo "<script>window.location.href='manage_brand_collections.php';</script>";
        exit;
    }
}

// Handle Delete
if (isset($_GET['delete_id']) && $action != 'add' && $action != 'edit') {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM brand_collections WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success_msg = "Collection deleted successfully!";
        } else {
            $error_msg = "Error deleting collection.";
        }
        $stmt->close();
    }
}

// Fetch edit data
$edit_data = null;
if ($action == 'edit' && $edit_id > 0) {
    $ed_query = $conn->query("SELECT * FROM brand_collections WHERE id = $edit_id");
    if ($ed_query && $ed_query->num_rows > 0) {
        $edit_data = $ed_query->fetch_assoc();
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

        <?php if ($action == 'add' || $action == 'edit'): ?>
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1><?php echo $action == 'add' ? 'Add Brand Collection' : 'Edit Brand Collection'; ?></h1>
                <a href="manage_brand_collections.php" class="btn-secondary" style="padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 5px; text-decoration: none; color: var(--text-dark);">Back to List</a>
            </div>
            
            <div class="table-wrapper" style="padding: 25px;">
                <form action="manage_brand_collections.php<?php echo $action == 'edit' ? '?edit_id='.$edit_id : '?action=add'; ?>" method="POST" enctype="multipart/form-data">
                    <?php if ($action == 'edit'): ?>
                        <input type="hidden" name="edit_collection" value="1">
                        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_collection" value="1">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Title <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="title" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Position</label>
                            <input type="number" name="position" value="<?php echo $edit_data ? htmlspecialchars($edit_data['position']) : '0'; ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        </div>

                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Button Text</label>
                            <input type="text" name="btn_text" value="<?php echo $edit_data ? htmlspecialchars($edit_data['btn_text']) : ''; ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="e.g. Shop Now">
                        </div>

                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Button Link URL</label>
                            <input type="text" name="btn_link" value="<?php echo $edit_data ? htmlspecialchars($edit_data['btn_link']) : ''; ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="e.g. shop-furniture.php">
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Button Icon (FontAwesome class)</label>
                            <input type="text" name="btn_icon" value="<?php echo $edit_data ? htmlspecialchars($edit_data['btn_icon']) : ''; ?>" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" placeholder="e.g. fa-solid fa-cart-shopping">
                        </div>
                        
                        <div class="form-group">
                            <label style="display:block; margin-bottom:8px; font-weight:500;">Image</label>
                            <?php if ($edit_data && !empty($edit_data['image'])): ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($edit_data['image']); ?>" style="width: 100px; border-radius: 5px; margin-bottom: 10px; display: block;">
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px; background: white;">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Description</label>
                        <textarea name="description" rows="4" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px; font-family:inherit;"><?php echo $edit_data ? htmlspecialchars($edit_data['description']) : ''; ?></textarea>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-primary" style="padding: 12px 30px; font-size: 16px;">
                            <?php echo $action == 'edit' ? 'Update Collection' : 'Add Collection'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
        <?php else: ?>
            <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h1>Brand Collections</h1>
                <a href="manage_brand_collections.php?action=add" class="btn-primary" style="padding: 10px 20px; border-radius: 5px; text-decoration: none; color: white;">Add New Collection</a>
            </div>
            
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Button Details</th>
                            <th>Position</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query = "SELECT * FROM brand_collections ORDER BY position ASC";
                        $result = $conn->query($query);
                        
                        if($result && $result->num_rows > 0): 
                            while($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td>
                                    <?php if(!empty($row['image'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($row['image']); ?>" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 60px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #aaa;"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                <td>
                                    <small style="color:var(--text-muted);">Text:</small> <?php echo htmlspecialchars($row['btn_text']); ?><br>
                                    <small style="color:var(--text-muted);">Link:</small> <?php echo htmlspecialchars($row['btn_link']); ?>
                                </td>
                                <td><?php echo $row['position']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="manage_brand_collections.php?edit_id=<?php echo $row['id']; ?>" class="btn-icon edit" style="background: #f1f5f9; color: #475569; padding: 6px 10px; border-radius: 5px; margin-right: 5px;"><i class="fa-solid fa-pen"></i></a>
                                        <a href="manage_brand_collections.php?delete_id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this collection?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 20px;">No collections found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

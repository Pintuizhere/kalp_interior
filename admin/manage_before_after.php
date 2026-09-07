<?php
session_start();
require 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$success_msg = '';
$error_msg = '';

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_pair'])) {
    $display_order = (int)$_POST['display_order'];
    
    if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] == 0 &&
        isset($_FILES['after_image']) && $_FILES['after_image']['error'] == 0) {
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $upload_dir = '../uploads/media/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Before Image
        $b_info = pathinfo($_FILES['before_image']['name']);
        $b_ext = strtolower($b_info['extension']);
        $b_name = 'before_' . time() . '_' . rand(100, 999) . '.' . $b_ext;
        $b_target = $upload_dir . $b_name;
        
        // After Image
        $a_info = pathinfo($_FILES['after_image']['name']);
        $a_ext = strtolower($a_info['extension']);
        $a_name = 'after_' . time() . '_' . rand(100, 999) . '.' . $a_ext;
        $a_target = $upload_dir . $a_name;
        
        if (in_array($b_ext, $allowed_ext) && in_array($a_ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES['before_image']['tmp_name'], $b_target) &&
                move_uploaded_file($_FILES['after_image']['tmp_name'], $a_target)) {
                
                $db_before = 'uploads/media/' . $b_name;
                $db_after = 'uploads/media/' . $a_name;
                
                $stmt = $conn->prepare("INSERT INTO before_after (before_image, after_image, display_order) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $db_before, $db_after, $display_order);
                if ($stmt->execute()) {
                    $_SESSION['success_msg'] = "Before & After pair added successfully!";
                } else {
                    $_SESSION['error_msg'] = "Database Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['error_msg'] = "Failed to upload images.";
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type. Allowed: jpg, jpeg, png, webp";
        }
    } else {
        $_SESSION['error_msg'] = "Please select both a Before and After image.";
    }
    header("Location: manage_before_after.php");
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Get file paths to delete files from server
    $res = $conn->query("SELECT before_image, after_image FROM before_after WHERE id = $delete_id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $b_file = '../' . $row['before_image'];
        $a_file = '../' . $row['after_image'];
        
        // Delete from DB
        if ($conn->query("DELETE FROM before_after WHERE id = $delete_id")) {
            // we delete file after db success
            if (file_exists($b_file) && strpos($b_file, 'assets/') === false) @unlink($b_file);
            if (file_exists($a_file) && strpos($a_file, 'assets/') === false) @unlink($a_file);
            
            $_SESSION['success_msg'] = "Pair deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete from database.";
        }
    } else {
        $_SESSION['error_msg'] = "Pair not found.";
    }
    header("Location: manage_before_after.php");
    exit();
}

$pageTitle = "Before & After";
$currentPage = "manage_before_after";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <i class="fa-solid fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1>Manage Before & After Images</h1>
        </div>
        <p class="text-muted" style="margin-bottom: 30px;">Add and manage the image pairs displayed in the Before & After section.</p>
        
        <div class="form-grid-1x2">
            
            <!-- Upload Form -->
            <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); min-width: 0;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Add New Pair</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Before Image</label>
                        <input type="file" name="before_image" accept="image/*" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">After Image</label>
                        <input type="file" name="after_image" accept="image/*" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Display Order</label>
                        <input type="number" name="display_order" value="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <small class="text-muted">Lower numbers appear first.</small>
                    </div>
                    
                    <button type="submit" name="upload_pair" class="btn-primary" style="width: 100%; padding: 12px;">Add Pair</button>
                </form>
            </div>
            
            <!-- Pairs Table -->
            <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); min-width: 0;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Existing Pairs</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; text-align: left;">
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Order</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Before</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">After</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pairs = $conn->query("SELECT * FROM before_after ORDER BY display_order ASC, id DESC");
                            if ($pairs->num_rows > 0):
                                while($pair = $pairs->fetch_assoc()):
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><?php echo $pair['display_order']; ?></td>
                                <td style="padding: 12px;">
                                    <img src="../<?php echo $pair['before_image']; ?>" alt="Before" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td style="padding: 12px;">
                                    <img src="../<?php echo $pair['after_image']; ?>" alt="After" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td style="padding: 12px;">
                                    <a href="?delete_id=<?php echo $pair['id']; ?>" class="btn-secondary" style="background: #dc3545; color: white; padding: 6px 12px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap; border-radius: 5px; text-decoration: none;" onclick="return confirm('Delete this pair?');"><i class="fa-solid fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="4" style="padding: 20px; text-align: center; color: #777;">No pairs found. Add your first Before & After pair!</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>

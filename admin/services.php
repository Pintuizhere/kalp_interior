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

// Handle Add/Edit Service
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_service'])) {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    $name = htmlspecialchars($_POST['name']);
    $short_desc = htmlspecialchars($_POST['short_desc']);
    $icon = htmlspecialchars($_POST['icon']);
    $status = htmlspecialchars($_POST['status']);
    $display_order = (int)$_POST['display_order'];
    
    // Check if new cover image is uploaded
    $has_new_image = isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0;
    $db_image = null;
    
    if ($has_new_image) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $upload_dir = '../uploads/media/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $info = pathinfo($_FILES['cover_image']['name']);
        $ext = strtolower($info['extension']);
        $filename = 'service_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $target = $upload_dir . $filename;
        
        if (in_array($ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target)) {
                $db_image = 'uploads/media/' . $filename;
            } else {
                $_SESSION['error_msg'] = "Failed to upload image.";
                header("Location: services.php");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type. Allowed: jpg, jpeg, png, webp, gif";
            header("Location: services.php");
            exit();
        }
    }

    if ($edit_id > 0) {
        // UPDATE
        if ($db_image) {
            $stmt = $conn->prepare("UPDATE services SET name=?, short_desc=?, icon=?, cover_image=?, status=?, display_order=? WHERE id=?");
            $stmt->bind_param("sssssii", $name, $short_desc, $icon, $db_image, $status, $display_order, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE services SET name=?, short_desc=?, icon=?, status=?, display_order=? WHERE id=?");
            $stmt->bind_param("ssssii", $name, $short_desc, $icon, $status, $display_order, $edit_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Service updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // INSERT
        // If no image is provided, use a placeholder
        $db_image = $db_image ? $db_image : 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
        $stmt = $conn->prepare("INSERT INTO services (name, short_desc, icon, cover_image, status, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $short_desc, $icon, $db_image, $status, $display_order);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Service added successfully!";
        } else {
            $_SESSION['error_msg'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    header("Location: services.php");
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Get image to optionally delete it from server
    $res = $conn->query("SELECT cover_image FROM services WHERE id = $delete_id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file = '../' . $row['cover_image'];
        
        if ($conn->query("DELETE FROM services WHERE id = $delete_id")) {
            // Delete associated page content
            $page_name = 'service_' . $delete_id;
            $conn->query("DELETE FROM page_content WHERE page_name = '$page_name'");
            
            if (file_exists($file) && strpos($file, 'uploads/') !== false) @unlink($file);
            $_SESSION['success_msg'] = "Service deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete from database.";
        }
    } else {
        $_SESSION['error_msg'] = "Service not found.";
    }
    header("Location: services.php");
    exit();
}

// Fetch edit data if edit_id is set
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = $conn->query("SELECT * FROM services WHERE id = $edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

$pageTitle = 'Services';
$currentPage = 'services';
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
            <div>
                <h1>Manage Services</h1>
                <p class="text-muted" style="margin-bottom: 0;">Add new services, edit basic info, or open the Live Editor for detailed pages.</p>
            </div>
            <?php if(!$edit_data): ?>
                <button class="btn-primary" onclick="switchTab('add-service')">
                    <i class="fa-solid fa-plus"></i> Add New Service
                </button>
            <?php else: ?>
                <a href="services.php" class="btn-secondary" style="background:#6c757d; color:#fff; padding:10px 15px; border-radius:5px; text-decoration:none;">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <?php if(!$edit_data): ?>
        <!-- MANAGE SERVICES VIEW -->
        <div class="tab-content active" id="view-manage">
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $items = $conn->query("SELECT * FROM services ORDER BY display_order ASC, id DESC");
                        if ($items && $items->num_rows > 0):
                            while($item = $items->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div class="user-details">
                                    <img src="<?php echo strpos($item['cover_image'], 'http') === 0 ? $item['cover_image'] : '../' . $item['cover_image']; ?>" alt="Cover" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <h4 style="font-size: 14px; margin-bottom: 4px;"><?php echo htmlspecialchars($item['name']); ?></h4>
                                        <p style="color: var(--text-muted); font-size: 12px;"><?php echo htmlspecialchars(substr($item['short_desc'], 0, 50)) . '...'; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($item['status'] == 'Active'): ?>
                                    <span class="pill new" style="background: #dcfce7; color: #166534;">Active</span>
                                <?php else: ?>
                                    <span class="pill closed">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $item['display_order']; ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="editor-service.php?id=<?php echo $item['id']; ?>" class="btn-icon" style="background: #EAB136; color: white;" title="Live Editor (Details Page)"><i class="fa-solid fa-desktop"></i></a>
                                    <a href="?edit_id=<?php echo $item['id']; ?>" class="btn-icon" style="background: #3b82f6; color: white;" title="Edit Info"><i class="fa-solid fa-pen"></i></a>
                                    <a href="?delete_id=<?php echo $item['id']; ?>" class="btn-icon delete" style="background: #dc3545; color: white;" onclick="return confirm('Delete this service and all its content?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">No services found. Add one to get started!</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ADD/EDIT SERVICE VIEW -->
        <div class="tab-content <?php echo $edit_data ? 'active' : ''; ?>" id="view-add-service">
            <div class="form-panel" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;"><?php echo $edit_data ? 'Edit Basic Info' : 'Add New Service'; ?></h3>
                
                <form action="services.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Service Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter service name (e.g. Residential Interior)" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['name']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <!-- Custom Icon Picker -->
                        <div style="margin-bottom: 15px; position: relative;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">FontAwesome Icon</label>
                            <div style="display: flex; gap: 10px;">
                                <?php $current_icon = $edit_data ? htmlspecialchars($edit_data['icon']) : 'fa-solid fa-couch'; ?>
                                <div id="selected-icon-preview" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #f8f9fa;">
                                    <i class="<?php echo $current_icon; ?>"></i>
                                </div>
                                <input type="text" name="icon" id="icon-input" value="<?php echo $current_icon; ?>" readonly style="flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer;" onclick="document.getElementById('icon-picker-dropdown').style.display='block';">
                                <button type="button" class="btn-secondary" style="padding: 10px 15px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;" onclick="document.getElementById('icon-picker-dropdown').style.display='block';">Select</button>
                            </div>
                            
                            <!-- Icon Picker Dropdown -->
                            <div id="icon-picker-dropdown" style="display: none; position: absolute; top: 75px; left: 0; width: 100%; background: white; border: 1px solid #ddd; border-radius: 5px; padding: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 100;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h4 style="margin: 0; font-size: 14px;">Select an Icon</h4>
                                    <button type="button" onclick="document.getElementById('icon-picker-dropdown').style.display='none';" style="background: none; border: none; cursor: pointer; font-size: 16px;">&times;</button>
                                </div>
                                <input type="text" id="icon-search" placeholder="Search icons..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">
                                
                                <div id="icon-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto;">
                                    <!-- Icons injected by JS -->
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Status</label>
                            <select name="status" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="Active" <?php echo ($edit_data && $edit_data['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Draft" <?php echo ($edit_data && $edit_data['status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo $edit_data ? (int)$edit_data['display_order'] : '0'; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Short Description (For Cards)</label>
                            <input type="text" name="short_desc" class="form-control" placeholder="Brief summary of the service" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['short_desc']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Service Cover Image</label>
                            <?php if ($edit_data && !empty($edit_data['cover_image'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="<?php echo strpos($edit_data['cover_image'], 'http') === 0 ? $edit_data['cover_image'] : '../' . $edit_data['cover_image']; ?>" alt="Current Image" style="height: 60px; border-radius: 5px; object-fit: cover;">
                                    <div style="font-size: 12px; color: #666;">Leave empty to keep current image</div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cover_image" accept="image/*" <?php echo $edit_data ? '' : 'required'; ?> style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1; display:flex; justify-content:flex-end; gap:15px; margin-top:10px;">
                            <?php if(!$edit_data): ?>
                                <button type="button" class="btn-primary" style="background:#f1f5f9; color:var(--text-main); border:none; padding:10px 20px; border-radius:5px; cursor:pointer;" onclick="switchTab('manage')">Cancel</button>
                            <?php endif; ?>
                            <button type="submit" name="save_service" class="btn-primary" style="background:var(--primary-color); color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">
                                <i class="fa-solid fa-floppy-disk"></i> <?php echo $edit_data ? 'Save Changes' : 'Save Service'; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById('view-' + tabId).classList.add('active');
}

// Icon Picker Logic
const iconGrid = document.getElementById('icon-grid');
const iconSearch = document.getElementById('icon-search');
const iconInput = document.getElementById('icon-input');
const iconPreview = document.getElementById('selected-icon-preview');
const dropdown = document.getElementById('icon-picker-dropdown');

const icons = [
    'fa-solid fa-couch', 'fa-solid fa-house-chimney', 'fa-solid fa-building', 'fa-solid fa-chair', 
    'fa-solid fa-pen-ruler', 'fa-solid fa-kitchen-set', 'fa-solid fa-cube', 'fa-solid fa-border-all', 
    'fa-solid fa-wifi', 'fa-solid fa-lightbulb', 'fa-solid fa-paintbrush', 'fa-solid fa-hammer',
    'fa-solid fa-tools', 'fa-solid fa-palette'
];

function renderIcons(filter = '') {
    iconGrid.innerHTML = '';
    icons.filter(icon => icon.includes(filter.toLowerCase())).forEach(icon => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.cssText = 'border: 1px solid #eee; background: white; padding: 10px; border-radius: 4px; cursor: pointer; font-size: 18px; transition: 0.2s;';
        btn.innerHTML = `<i class="${icon}"></i>`;
        btn.title = icon;
        
        btn.onmouseover = () => btn.style.background = '#f0f0f0';
        btn.onmouseout = () => btn.style.background = 'white';
        
        btn.onclick = () => {
            iconInput.value = icon;
            iconPreview.innerHTML = `<i class="${icon}"></i>`;
            dropdown.style.display = 'none';
        };
        iconGrid.appendChild(btn);
    });
}

renderIcons();

iconSearch.addEventListener('input', (e) => {
    renderIcons(e.target.value);
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('#icon-picker-dropdown') && !e.target.closest('#icon-input') && !e.target.closest('button[onclick*="icon-picker-dropdown"]')) {
        dropdown.style.display = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>

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

// Handle Add/Edit Award
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_award'])) {
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    $icon = htmlspecialchars($_POST['icon']);
    $brand = htmlspecialchars($_POST['brand']);
    $title = htmlspecialchars($_POST['title']);
    $day_text = htmlspecialchars($_POST['day_text']);
    $date_text = htmlspecialchars($_POST['date_text']);
    $display_order = (int)$_POST['display_order'];
    
    // Check if new image is uploaded
    $has_new_image = isset($_FILES['image']) && $_FILES['image']['error'] == 0;
    $db_image = null;
    
    if ($has_new_image) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $upload_dir = '../uploads/media/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $info = pathinfo($_FILES['image']['name']);
        $ext = strtolower($info['extension']);
        $name = 'award_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $target = $upload_dir . $name;
        
        if (in_array($ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $db_image = 'uploads/media/' . $name;
            } else {
                $_SESSION['error_msg'] = "Failed to upload image.";
                header("Location: manage_awards.php");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Invalid file type. Allowed: jpg, jpeg, png, webp, gif";
            header("Location: manage_awards.php");
            exit();
        }
    }

    if ($edit_id > 0) {
        // UPDATE
        if ($db_image) {
            $stmt = $conn->prepare("UPDATE awards SET image=?, icon=?, brand=?, title=?, day_text=?, date_text=?, display_order=? WHERE id=?");
            $stmt->bind_param("ssssssii", $db_image, $icon, $brand, $title, $day_text, $date_text, $display_order, $edit_id);
        } else {
            $stmt = $conn->prepare("UPDATE awards SET icon=?, brand=?, title=?, day_text=?, date_text=?, display_order=? WHERE id=?");
            $stmt->bind_param("sssssii", $icon, $brand, $title, $day_text, $date_text, $display_order, $edit_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Award updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // INSERT
        if ($db_image) {
            $stmt = $conn->prepare("INSERT INTO awards (image, icon, brand, title, day_text, date_text, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $db_image, $icon, $brand, $title, $day_text, $date_text, $display_order);
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Award added successfully!";
            } else {
                $_SESSION['error_msg'] = "Database Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_msg'] = "Please select an image.";
        }
    }
    
    header("Location: manage_awards.php");
    exit();
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    $res = $conn->query("SELECT image FROM awards WHERE id = $delete_id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file = '../' . $row['image'];
        
        if ($conn->query("DELETE FROM awards WHERE id = $delete_id")) {
            if (file_exists($file) && strpos($file, 'assets/') === false) @unlink($file);
            $_SESSION['success_msg'] = "Award deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete from database.";
        }
    } else {
        $_SESSION['error_msg'] = "Award not found.";
    }
    header("Location: manage_awards.php");
    exit();
}

// Fetch edit data if edit_id is set
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = $conn->query("SELECT * FROM awards WHERE id = $edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

$pageTitle = "Awards & Press";
$currentPage = "manage_awards";
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
                <h1>Manage Awards & Press</h1>
                <p class="text-muted" style="margin-top: 5px; margin-bottom: 0;">Add, edit, and manage the awards displayed on the frontend accordion.</p>
            </div>
        </div>
        
        <div class="form-grid-1x2" style="margin-top: 30px;">
            
            <!-- Upload/Edit Form -->
            <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); min-width: 0;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;"><?php echo $edit_data ? 'Edit Award' : 'Add New Award'; ?></h3>
                
                <?php if ($edit_data): ?>
                    <a href="manage_awards.php" style="display: inline-block; margin-bottom: 15px; font-size: 14px; color: #666;"><i class="fa-solid fa-arrow-left"></i> Cancel Edit</a>
                <?php endif; ?>
                
                <form action="manage_awards.php" method="POST" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $edit_data['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Background Image</label>
                        <?php if ($edit_data && !empty($edit_data['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="../<?php echo $edit_data['image']; ?>" alt="Current Image" style="height: 60px; border-radius: 5px;">
                                <div style="font-size: 12px; color: #666;">Leave empty to keep current image</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*" <?php echo $edit_data ? '' : 'required'; ?> style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Brand / Source</label>
                        <input type="text" name="brand" placeholder="e.g. Ranchi Express" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['brand']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>

                    <div style="margin-bottom: 15px; position: relative;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Icon</label>
                        <div style="display: flex; gap: 10px;">
                            <?php $current_icon = $edit_data ? htmlspecialchars($edit_data['icon']) : 'fa-solid fa-award'; ?>
                            <div id="selected-icon-preview" style="width: 42px; height: 42px; border: 1px solid #ddd; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #f8f9fa;">
                                <i class="<?php echo $current_icon; ?>"></i>
                            </div>
                            <input type="text" name="icon" id="icon-input" value="<?php echo $current_icon; ?>" readonly style="flex-grow: 1; min-width: 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px; cursor: pointer;" onclick="document.getElementById('icon-picker-dropdown').style.display='block';">
                            <button type="button" class="btn-secondary" style="padding: 10px 15px;" onclick="document.getElementById('icon-picker-dropdown').style.display='block';">Select</button>
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

                    <script>
                        const iconGrid = document.getElementById('icon-grid');
                        const iconSearch = document.getElementById('icon-search');
                        const iconInput = document.getElementById('icon-input');
                        const iconPreview = document.getElementById('selected-icon-preview');
                        const dropdown = document.getElementById('icon-picker-dropdown');
                        
                        const icons = [
                            'fa-solid fa-award', 'fa-solid fa-newspaper', 'fa-solid fa-trophy', 'fa-solid fa-star', 'fa-solid fa-medal', 'fa-solid fa-crown',
                            'fa-solid fa-ranking-star', 'fa-solid fa-certificate', 'fa-solid fa-gem', 'fa-solid fa-thumbs-up', 'fa-solid fa-heart', 'fa-solid fa-building',
                            'fa-solid fa-house', 'fa-solid fa-couch', 'fa-solid fa-pen-nib', 'fa-solid fa-paintbrush', 'fa-solid fa-lightbulb', 'fa-solid fa-palette'
                        ];

                        function renderIcons(filter = '') {
                            iconGrid.innerHTML = '';
                            icons.filter(icon => icon.includes(filter.toLowerCase())).forEach(icon => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'icon-btn';
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

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Title</label>
                        <input type="text" name="title" placeholder="e.g. Featured: Ranchi Express" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>

                    <div class="form-grid" style="margin-bottom: 15px; gap: 10px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Day</label>
                            <input type="text" name="day_text" placeholder="e.g. Sunday" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['day_text']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Date</label>
                            <input type="text" name="date_text" placeholder="e.g. Oct 02, 2022" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['date_text']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px;">Display Order</label>
                        <input type="number" name="display_order" value="<?php echo $edit_data ? (int)$edit_data['display_order'] : '0'; ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <button type="submit" name="save_award" class="btn-primary" style="width: 100%; padding: 12px; background:var(--primary-color); color:#fff; border:none; border-radius:5px; cursor:pointer;">
                        <i class="fa-solid fa-floppy-disk"></i> <?php echo $edit_data ? 'Save Changes' : 'Save Changes'; ?>
                    </button>
                </form>
            </div>
            
            <!-- Items Table -->
            <div class="card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); min-width: 0;">
                <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Existing Awards</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa; text-align: left;">
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Image</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Brand & Title</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Date</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Order</th>
                                <th style="padding: 12px; border-bottom: 2px solid #ddd;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $items = $conn->query("SELECT * FROM awards ORDER BY display_order ASC, id DESC");
                            if ($items && $items->num_rows > 0):
                                while($item = $items->fetch_assoc()):
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">
                                    <img src="../<?php echo $item['image']; ?>" alt="Bg" style="width: 80px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td style="padding: 12px;">
                                    <strong><i class="<?php echo $item['icon']; ?>"></i> <?php echo htmlspecialchars($item['brand']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($item['title']); ?></small>
                                </td>
                                <td style="padding: 12px;">
                                    <?php echo htmlspecialchars($item['day_text']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($item['date_text']); ?></small>
                                </td>
                                <td style="padding: 12px;"><?php echo $item['display_order']; ?></td>
                                <td style="padding: 12px; white-space: nowrap;">
                                    <a href="?edit_id=<?php echo $item['id']; ?>" class="btn-primary" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 0.85rem; text-decoration:none; border-radius:3px; margin-right: 5px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="?delete_id=<?php echo $item['id']; ?>" class="btn-secondary" style="background: #dc3545; color: white; padding: 6px 12px; font-size: 0.85rem; text-decoration:none; border-radius:3px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Delete this award?');"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" style="padding: 30px; text-align: center; color: #777;">
                                    No awards found.
                                </td>
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

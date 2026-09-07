<?php
require_once 'config/db.php';
$pageTitle = 'Manage Team';
$currentPage = 'manage_team';

$success_msg = '';
$error_msg = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'delete') $success_msg = "Team member deleted successfully!";
    if ($_GET['success'] == 'update') $success_msg = "Team member updated successfully!";
    if ($_GET['success'] == 'insert') $success_msg = "Team member added successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: manage_team.php?success=delete");
        exit;
    } else {
        $error_msg = "Failed to delete team member.";
    }
    $stmt->close();
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_team'])) {
    $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
    $name = $conn->real_escape_string($_POST['name']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $whatsapp = $conn->real_escape_string($_POST['whatsapp'] ?? '');
    $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    $status = $conn->real_escape_string($_POST['status']);

    $image = '';

    if (empty($name) || empty($role)) {
        $error_msg = "Name and Role are required.";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/team/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_extension, $allowed_exts)) {
                $image = 'team_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $image;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error_msg = "Failed to upload image.";
                }
            } else {
                $error_msg = "Invalid image format. Allowed: jpg, jpeg, png, webp.";
            }
        } elseif ($member_id == 0) {
            $error_msg = "Image is required for new team members.";
        }

        if (empty($error_msg)) {
            if ($member_id > 0) {
                // Update existing
                $update_query = "UPDATE team_members SET name=?, role=?, email=?, whatsapp=?, display_order=?, status=?";
                $params = [$name, $role, $email, $whatsapp, $display_order, $status];
                $types = "ssssis";

                if (!empty($image)) {
                    $update_query .= ", image=?";
                    $params[] = $image;
                    $types .= "s";
                }
                $update_query .= " WHERE id=?";
                $params[] = $member_id;
                $types .= "i";

                $stmt = $conn->prepare($update_query);
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    header("Location: manage_team.php?success=update");
                    exit;
                } else {
                    $error_msg = "Database error: " . $conn->error;
                }
                $stmt->close();
            } else {
                // Insert new
                $stmt = $conn->prepare("INSERT INTO team_members (name, role, image, email, whatsapp, display_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssis", $name, $role, $image, $email, $whatsapp, $display_order, $status);
                if ($stmt->execute()) {
                    header("Location: manage_team.php?success=insert");
                    exit;
                } else {
                    $error_msg = "Database error: " . $conn->error;
                }
                $stmt->close();
            }
        }
    }
}

// Fetch for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
    }
    $stmt->close();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1><?php echo isset($_GET['edit']) ? 'Edit Team Member' : 'Manage Team'; ?></h1>
            <?php if(!isset($_GET['edit'])): ?>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn-primary" onclick="switchTab('add-team')">
                    <i class="fa-solid fa-plus"></i> Add New Member
                </button>
            </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($success_msg)): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- MANAGE TEAM VIEW -->
        <div class="tab-content <?php echo isset($_GET['edit']) ? '' : 'active'; ?>" id="view-manage">
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name & Role</th>
                            <th>Contact</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM team_members ORDER BY display_order ASC, created_at DESC";
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $img_src = !empty($row['image']) ? '../uploads/team/' . htmlspecialchars($row['image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['name']);
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo $img_src; ?>" class="user-avatar" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            </td>
                            <td>
                                <h4 style="font-size: 14px; margin-bottom: 2px;"><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p style="color: var(--text-muted); font-size: 11px; text-transform: uppercase;"><?php echo htmlspecialchars($row['role']); ?></p>
                            </td>
                            <td>
                                <?php if(!empty($row['email'])): ?>
                                    <span style="font-size: 12px; display: block;"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></span>
                                <?php endif; ?>
                                <?php if(!empty($row['whatsapp'])): ?>
                                    <span style="font-size: 12px; display: block;"><i class="fa-brands fa-whatsapp"></i> <?php echo htmlspecialchars($row['whatsapp']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo (int)$row['display_order']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="manage_team.php?edit=<?php echo $row['id']; ?>" class="btn-icon btn-edit" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="manage_team.php?delete=<?php echo $row['id']; ?>" class="btn-icon btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this team member?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center; padding: 20px;'>No team members found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD / EDIT TEAM VIEW -->
        <div class="tab-content <?php echo isset($_GET['edit']) ? 'active' : ''; ?>" id="view-add-team">
            <div class="form-container">
                <form action="manage_team.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="member_id" value="<?php echo isset($edit_data['id']) ? $edit_data['id'] : 0; ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo isset($edit_data['name']) ? htmlspecialchars($edit_data['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Role / Title <span class="required">*</span></label>
                            <input type="text" name="role" class="form-control" value="<?php echo isset($edit_data['role']) ? htmlspecialchars($edit_data['role']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo isset($edit_data['email']) ? htmlspecialchars($edit_data['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>WhatsApp Number</label>
                            <input type="text" name="whatsapp" class="form-control" value="<?php echo isset($edit_data['whatsapp']) ? htmlspecialchars($edit_data['whatsapp']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo isset($edit_data['display_order']) ? (int)$edit_data['display_order'] : 0; ?>">
                            <span class="help-text">Lower numbers appear first</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo (isset($edit_data['status']) && $edit_data['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (isset($edit_data['status']) && $edit_data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>Member Photo <?php echo !isset($edit_data) ? '<span class="required">*</span>' : ''; ?></label>
                            <div class="image-upload-wrapper">
                                <?php if(isset($edit_data['image']) && !empty($edit_data['image'])): ?>
                                    <div class="current-image" style="margin-bottom: 10px;">
                                        <img src="../uploads/team/<?php echo htmlspecialchars($edit_data['image']); ?>" style="width: 150px; height: auto; border-radius: 8px;">
                                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Current Photo</p>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp" <?php echo !isset($edit_data) ? 'required' : ''; ?>>
                                <span class="help-text">Recommended size: 500x600px. Max size: 2MB.</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px; display: flex; gap: 15px; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 20px;">
                        <button type="submit" name="save_team" class="btn-primary" style="padding: 12px 30px; font-size: 16px;">
                            <i class="fa-solid fa-save"></i> <?php echo isset($edit_data) ? 'Update Team Member' : 'Save Team Member'; ?>
                        </button>
                        <?php if(isset($_GET['edit'])): ?>
                            <a href="manage_team.php" class="btn-secondary" style="padding: 12px 30px; font-size: 16px; text-decoration: none;">Cancel</a>
                        <?php else: ?>
                            <button type="button" class="btn-secondary" style="padding: 12px 30px; font-size: 16px;" onclick="switchTab('manage')">Cancel</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    
    if (tab === 'add-team') {
        document.getElementById('view-add-team').classList.add('active');
        document.querySelector('.page-header h1').innerText = 'Add New Member';
    } else {
        document.getElementById('view-manage').classList.add('active');
        document.querySelector('.page-header h1').innerText = 'Manage Team';
    }
}
</script>

<?php include 'includes/footer.php'; ?>

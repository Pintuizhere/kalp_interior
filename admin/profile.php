<?php
require_once 'config/db.php';
$pageTitle = 'Settings';
$currentPage = 'profile'; // Keep sidebar active state

include 'includes/header.php';
include 'includes/sidebar.php';

$success_msg = '';
$error_msg = '';
$current_user_id = $_SESSION['admin_id'] ?? 0;
$upload_dir = '../uploads/profiles/';

// Handle Profile Updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Update Personal Details ---
    if (isset($_POST['update_personal'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $job_title = trim($_POST['job_title']);
        
        if (empty($email)) {
            $error_msg = "Email address cannot be empty.";
        } else {
            $check_stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
            $check_stmt->bind_param("si", $email, $current_user_id);
            $check_stmt->execute();
            if ($check_stmt->get_result()->num_rows > 0) {
                $error_msg = "This email address is already in use.";
            } else {
                $stmt = $conn->prepare("UPDATE admin_users SET full_name = ?, email = ?, job_title = ? WHERE id = ?");
                $stmt->bind_param("sssi", $full_name, $email, $job_title, $current_user_id);
                if ($stmt->execute()) {
                    $success_msg = "Personal details updated successfully!";
                    $_SESSION['admin_email'] = $email;
                } else {
                    $error_msg = "Error updating details.";
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    }

    // --- Update Profile Picture ---
    if (isset($_POST['update_picture'])) {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
            $file = $_FILES['profile_image'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($file_extension, $allowed_exts)) {
                $new_file_name = 'profile_' . $current_user_id . '_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("UPDATE admin_users SET profile_image = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_file_name, $current_user_id);
                    $stmt->execute();
                    $stmt->close();
                    $success_msg = "Profile picture updated successfully!";
                } else {
                    $error_msg = "Failed to upload image.";
                }
            } else {
                $error_msg = "Invalid image format. Supported formats: JPG, PNG, WEBP.";
            }
        }
    }

    // --- Remove Profile Picture ---
    if (isset($_POST['remove_picture'])) {
        $stmt = $conn->prepare("UPDATE admin_users SET profile_image = NULL WHERE id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $stmt->close();
        $success_msg = "Profile picture removed successfully!";
    }

    // --- Update Password ---
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($new_password)) {
            $error_msg = "Password cannot be empty.";
        } elseif ($new_password !== $confirm_password) {
            $error_msg = "New passwords do not match.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $current_user_id);
            if ($stmt->execute()) {
                $success_msg = "Password updated successfully!";
            } else {
                $error_msg = "Error updating password.";
            }
            $stmt->close();
        }
    }
}

// Fetch current user details
$user_email = '';
$user_full_name = '';
$user_job_title = '';
$user_image = null;
$user_role = 'SUPER ADMIN';
$stmt = $conn->prepare("SELECT email, full_name, job_title, profile_image, role FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $user_email = $row['email'];
    $user_full_name = $row['full_name'];
    $user_job_title = $row['job_title'];
    $user_image = $row['profile_image'];
    if (!empty($row['role'])) {
        $user_role = strtoupper($row['role']);
    }
}
$stmt->close();
?>

<style>
    .settings-header {
        font-family: 'League Spartan', sans-serif;
        font-size: 28px;
        font-weight: 800;
        text-transform: uppercase;
        color: #000;
        margin-bottom: 25px;
    }
    .settings-tabs {
        display: flex;
        gap: 30px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    .settings-tab {
        padding: 10px 0;
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
    }
    .settings-tab.active {
        color: #000;
    }
    .settings-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #ef4444; /* red underline */
    }
    .settings-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        min-width: 0;
    }
    .settings-card h3 {
        margin: 0 0 25px 0;
        font-family: 'League Spartan', sans-serif;
        font-size: 20px;
        color: #000;
    }
    .profile-pic-container {
        display: flex;
        align-items: center;
        gap: 25px;
        flex-wrap: wrap;
    }
    .profile-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 30px;
        color: #475569;
        font-weight: 700;
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-info p {
        margin: 5px 0 15px 0;
        color: #64748b;
        font-size: 13px;
    }
    .btn-upload {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #000;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-upload:hover {
        background: #f1f5f9;
    }
    .btn-remove {
        background: none;
        border: none;
        color: #ef4444;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 8px 16px;
    }
    .btn-remove:hover {
        text-decoration: underline;
    }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 8px;
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: #0f172a;
        box-sizing: border-box;
    }
    .form-control:focus {
        outline: none;
        border-color: #94a3b8;
    }
    .btn-save {
        background: #000;
        color: #fff;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-family: 'League Spartan', sans-serif;
        text-transform: uppercase;
        font-size: 13px;
        float: right;
    }
</style>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <div style="max-width: 900px;">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo $success_msg; ?>
            </div>
            <script>
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            </script>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 8px;"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="settings-header">SETTINGS</div>
        
        <div class="settings-tabs">
            <div class="settings-tab active" onclick="switchTab('profile', this)">My Profile</div>
            <div class="settings-tab" onclick="switchTab('security', this)">Account Security</div>
        </div>

        <!-- My Profile Tab -->
        <div id="tab-profile" style="display: block;">
            <!-- Profile Picture Card -->
            <div class="settings-card">
                <div class="profile-pic-container">
                    <div class="profile-avatar">
                        <?php if ($user_image): ?>
                            <img src="<?php echo $upload_dir . htmlspecialchars($user_image); ?>" alt="Profile">
                        <?php else: ?>
                            <?php 
                            $displayName = !empty($user_full_name) ? $user_full_name : 'User';
                            if (empty($user_full_name)) {
                                $parts = explode('@', $user_email);
                                $displayName = $parts[0];
                            }
                            echo strtoupper(substr($displayName, 0, 1)); 
                            ?>
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h3 style="margin: 0; font-family: 'Inter', sans-serif; font-size: 16px; font-weight: 700;">Profile Picture</h3>
                        <p>PNG, JPG or WEBP under 5MB. You are logged in as a <?php echo htmlspecialchars($user_role); ?>.</p>
                        
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                            <form action="profile.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                                <input type="hidden" name="update_picture" value="1">
                                <input type="file" name="profile_image" id="profile_image" style="display: none;" accept="image/png, image/jpeg, image/webp" onchange="document.getElementById('uploadForm').submit();">
                                <button type="button" class="btn-upload" onclick="document.getElementById('profile_image').click();">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Upload Image
                                </button>
                            </form>
                            
                            <?php if ($user_image): ?>
                            <form action="profile.php" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                                <input type="hidden" name="remove_picture" value="1">
                                <button type="submit" class="btn-remove">Remove</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Details Card -->
            <div class="settings-card">
                <h3>Personal Details</h3>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="update_personal" value="1">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user_full_name); ?>">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label>Job Title / Designation</label>
                        <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($user_job_title); ?>">
                    </div>
                    
                    <div style="overflow: hidden;">
                        <button type="submit" class="btn-save">SAVE CHANGES</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Security Tab -->
        <div id="tab-security" style="display: none;">
            <div class="settings-card">
                <h3>Update Password</h3>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="update_password" value="1">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 30px;">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    
                    <div style="overflow: hidden;">
                        <button type="submit" class="btn-save">UPDATE PASSWORD</button>
                    </div>
                </form>
            </div>
        </div>

        </div>
    </div>
</div>

<script>
function switchTab(tabId, element) {
    // Hide all tabs
    document.getElementById('tab-profile').style.display = 'none';
    document.getElementById('tab-security').style.display = 'none';
    
    // Remove active class from all tab links
    const tabs = document.querySelectorAll('.settings-tab');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show selected tab
    document.getElementById('tab-' + tabId).style.display = 'block';
    
    // Add active class to clicked tab link
    element.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>

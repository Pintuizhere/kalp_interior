<?php
require_once 'config/db.php';
$pageTitle = 'Super Admin';
$currentPage = 'users';

$success_msg = '';
$error_msg = '';
$current_user_id = $_SESSION['admin_id'] ?? 0;

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'Super Admin';
    $status = 'Active'; // Hardcoded based on image
    
    if (empty($email) || empty($password) || empty($full_name)) {
        $error_msg = "Full Name, Email and Password are required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $error_msg = "A user with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO admin_users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $full_name, $email, $hashed_password, $role, $status);
            
            if ($insert_stmt->execute()) {
                header("Location: users.php?success=add");
                exit;
            } else {
                $error_msg = "Error adding user: " . $conn->error;
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    // Security: prevent deleting yourself
    if ($delete_id == $current_user_id) {
        $error_msg = "You cannot delete your own active account.";
    } else {
        $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            header("Location: users.php?success=delete");
            exit;
        } else {
            $error_msg = "Error deleting user.";
        }
        $stmt->close();
    }
}

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'add') $success_msg = "New admin user added successfully!";
    if ($_GET['success'] == 'delete') $success_msg = "User deleted successfully!";
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $success_msg; ?>
            </div>
            <script>
                if (window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('success');
                    window.history.replaceState({path:url.href}, '', url.href);
                }
            </script>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="margin:0; display: flex; align-items: center; gap: 10px; font-family: 'League Spartan', sans-serif;">
                    <i class="fa-solid fa-user-shield" style="color: var(--primary-color);"></i> SUPER ADMIN
                </h1>
                <p style="color:var(--text-muted); margin-top:5px;">Manage administrative users and global access control.</p>
            </div>
            <div>
                <button class="btn-primary" onclick="openAddAdminModal()" style="display: flex; align-items: center; gap: 8px; font-weight: 600; padding: 12px 20px; border-radius: 8px; background: #000; color: #fff; border: none; font-size: 13px; font-family: 'League Spartan', sans-serif; cursor: pointer; text-transform: uppercase;">
                    <i class="fa-solid fa-user-plus"></i> Add Admin User
                </button>
            </div>
        </div>

        <style>
            .super-admin-table {
                width: 100%;
                border-collapse: collapse;
            }
            .super-admin-table th {
                text-align: left;
                padding: 15px 25px;
                color: #8b95a5;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 700;
                border-bottom: 1px solid #f1f5f9;
            }
            .super-admin-table td {
                padding: 15px 25px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: middle;
            }
            .super-admin-table tr:last-child td {
                border-bottom: none;
            }
            .sa-name-col {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .sa-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                color: #475569;
                font-size: 16px;
                overflow: hidden;
            }
            .sa-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .sa-role-pill {
                display: inline-block;
                padding: 4px 10px;
                background: #ffe4e6;
                color: #e11d48;
                font-size: 10px;
                font-weight: 700;
                border-radius: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .sa-status-dot {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 700;
                color: #10b981;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .sa-status-dot::before {
                content: '';
                display: inline-block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #10b981;
            }
            
            /* Modal Styles */
            .sa-modal-overlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.4); z-index: 99999;
                display: flex; align-items: center; justify-content: center;
                opacity: 0; visibility: hidden; transition: all 0.3s ease;
                backdrop-filter: blur(2px);
            }
            .sa-modal-overlay.active { opacity: 1; visibility: visible; }
            .sa-modal-box {
                background: #fff; width: 100%; max-width: 500px;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
                overflow: hidden;
            }
            .sa-modal-overlay.active .sa-modal-box { transform: translateY(0) scale(1); }
            .sa-modal-header {
                padding: 20px 25px;
                display: flex; justify-content: space-between; align-items: center;
                border-bottom: 1px solid #f1f5f9;
            }
            .sa-modal-header h3 {
                margin: 0; font-family: 'League Spartan', sans-serif; font-size: 20px; text-transform: uppercase; font-weight: 800; color: #000;
            }
            .sa-modal-close {
                background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;
            }
            .sa-modal-body {
                padding: 25px;
            }
            .sa-form-group { margin-bottom: 20px; }
            .sa-form-group label {
                display: block; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
            }
            .sa-form-control {
                width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px;
                font-family: 'Inter', sans-serif; font-size: 14px; color: #334155; transition: 0.2s; box-sizing: border-box;
            }
            .sa-form-control:focus {
                outline: none; border-color: #94a3b8;
            }
            .sa-modal-footer {
                padding: 20px 25px; background: #fff;
                display: flex; justify-content: flex-end; gap: 15px;
                border-top: 1px solid #f1f5f9;
            }
        </style>

        <div class="table-wrapper" style="padding: 0; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); overflow-x: auto; background: #fff;">
            <table class="super-admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = $conn->query("SELECT id, full_name, email, role, status FROM admin_users ORDER BY id ASC");
                    
                    if ($users->num_rows > 0) {
                        while($row = $users->fetch_assoc()) {
                            $is_current = ($row['id'] == $current_user_id);
                            
                            // Determine display name
                            $displayName = !empty($row['full_name']) ? htmlspecialchars($row['full_name']) : 'User';
                            if (empty($row['full_name'])) {
                                $parts = explode('@', $row['email']);
                                $displayName = strtoupper($parts[0]);
                            }
                            
                            $initial = strtoupper(substr($displayName, 0, 1));
                            
                            $roleName = !empty($row['role']) ? htmlspecialchars($row['role']) : 'SUPER ADMIN';
                            $status = !empty($row['status']) ? htmlspecialchars($row['status']) : 'ACTIVE';
                            ?>
                            <tr>
                                <td>
                                    <div class="sa-name-col">
                                        <div class="sa-avatar">
                                            <?php echo $initial; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 14px; color: #1e293b; text-transform: uppercase;">
                                                <?php echo $displayName; ?>
                                                <?php if($is_current): ?>
                                                    <span style="font-size: 10px; background: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; margin-left: 5px; text-transform: none;">You</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span style="color: #64748b; font-size: 14px;"><?php echo htmlspecialchars($row['email']); ?></span></td>
                                <td><span class="sa-role-pill"><?php echo $roleName; ?></span></td>
                                <td><span class="sa-status-dot"><?php echo $status; ?></span></td>
                                <td>
                                    <?php if(!$is_current): ?>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="action-btn delete" style="color: #94a3b8; background: transparent; padding: 5px; font-size: 16px;">
                                            <i class="fa-solid fa-key"></i>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #cbd5e1; padding: 5px; font-size: 16px;"><i class="fa-solid fa-key"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #94a3b8;'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="sa-modal-overlay" id="addAdminModal">
    <div class="sa-modal-box">
        <form action="users.php" method="POST">
            <input type="hidden" name="add_user" value="1">
            
            <div class="sa-modal-header">
                <h3>Add New Admin</h3>
                <button type="button" class="sa-modal-close" onclick="closeAddAdminModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="sa-modal-body">
                <div class="sa-form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="sa-form-control" placeholder="e.g. John Doe" required>
                </div>
                
                <div class="sa-form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="sa-form-control" placeholder="admin@company.com" required>
                </div>
                
                <div class="sa-form-group">
                    <label>Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="adminPassword" class="sa-form-control" placeholder="Create a password" required>
                        <i class="fa-solid fa-eye" id="toggleAdminPassword" style="position: absolute; right: 15px; top: 15px; color: #94a3b8; cursor: pointer;"></i>
                    </div>
                </div>
                
                <div class="sa-form-group">
                    <label>Role Level</label>
                    <select name="role" class="sa-form-control">
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admin (Standard)">Admin (Standard)</option>
                        <option value="Editor">Editor</option>
                    </select>
                </div>
            </div>
            
            <div class="sa-modal-footer">
                <button type="button" class="custom-modal-btn cancel" onclick="closeAddAdminModal()">CANCEL</button>
                <button type="submit" style="background: #000; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; font-family: 'League Spartan', sans-serif; text-transform: uppercase;">Create User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAdminModal() {
    document.getElementById('addAdminModal').classList.add('active');
}
function closeAddAdminModal() {
    document.getElementById('addAdminModal').classList.remove('active');
}

// Password toggle
document.getElementById('toggleAdminPassword').addEventListener('click', function() {
    const pwd = document.getElementById('adminPassword');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        this.classList.remove('fa-eye');
        this.classList.add('fa-eye-slash');
    } else {
        pwd.type = 'password';
        this.classList.remove('fa-eye-slash');
        this.classList.add('fa-eye');
    }
});
</script>

<?php include 'includes/footer.php'; ?>

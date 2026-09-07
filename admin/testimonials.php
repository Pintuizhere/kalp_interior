<?php
require_once 'config/db.php';
$pageTitle = 'Testimonials';
$currentPage = 'testimonials';

$success_msg = '';
$error_msg = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'delete') $success_msg = "Testimonial deleted successfully!";
    if ($_GET['success'] == 'update') $success_msg = "Testimonial updated successfully!";
    if ($_GET['success'] == 'insert') $success_msg = "Testimonial saved successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: testimonials.php?success=delete");
        exit;
    } else {
        $error_msg = "Failed to delete testimonial.";
    }
    $stmt->close();
}

// Handle Stats Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_stats'])) {
    $success = true;
    $page_target = 'testimonial_stats';
    $check_stmt = $conn->prepare("SELECT 1 FROM page_content WHERE page_name = ? AND section_key = ?");
    $update_stmt = $conn->prepare("UPDATE page_content SET content_value = ? WHERE page_name = ? AND section_key = ?");
    $insert_stmt = $conn->prepare("INSERT INTO page_content (page_name, section_key, content_value) VALUES (?, ?, ?)");

    if (isset($_POST['stats']) && is_array($_POST['stats'])) {
        foreach ($_POST['stats'] as $section_key => $content_value) {
            $check_stmt->bind_param("ss", $page_target, $section_key);
            $check_stmt->execute();
            $res = $check_stmt->get_result();
            if ($res->num_rows > 0) {
                $update_stmt->bind_param("sss", $content_value, $page_target, $section_key);
                if (!$update_stmt->execute()) $success = false;
            } else {
                $insert_stmt->bind_param("sss", $page_target, $section_key, $content_value);
                if (!$insert_stmt->execute()) $success = false;
            }
        }
    }
    
    if ($success) {
        $success_msg = "Testimonial stats updated successfully!";
    } else {
        $error_msg = "Error saving stats.";
    }
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_testimonial'])) {
    $testimonial_id = isset($_POST['testimonial_id']) ? (int)$_POST['testimonial_id'] : 0;
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_role = $conn->real_escape_string($_POST['client_role'] ?? '');
    $company_name = $conn->real_escape_string($_POST['company_name'] ?? '');
    $company_icon = $conn->real_escape_string($_POST['company_icon'] ?? '');
    $content = $conn->real_escape_string($_POST['content']);
    $status = $conn->real_escape_string($_POST['status']);
    $company_logo_size = isset($_POST['company_logo_size']) ? (int)$_POST['company_logo_size'] : 40;

    $client_image = '';
    $company_logo = '';

    if (empty($client_name) || empty($content)) {
        $error_msg = "Name and Content are required.";
    } else {
        if (isset($_FILES['client_image']) && $_FILES['client_image']['error'] == 0) {
            $upload_dir = '../uploads/testimonials/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_extension = strtolower(pathinfo($_FILES['client_image']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_extension, $allowed_exts)) {
                $client_image = 'testi_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $client_image;
                if (!move_uploaded_file($_FILES['client_image']['tmp_name'], $target_file)) {
                    $error_msg = "Failed to upload image.";
                }
            } else {
                $error_msg = "Invalid image format.";
            }
        }

        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] == 0) {
            $upload_dir = '../uploads/testimonials/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_extension = strtolower(pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];

            if (in_array($file_extension, $allowed_exts)) {
                $company_logo = 'logo_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $company_logo;
                if (!move_uploaded_file($_FILES['company_logo']['tmp_name'], $target_file)) {
                    $error_msg = "Failed to upload company logo.";
                }
            } else {
                $error_msg = "Invalid company logo format.";
            }
        }

        if (empty($error_msg)) {
            if ($testimonial_id > 0) {
                // Update existing
                $update_query = "UPDATE testimonials SET client_name=?, client_role=?, company_name=?, company_icon=?, content=?, status=?, company_logo_size=?";
                $params = [$client_name, $client_role, $company_name, $company_icon, $content, $status, $company_logo_size];
                $types = "ssssssi";

                if (!empty($client_image)) {
                    $update_query .= ", client_image=?";
                    $params[] = $client_image;
                    $types .= "s";
                }
                if (!empty($company_logo)) {
                    $update_query .= ", company_logo=?";
                    $params[] = $company_logo;
                    $types .= "s";
                }
                $update_query .= " WHERE id=?";
                $params[] = $testimonial_id;
                $types .= "i";

                $stmt = $conn->prepare($update_query);
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    header("Location: testimonials.php?success=update");
                    exit;
                } else {
                    $error_msg = "Database error: " . $conn->error;
                }
                $stmt->close();
            } else {
                // Insert new
                $stmt = $conn->prepare("INSERT INTO testimonials (client_name, client_role, company_name, company_logo, company_icon, client_image, content, status, company_logo_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssi", $client_name, $client_role, $company_name, $company_logo, $company_icon, $client_image, $content, $status, $company_logo_size);
                if ($stmt->execute()) {
                    header("Location: testimonials.php?success=insert");
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
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ?");
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

// Fetch Stats
$stats_data = [];
$stmt = $conn->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = 'testimonial_stats'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats_data[$row['section_key']] = $row['content_value'];
}
$stmt->close();
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1><?php echo isset($_GET['edit']) ? 'Edit Testimonial' : 'Manage Testimonials'; ?></h1>
            <?php if(!isset($_GET['edit'])): ?>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn-primary" style="background-color: var(--accent-color); color: var(--text-dark);" onclick="switchTab('manage-stats')">
                    <i class="fa-solid fa-chart-bar"></i> Manage Stats
                </button>
                <button class="btn-primary" onclick="switchTab('add-testimonial')">
                    <i class="fa-solid fa-plus"></i> Add New Testimonial
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

        <!-- MANAGE TESTIMONIALS VIEW -->
        <div class="tab-content <?php echo isset($_GET['edit']) ? '' : 'active'; ?>" id="view-manage">
            <div class="table-wrapper">
                <div class="table-toolbar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search testimonials...">
                    </div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client Info</th>
                            <th>Company / Brand</th>
                            <th>Testimonial Preview</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM testimonials ORDER BY created_at DESC";
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $img_src = !empty($row['client_image']) ? '../uploads/testimonials/' . htmlspecialchars($row['client_image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['client_name']);
                        ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <img src="<?php echo $img_src; ?>" class="user-avatar" alt="<?php echo htmlspecialchars($row['client_name']); ?>">
                                    <div class="user-details">
                                        <h4 style="font-size: 14px; margin-bottom: 2px;"><?php echo htmlspecialchars($row['client_name']); ?></h4>
                                        <p style="color: var(--text-muted); font-size: 11px;"><?php echo htmlspecialchars($row['client_role']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if(!empty($row['company_name'])): ?>
                                <div style="font-size: 12px; color: var(--text-main); display: flex; align-items: center; gap: 5px;">
                                    <?php if(!empty($row['company_logo'])): ?>
                                    <img src="../uploads/testimonials/<?php echo htmlspecialchars($row['company_logo']); ?>" style="max-height: <?php echo !empty($row['company_logo_size']) ? (int)$row['company_logo_size'] : 40; ?>px;">
                                    <?php elseif(!empty($row['company_icon'])): ?>
                                    <i class="<?php echo htmlspecialchars($row['company_icon']); ?>" style="color: #EAB136; font-size: <?php echo !empty($row['company_logo_size']) ? (int)$row['company_logo_size'] : 40; ?>px;"></i> 
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($row['company_name']); ?>
                                </div>
                                <?php else: ?>
                                <span style="color: var(--text-muted); font-size:12px;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-style: italic; color: var(--text-muted);">
                                "<?php echo htmlspecialchars(substr($row['content'], 0, 50)); ?>..."
                            </td>
                            <td><span class="pill progress"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn-icon edit"><i class="fa-solid fa-pen"></i></a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this testimonial?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan="5" style="text-align: center;">No testimonials found.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD TESTIMONIAL VIEW WITH LIVE PREVIEW -->
        <div class="tab-content <?php echo isset($_GET['edit']) ? 'active' : ''; ?>" id="view-add-testimonial">
            <div class="form-grid" style="gap: 30px;">
                
                <!-- Left: Form -->
                <div class="form-panel">
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            
                            <?php if($edit_data): ?>
                                <input type="hidden" name="testimonial_id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label">Client Name</label>
                                <input type="text" name="client_name" id="input_client_name" class="form-control" placeholder="e.g., Sarah Mitchell" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['client_name']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Client Role / Designation</label>
                                <input type="text" name="client_role" id="input_client_role" class="form-control" placeholder="e.g., Home Renovation Client or CEO" value="<?php echo $edit_data ? htmlspecialchars($edit_data['client_role']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Company / Brand Name</label>
                                <input type="text" name="company_name" id="input_company_name" class="form-control" placeholder="e.g., Logoipsum" value="<?php echo $edit_data ? htmlspecialchars($edit_data['company_name']) : ''; ?>">
                            </div>

                            <div class="form-group" style="display:flex; gap:15px; align-items:flex-start;">
                                <div style="flex:1;">
                                    <label class="form-label">Brand Logo (Image file)</label>
                                    <input type="file" name="company_logo" id="input_company_logo" class="form-control" accept="image/*" onchange="previewCompanyLogo(this)">
                                    <small style="color: var(--text-muted); font-size: 11px;">Upload an image (png, jpg, svg) or use an icon below.</small>
                                </div>
                                <div style="width:100px;">
                                    <label class="form-label">Logo Size</label>
                                    <div style="display:flex; align-items:center; gap:5px;">
                                        <input type="number" name="company_logo_size" id="input_company_logo_size" class="form-control" min="10" max="150" value="<?php echo $edit_data ? (int)$edit_data['company_logo_size'] : 40; ?>" style="padding-right:0;">
                                        <span style="font-size:12px; color:var(--text-muted);">px</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Company Icon (FontAwesome fallback)</label>
                                <input type="text" name="company_icon" id="input_company_icon" class="form-control" placeholder="e.g., fa-solid fa-gem" value="<?php echo $edit_data ? htmlspecialchars($edit_data['company_icon']) : 'fa-solid fa-gem'; ?>">
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Client Image / Avatar</label>
                                <div class="file-upload-box" onclick="document.getElementById('input_client_image').click()" style="padding: 30px; cursor:pointer;">
                                    <i class="fa-solid fa-user-circle"></i>
                                    <h4>Click to upload client photo</h4>
                                    <p style="font-size:12px; margin-top:5px;">Square image recommended (e.g., 150x150px)</p>
                                    <input type="file" name="client_image" id="input_client_image" accept="image/*" style="display:none;" onchange="previewImage(this)">
                                </div>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Testimonial Content</label>
                                <textarea name="content" id="input_content" class="form-control" placeholder="Write the client's review here..." style="min-height: 150px;" required><?php echo $edit_data ? htmlspecialchars($edit_data['content']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="Published" <?php echo ($edit_data && $edit_data['status'] == 'Published') ? 'selected' : ''; ?>>Published</option>
                                    <option value="Draft" <?php echo ($edit_data && $edit_data['status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>

                            <div class="form-group full" style="display:flex; justify-content:flex-end; gap:15px; margin-top:10px;">
                                <?php if($edit_data): ?>
                                    <a href="testimonials.php" class="btn-primary" style="background:#f1f5f9; color:var(--text-main); text-decoration: none; padding: 10px 20px; border-radius: 5px;">Cancel</a>
                                <?php else: ?>
                                    <button type="button" class="btn-primary" style="background:#f1f5f9; color:var(--text-main);" onclick="switchTab('manage')">Cancel</button>
                                <?php endif; ?>
                                <button type="submit" name="save_testimonial" class="btn-primary"><?php echo $edit_data ? 'Update Testimonial' : 'Save Testimonial'; ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right: Live Preview -->
                <div class="preview-panel" style="background: #f8fafc; padding: 30px; border-radius: 12px; border: 1px dashed #cbd5e1; display:flex; flex-direction:column; align-items:center;">
                    <h3 style="font-size: 16px; margin-bottom: 30px; color: var(--text-muted);">Live Preview</h3>
                    
                    <!-- Preview Card matching frontend HTML -->
                    <div style="width: 100%; max-width: 400px; background: white; border-radius: 10px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                            <?php 
                                $preview_img_src = 'https://ui-avatars.com/api/?name=C+N';
                                if($edit_data && !empty($edit_data['client_image'])) {
                                    $preview_img_src = '../uploads/testimonials/' . htmlspecialchars($edit_data['client_image']);
                                } else if($edit_data && !empty($edit_data['client_name'])) {
                                    $preview_img_src = 'https://ui-avatars.com/api/?name='.urlencode($edit_data['client_name']);
                                }
                            ?>
                            <img id="preview_img" src="<?php echo $preview_img_src; ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <span id="preview_company_wrapper" style="font-weight: 700; color: var(--text-dark); font-size: 16px; display: flex; align-items: center; gap: 5px; <?php echo ($edit_data && (empty($edit_data['company_name']) && empty($edit_data['company_logo']))) ? 'display:none;' : ''; ?>">
                                <?php 
                                    $logo_display = 'none';
                                    $icon_display = 'inline-block';
                                    $logo_src = '';
                                    if($edit_data && !empty($edit_data['company_logo'])) {
                                        $logo_display = 'inline-block';
                                        $icon_display = 'none';
                                        $logo_src = '../uploads/testimonials/' . htmlspecialchars($edit_data['company_logo']);
                                    }
                                ?>
                                <img id="preview_company_img" src="<?php echo $logo_src; ?>" style="max-height: <?php echo $edit_data ? (int)$edit_data['company_logo_size'] : 40; ?>px; display: <?php echo $logo_display; ?>;">
                                <i id="preview_icon" class="<?php echo $edit_data ? htmlspecialchars($edit_data['company_icon']) : 'fa-solid fa-gem'; ?>" style="color: #EAB136; display: <?php echo $icon_display; ?>; font-size: <?php echo $edit_data ? (int)$edit_data['company_logo_size'] : 40; ?>px;"></i> 
                                <span id="preview_company"><?php echo $edit_data ? htmlspecialchars($edit_data['company_name']) : ''; ?></span>
                            </span>
                        </div>
                        <div style="font-size: 45px; color: #334C40; line-height: 1; margin-bottom: 20px;">
                            <i class="fa-solid fa-quote-left"></i>
                        </div>
                        <p id="preview_content" style="color: #64748b; line-height: 1.6; margin-bottom: 30px; font-size: 15px;">
                            <?php echo $edit_data ? htmlspecialchars($edit_data['content']) : 'The testimonial content will appear here...'; ?>
                        </p>
                        <div style="border-left: 3px solid #334C40; padding-left: 15px;">
                            <h4 id="preview_name" style="color: var(--text-dark); margin: 0 0 3px 0; font-size: 16px; text-transform: uppercase;"><?php echo $edit_data ? htmlspecialchars($edit_data['client_name']) : 'CLIENT NAME'; ?></h4>
                            <p id="preview_role" style="color: #64748b; font-size: 13px; margin: 0;"><?php echo $edit_data ? htmlspecialchars($edit_data['client_role']) : 'Role / Title'; ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- MANAGE STATS VIEW -->
        <div class="tab-content" id="view-manage-stats">
            <div style="background: #F6F6F6; padding: 40px; border-radius: 10px; position: relative;">
                <style>
                    #view-manage-stats .testimonial-section { padding: 0 !important; background: transparent !important; }
                    #view-manage-stats .testi-top-area,
                    #view-manage-stats .testi-slider-wrapper,
                    #view-manage-stats .testi-pagination,
                    #view-manage-stats button.testi-nav-arrow { display: none !important; }
                    #view-manage-stats [contenteditable="true"] { outline: 1px dashed rgba(0,0,0,0.2); cursor: text; min-width: 30px; display: inline-block; padding: 2px; }
                    #view-manage-stats [contenteditable="true"]:focus { outline: 2px solid var(--accent-color); background: rgba(255,255,255,0.5); }
                </style>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="margin: 0; color: var(--text-dark);">Live Edit Testimonial Stats</h3>
                    <button class="btn-primary" onclick="saveTestiStatsLive(this)">Save Stats</button>
                </div>
                
                <?php include '../includes/components/testimonial.php'; ?>
                
            </div>
        </div>

    </div>

    <div class="admin-footer">
        <div>© 2025 Kalp Interior Design Studio. All rights reserved.</div>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById('view-' + tabId).classList.add('active');
}

// Live Preview JS logic
document.addEventListener('DOMContentLoaded', () => {
    const inputs = {
        name: document.getElementById('input_client_name'),
        role: document.getElementById('input_client_role'),
        company: document.getElementById('input_company_name'),
        icon: document.getElementById('input_company_icon'),
        content: document.getElementById('input_content'),
        logo_size: document.getElementById('input_company_logo_size')
    };

    const preview = {
        name: document.getElementById('preview_name'),
        role: document.getElementById('preview_role'),
        company: document.getElementById('preview_company'),
        icon: document.getElementById('preview_icon'),
        company_img: document.getElementById('preview_company_img'),
        content: document.getElementById('preview_content'),
        img: document.getElementById('preview_img')
    };

    function updatePreview() {
        preview.name.innerText = inputs.name.value || 'CLIENT NAME';
        preview.role.innerText = inputs.role.value || 'Role / Title';
        preview.company.innerText = inputs.company.value || '';
        
        const hasNewLogo = document.getElementById('input_company_logo').files[0];
        const hasExistingLogo = preview.company_img.getAttribute('src') && preview.company_img.getAttribute('src') !== '';
        const hasLogo = hasNewLogo || hasExistingLogo;
        
        const hasCompany = inputs.company.value.trim() !== '';

        if (!hasLogo && !hasCompany) {
            document.getElementById('preview_company_wrapper').style.display = 'none';
        } else {
            document.getElementById('preview_company_wrapper').style.display = 'flex';
        }

        // Only fallback to icon if there is genuinely no logo (new or existing)
        if (!hasLogo) {
            preview.icon.className = inputs.icon.value || 'fa-solid fa-gem';
            preview.icon.style.display = 'inline-block';
            preview.company_img.style.display = 'none';
        } else {
            // Ensure logo is visible (it might have been hidden previously)
            preview.icon.style.display = 'none';
            preview.company_img.style.display = 'inline-block';
        }

        preview.content.innerText = inputs.content.value || 'The testimonial content will appear here...';
        
        if(!inputs.name.value && !document.getElementById('input_client_image').files[0]) {
             preview.img.src = 'https://ui-avatars.com/api/?name=C+N';
        } else if(inputs.name.value && !document.getElementById('input_client_image').files[0]) {
             preview.img.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(inputs.name.value);
        }
        
        let logoSize = parseInt(inputs.logo_size.value) || 40;
        if (logoSize > 150) {
            logoSize = 150;
            inputs.logo_size.value = 150;
        } else if (logoSize < 10) {
            // Don't auto-correct while they might be typing a single digit like '1', but cap the minimum applied size
            // logoSize = Math.max(10, logoSize); 
            // Wait, to allow typing, just limit the applied size, not the input value immediately
        }
        
        preview.company_img.style.maxHeight = Math.max(10, logoSize) + 'px';
        preview.icon.style.fontSize = Math.max(10, logoSize) + 'px';
    }

    Object.values(inputs).forEach(input => {
        if(input) input.addEventListener('input', updatePreview);
    });

    // Initial update on load to handle pre-filled edit data
    if (document.getElementById('input_client_name').value) {
        // Only run on inputs so we don't clear the server-side generated image logic
        preview.name.innerText = inputs.name.value;
        preview.role.innerText = inputs.role.value;
        preview.company.innerText = inputs.company.value;
        preview.content.innerText = inputs.content.value;
    }
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewCompanyLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_company_img').src = e.target.result;
            document.getElementById('preview_company_img').style.display = 'inline-block';
            document.getElementById('preview_icon').style.display = 'none';
            document.getElementById('preview_company_wrapper').style.display = 'flex';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        document.getElementById('preview_company_img').style.display = 'none';
        document.getElementById('preview_icon').style.display = 'inline-block';
        if(document.getElementById('input_company_name').value.trim() === '') {
            document.getElementById('preview_company_wrapper').style.display = 'none';
        }
    }
}

function saveTestiStatsLive(btn) {
    const originalText = btn.innerText;
    btn.innerText = 'Saving...';
    
    const formData = new FormData();
    formData.append('save_content', '1');
    formData.append('content[testi_stat1_value]', document.querySelector('.testi-stat1-val').innerText);
    formData.append('content[testi_stat1_label]', document.querySelector('.testi-stat1-label').innerText);
    formData.append('content[testi_stat2_value]', document.querySelector('.testi-stat2-val').innerText);
    formData.append('content[testi_stat2_label]', document.querySelector('.testi-stat2-label').innerText);
    formData.append('content[testi_stat3_value]', document.querySelector('.testi-stat3-val').innerText);
    formData.append('content[testi_stat3_label]', document.querySelector('.testi-stat3-label').innerText);
    formData.append('content[testi_stat4_value]', document.querySelector('.testi-stat4-val').innerText);
    formData.append('content[testi_stat4_label]', document.querySelector('.testi-stat4-label').innerText);
    
    fetch('manage_page.php?page=testimonial_stats', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(() => {
        btn.innerText = 'Saved!';
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerText = 'Error';
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'Calculator Settings';
$currentPage = 'calculator';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

// Handle POST actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    // Breakdowns & PDF Template save
    // Breakdowns & General Settings save
    if ($action == 'save_breakdowns') {
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            $stmt = $conn->prepare("INSERT INTO calculator_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            foreach ($_POST['settings'] as $key => $value) {
                $stmt->bind_param("sss", $key, $value, $value);
                $stmt->execute();
            }
            $stmt->close();
            $success_msg = "Settings updated successfully!";
        }
        
        // Handle PDF upload
        if (isset($_FILES['appended_pdf']) && $_FILES['appended_pdf']['error'] == 0) {
            $allowed = ['pdf'];
            $filename = $_FILES['appended_pdf']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), $allowed)) {
                $newname = 'appended_brochure_' . time() . '.pdf';
                $destination = '../assets/images/' . $newname;
                if (move_uploaded_file($_FILES['appended_pdf']['tmp_name'], $destination)) {
                    $pdf_url = 'assets/images/' . $newname;
                    $conn->query("INSERT INTO calculator_settings (setting_key, setting_value) VALUES ('appended_pdf_path', '$pdf_url') ON DUPLICATE KEY UPDATE setting_value='$pdf_url'");
                    $success_msg = "Settings and PDF uploaded successfully!";
                }
            } else {
                $error_msg = "Invalid file type. Only PDF is allowed.";
            }
        }
    }
    
    // Add/Edit Category
    if ($action == 'save_category') {
        $id = (int)($_POST['id'] ?? 0);
        $name = $conn->real_escape_string($_POST['name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $icon = $conn->real_escape_string($_POST['icon']);
        
        if ($id > 0) {
            $conn->query("UPDATE calc_categories SET name='$name', slug='$slug', icon='$icon' WHERE id=$id");
        } else {
            $conn->query("INSERT INTO calc_categories (name, slug, icon) VALUES ('$name', '$slug', '$icon')");
        }
        $success_msg = "Category saved!";
    }
    
    // Add/Edit Type
    if ($action == 'save_type') {
        $id = (int)($_POST['id'] ?? 0);
        $category_slug = $conn->real_escape_string($_POST['category_slug']);
        $name = $conn->real_escape_string($_POST['name']);
        $sqft = (int)$_POST['sqft'];
        $icon = $conn->real_escape_string($_POST['icon']);
        
        if ($id > 0) {
            $conn->query("UPDATE calc_types SET category_slug='$category_slug', name='$name', sqft=$sqft, icon='$icon' WHERE id=$id");
        } else {
            $conn->query("INSERT INTO calc_types (category_slug, name, icon, sqft) VALUES ('$category_slug', '$name', '$icon', $sqft)");
        }
        $success_msg = "Type saved!";
    }
    
    // Add/Edit Design Style
    if ($action == 'save_style') {
        $id = (int)($_POST['id'] ?? 0);
        $category_slug = $conn->real_escape_string($_POST['category_slug']);
        $name = $conn->real_escape_string($_POST['name']);
        $percent = (float)$_POST['percent_value'];
        $icon = $conn->real_escape_string($_POST['icon']);
        $position = (int)($_POST['position'] ?? 0);
        
        if ($id > 0) {
            $conn->query("UPDATE calc_styles SET category_slug='$category_slug', name='$name', percent_value=$percent, icon='$icon', position=$position WHERE id=$id");
        } else {
            $conn->query("INSERT INTO calc_styles (category_slug, name, icon, percent_value, position) VALUES ('$category_slug', '$name', '$icon', $percent, $position)");
        }
        $success_msg = "Style saved!";
    }
    
    // Add/Edit Package
    if ($action == 'save_package') {
        $id = (int)($_POST['id'] ?? 0);
        $category_slug = $conn->real_escape_string($_POST['category_slug']);
        $name = $conn->real_escape_string($_POST['name']);
        $price = (int)$_POST['price_per_sqft'];
        $pdf_specs = $conn->real_escape_string($_POST['pdf_specs']);
        
        if ($id > 0) {
            $conn->query("UPDATE calc_packages SET category_slug='$category_slug', name='$name', price_per_sqft=$price, pdf_specs='$pdf_specs' WHERE id=$id");
        } else {
            // New packages won't have the SVG out of the box unless added, but keep it simple
            $conn->query("INSERT INTO calc_packages (category_slug, name, price_per_sqft, pdf_specs) VALUES ('$category_slug', '$name', $price, '$pdf_specs')");
        }
        $success_msg = "Package saved!";
    }
    
    // Add/Edit Addon
    if ($action == 'save_addon') {
        $id = (int)($_POST['id'] ?? 0);
        $category_slug = $conn->real_escape_string($_POST['category_slug'] ?? 'residential');
        $name = $conn->real_escape_string($_POST['name']);
        $percent = (float)$_POST['percent_value'];
        
        if ($id > 0) {
            $conn->query("UPDATE calc_addons SET category_slug='$category_slug', name='$name', percent_value=$percent WHERE id=$id");
        } else {
            $conn->query("INSERT INTO calc_addons (category_slug, name, percent_value) VALUES ('$category_slug', '$name', $percent)");
        }
        $success_msg = "Add-on saved!";
    }
    
    // Add/Edit Breakdown
    if ($action == 'save_breakdown') {
        $id = (int)($_POST['id'] ?? 0);
        $category_slug = $conn->real_escape_string($_POST['category_slug'] ?? 'residential');
        $name = $conn->real_escape_string($_POST['name']);
        $percent = (float)$_POST['percent_value'];
        $position = (int)($_POST['position'] ?? 0);
        
        if ($id > 0) {
            $conn->query("UPDATE calc_breakdowns SET category_slug='$category_slug', name='$name', percent_value=$percent, position=$position WHERE id=$id");
        } else {
            $conn->query("INSERT INTO calc_breakdowns (category_slug, name, percent_value, position) VALUES ('$category_slug', '$name', $percent, $position)");
        }
        $success_msg = "Cost breakdown item saved!";
    }
}

// Handle GET Actions (Delete)
if (isset($_GET['delete'])) {
    $table = $conn->real_escape_string($_GET['delete']);
    $id = (int)$_GET['id'];
    if (in_array($table, ['calc_categories', 'calc_types', 'calc_styles', 'calc_packages', 'calc_addons', 'calc_breakdowns'])) {
        $conn->query("DELETE FROM $table WHERE id=$id");
        $success_msg = "Item deleted successfully!";
    }
}

// Fetch all data
$categories = $conn->query("SELECT * FROM calc_categories ORDER BY id ASC");
$types = $conn->query("SELECT * FROM calc_types ORDER BY category_slug ASC, id ASC");
$styles = $conn->query("SELECT * FROM calc_styles ORDER BY position ASC, percent_value ASC");
$packages = $conn->query("SELECT * FROM calc_packages ORDER BY category_slug ASC, price_per_sqft ASC");
$addons = $conn->query("SELECT * FROM calc_addons ORDER BY id ASC");
$breakdowns = $conn->query("SELECT * FROM calc_breakdowns ORDER BY category_slug ASC, position ASC");

$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM calculator_settings");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<style>
.tabs-header { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid var(--border-color); }
.tab-btn { background: none; border: none; padding: 10px 20px; cursor: pointer; font-size: 14px; font-weight: 500; color: var(--text-muted); border-bottom: 2px solid transparent; margin-bottom: -2px; }
.tab-btn.active { color: var(--primary-color); border-bottom-color: var(--accent-color); }
.tab-content { display: none; }
.tab-content.active { display: block; }
.form-panel { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.form-group { margin-bottom: 15px; }
.form-label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 13px; }
.form-control { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 5px; }
.btn-primary { background: var(--accent-color); color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
.admin-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; min-width: 600px; }
.table-wrapper { overflow-x: auto; }
.admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); }
.admin-table th { background: #f8fafc; font-weight: 600; font-size: 12px; text-transform: uppercase; color: var(--text-muted); }
.action-btns { display: flex; gap: 10px; }
.btn-icon { color: var(--text-muted); transition: 0.3s; }
.btn-icon:hover { color: var(--accent-color); }
.btn-icon.delete:hover { color: #ef4444; }

/* Responsive Grids */
.calc-grid-1x2 { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
.calc-grid-1x1-sm { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 10px; }
.calc-grid-1x1-lg { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }

@media (max-width: 768px) {
    .calc-grid-1x2, .calc-grid-1x1-sm, .calc-grid-1x1-lg { grid-template-columns: 1fr; }
}
</style>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <?php if(!empty($success_msg)): ?>
            <div id="successToast" style="position: fixed; top: 20px; right: 20px; background: #2ECC71; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; font-weight: 500; display: flex; align-items: center; gap: 10px; transition: opacity 0.3s ease;">
                <i class="fa-solid fa-circle-check"></i>
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
            <script>
                setTimeout(function() {
                    const toast = document.getElementById('successToast');
                    if (toast) {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3000); // Show for 3 seconds
            </script>
        <?php endif; ?>
        
        <div class="page-header">
            <h1>Calculator Live Editor</h1>
            <p style="color:var(--text-muted); margin-top:5px;">Manage all variables and pricing for the cost calculator.</p>
        </div>

        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab(event, 'tab-categories')">Categories</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-types')">Types & SqFt</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-styles')">Design Styles</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-packages')">Packages & PDF</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-addons')">Add-ons</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-breakdowns')">Cost Breakdown</button>
            <button class="tab-btn" onclick="openTab(event, 'tab-pdf-template')">PDF Template</button>
        </div>

        <!-- CATEGORIES TAB -->
        <div id="tab-categories" class="tab-content active">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Category</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_category">
                        <input type="hidden" name="id" id="cat_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="cat_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon Class (e.g. fa-solid fa-house)</label>
                            <div class="icon-input-group">
                                <div class="icon-input-preview">
                                    <i id="cat_icon_preview" class="fa-solid fa-house"></i>
                                </div>
                                <input type="text" name="icon" id="cat_icon" required class="form-control" onkeyup="updatePreview(this, 'cat_icon_preview', 'fa-house')">
                                <button type="button" class="btn-select" onclick="openIconPicker('cat_icon', 'cat_icon_preview')">Select</button>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Save Category</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('cat')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Icon</th><th>Name</th><th>Slug</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><i class="<?php echo $row['icon']; ?>"></i></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><?php echo $row['slug']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editCat(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['icon']); ?>')"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_categories&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- TYPES TAB -->
        <div id="tab-types" class="tab-content">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Specific Type</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_type">
                        <input type="hidden" name="id" id="type_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_slug" id="type_cat" class="form-control" required>
                                <?php 
                                if (isset($categories) && $categories->num_rows > 0) {
                                    mysqli_data_seek($categories, 0);
                                    while($cat = $categories->fetch_assoc()):
                                        if($cat['slug'] == 'kitchen' || $cat['slug'] == 'modular-kitchen') continue;
                                ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Name (e.g. 1 BHK)</label>
                            <input type="text" name="name" id="type_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Default SqFt Area</label>
                            <input type="number" name="sqft" id="type_sqft" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon Class</label>
                            <div class="icon-input-group">
                                <div class="icon-input-preview">
                                    <i id="type_icon_preview" class="fa-solid fa-bed"></i>
                                </div>
                                <input type="text" name="icon" id="type_icon" required class="form-control" onkeyup="updatePreview(this, 'type_icon_preview', 'fa-bed')">
                                <button type="button" class="btn-select" onclick="openIconPicker('type_icon', 'type_icon_preview')">Select</button>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Save Type</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('type')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Category</th><th>Icon</th><th>Name</th><th>SqFt</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $types->fetch_assoc()): ?>
                            <tr>
                                <td><span style="text-transform:uppercase; font-size:10px; padding:3px 8px; background:#eee; border-radius:10px;"><?php echo $row['category_slug']; ?></span></td>
                                <td><i class="<?php echo $row['icon']; ?>"></i></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><?php echo $row['sqft']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editType(<?php echo $row['id']; ?>, '<?php echo addslashes($row['category_slug']); ?>', '<?php echo addslashes($row['name']); ?>', <?php echo $row['sqft']; ?>, '<?php echo addslashes($row['icon']); ?>')"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_types&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- STYLES TAB -->
        <div id="tab-styles" class="tab-content">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Design Style</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_style">
                        <input type="hidden" name="id" id="style_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_slug" id="style_cat" class="form-control" required>
                                <?php 
                                if (isset($categories) && $categories->num_rows > 0) {
                                    mysqli_data_seek($categories, 0);
                                    while($cat = $categories->fetch_assoc()):
                                        if($cat['slug'] == 'kitchen' || $cat['slug'] == 'modular-kitchen') continue;
                                ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Style Name</label>
                            <input type="text" name="name" id="style_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Position (Sort Order)</label>
                            <input type="number" name="position" id="style_pos" required class="form-control" value="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price Adjustment % (e.g. -8 for -8%, 15 for +15%)</label>
                            <input type="number" step="0.01" name="percent_value" id="style_pct" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon Class</label>
                            <div class="icon-input-group">
                                <div class="icon-input-preview">
                                    <i id="style_icon_preview" class="fa-solid fa-couch"></i>
                                </div>
                                <input type="text" name="icon" id="style_icon" required class="form-control" onkeyup="updatePreview(this, 'style_icon_preview', 'fa-couch')">
                                <button type="button" class="btn-select" onclick="openIconPicker('style_icon', 'style_icon_preview')">Select</button>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Save Style</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('style')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Pos</th><th>Category</th><th>Icon</th><th>Style Name</th><th>Adjustment %</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $styles->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['position']; ?></td>
                                <td><span style="text-transform:uppercase; font-size:10px; padding:3px 8px; background:#eee; border-radius:10px;"><?php echo $row['category_slug'] ?? 'residential'; ?></span></td>
                                <td><i class="<?php echo $row['icon']; ?>"></i></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td style="color: <?php echo $row['percent_value'] > 0 ? '#166534' : ($row['percent_value'] < 0 ? '#b91c1c' : '#333'); ?>">
                                    <?php echo ($row['percent_value'] > 0 ? '+' : '') . $row['percent_value'] . '%'; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editStyle(<?php echo $row['id']; ?>, '<?php echo addslashes($row['category_slug'] ?? 'residential'); ?>', '<?php echo addslashes($row['name']); ?>', <?php echo $row['percent_value']; ?>, '<?php echo addslashes($row['icon']); ?>', <?php echo $row['position']; ?>)"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_styles&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PACKAGES TAB -->
        <div id="tab-packages" class="tab-content">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Package</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_package">
                        <input type="hidden" name="id" id="pkg_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Category Area</label>
                            <select name="category_slug" id="pkg_cat" class="form-control" required>
                                <?php 
                                if (isset($categories) && $categories->num_rows > 0) {
                                    mysqli_data_seek($categories, 0);
                                    while($cat = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Package Name (e.g. Premium)</label>
                            <input type="text" name="name" id="pkg_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price per SqFt / RFT (₹)</label>
                            <input type="number" name="price_per_sqft" id="pkg_price" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">PDF Specifications Format (HTML)</label>
                            <textarea name="pdf_specs" id="pkg_pdf" class="form-control" style="font-family:monospace; font-size:12px; min-height:200px;"></textarea>
                            <p style="font-size:11px; color:#666; margin-top:5px;">This HTML is dynamically injected into the downloaded PDF quote.</p>
                        </div>
                        <button type="submit" class="btn-primary">Save Package</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('pkg')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Category</th><th>Package</th><th>Price</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $packages->fetch_assoc()): ?>
                            <tr>
                                <td><span style="text-transform:uppercase; font-size:10px; padding:3px 8px; background:#eee; border-radius:10px;"><?php echo $row['category_slug']; ?></span></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td>₹<?php echo number_format($row['price_per_sqft']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editPackage(<?php echo $row['id']; ?>, '<?php echo addslashes($row['category_slug']); ?>', '<?php echo addslashes($row['name']); ?>', <?php echo $row['price_per_sqft']; ?>, `<?php echo htmlspecialchars($row['pdf_specs']); ?>`)"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_packages&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ADDONS TAB -->
        <div id="tab-addons" class="tab-content">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Add-on</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_addon">
                        <input type="hidden" name="id" id="addon_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_slug" id="addon_cat" class="form-control" required>
                                <?php 
                                if (isset($categories) && $categories->num_rows > 0) {
                                    mysqli_data_seek($categories, 0);
                                    while($cat = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Add-on Name (e.g. Civil work)</label>
                            <input type="text" name="name" id="addon_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Value (% or ₹/sq ft)</label>
                            <input type="number" step="0.01" name="percent_value" id="addon_pct" required class="form-control">
                        </div>
                        <button type="submit" class="btn-primary">Save Add-on</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('addon')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Category</th><th>Name</th><th>Value</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $addons->fetch_assoc()): ?>
                            <tr>
                                <td><span style="text-transform:uppercase; font-size:10px; padding:3px 8px; background:#eee; border-radius:10px;"><?php echo $row['category_slug'] ?? 'residential'; ?></span></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td>
                                    <?php 
                                    if (($row['category_slug'] ?? '') == 'modular-kitchen') {
                                        echo '₹' . $row['percent_value'] . '/sq ft';
                                    } else {
                                        echo '+' . $row['percent_value'] . '%';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editAddon(<?php echo $row['id']; ?>, '<?php echo addslashes($row['category_slug'] ?? 'residential'); ?>', '<?php echo addslashes($row['name']); ?>', <?php echo $row['percent_value']; ?>)"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_addons&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- COST BREAKDOWN TAB -->
        <div id="tab-breakdowns" class="tab-content">
            <div class="calc-grid-1x2">
                <div class="form-panel" style="align-self: start;">
                    <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">Add/Edit Cost Breakdown Percentages</h3>
                    <p style="font-size:12px; color:#666; margin-bottom: 20px;">Ensure these total up to 100% per category. The system will calculate specific costs out of the subtotal based on these percentages.</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_breakdown">
                        <input type="hidden" name="id" id="bd_id" value="0">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category_slug" id="bd_cat" class="form-control" required>
                                <?php 
                                if (isset($categories) && $categories->num_rows > 0) {
                                    mysqli_data_seek($categories, 0);
                                    while($cat = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Item Name (e.g. Wardrobes & Storage)</label>
                            <input type="text" name="name" id="bd_name" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Percentage (%)</label>
                            <input type="number" step="0.01" name="percent_value" id="bd_pct" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Position (Sort Order)</label>
                            <input type="number" name="position" id="bd_pos" required class="form-control" value="0">
                        </div>
                        <button type="submit" class="btn-primary">Save Item</button>
                        <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="resetForm('bd')">Reset</button>
                    </form>
                </div>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead><tr><th>Pos</th><th>Category</th><th>Item Name</th><th>Percentage</th><th>Action</th></tr></thead>
                        <tbody>
                            <?php while($row = $breakdowns->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['position']; ?></td>
                                <td><span style="text-transform:uppercase; font-size:10px; padding:3px 8px; background:#eee; border-radius:10px;"><?php echo $row['category_slug']; ?></span></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo $row['percent_value']; ?>%</td>
                                <td>
                                    <div class="action-btns">
                                        <a href="javascript:void(0)" class="btn-icon" onclick="editBreakdown(<?php echo $row['id']; ?>, '<?php echo addslashes($row['category_slug']); ?>', '<?php echo addslashes($row['name']); ?>', <?php echo $row['percent_value']; ?>, <?php echo $row['position']; ?>)"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete=calc_breakdowns&id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PDF TEMPLATE TAB -->
        <div id="tab-pdf-template" class="tab-content">
            <div class="form-panel">
                <h3 style="margin-top:0; margin-bottom:20px; font-size:16px;">PDF Export Template HTML</h3>
                <p style="font-size:12px; color:#666; margin-bottom: 20px;">Edit the raw HTML of the PDF Quotation here. Be careful, as invalid HTML can break the PDF generation! The IDs in the HTML are used by the system to inject values dynamically.</p>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_breakdowns">
                    <div class="calc-grid-1x1-lg">
                        <div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label class="form-label" style="margin-bottom:10px;">Append Static PDF (Optional brochure/T&C)</label>
                                <?php if(!empty($settings['appended_pdf_path'])): ?>
                                    <div style="margin-bottom: 10px; font-size: 12px; color: green;">
                                        Currently appended: <a href="../<?php echo $settings['appended_pdf_path']; ?>" target="_blank">View File</a>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="appended_pdf" accept="application/pdf" class="form-control" style="padding: 5px;">
                                <small style="color: #666; font-size:11px;">Upload a standard PDF to automatically attach it to the end of user estimates.</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">PDF Export Template HTML</label>
                                <textarea name="settings[pdf_template_html]" id="pdf_template_editor" class="form-control" style="height: 400px; font-family: monospace; font-size: 11px;"><?php echo htmlspecialchars($settings['pdf_template_html'] ?? ''); ?></textarea>
                            </div>
                            <div style="margin-top: 20px;">
                                <button type="submit" class="btn-primary">Save Settings & Upload</button>
                                <button type="button" class="btn-primary" style="background:#ccc; color:#333; margin-left:10px;" onclick="document.getElementById('pdf_preview_frame').srcdoc = document.getElementById('pdf_template_editor').value.replace('display: none;', 'display: block;');">Refresh Preview</button>
                            </div>
                        </div>
                        <div>
                            <h4 style="margin-bottom:10px; font-size: 13px;">Live Preview</h4>
                            <div style="border: 1px solid var(--border-color); border-radius: 5px; overflow: hidden; height: 500px; background: #eee; position: relative;">
                                <?php 
                                    $preview_html = $settings['pdf_template_html'] ?? '';
                                    // Remove display: none so the preview is visible
                                    $preview_html = str_replace('display: none;', 'display: block;', $preview_html);
                                ?>
                                <iframe id="pdf_preview_frame" style="width: 794px; height: 1123px; border: none; transform: scale(0.44); transform-origin: top left; position: absolute; top: 0; left: 0;" srcdoc="<?php echo htmlspecialchars($preview_html); ?>"></iframe>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        </div>

<!-- Icon Picker Component -->
<style>
.icon-input-group {
    display: flex;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    overflow: hidden;
}
.icon-input-preview {
    width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border-right: 1px solid var(--border-color);
    color: var(--accent-color);
    font-size: 18px;
}
.icon-input-group input {
    flex-grow: 1;
    border: none !important;
    border-radius: 0 !important;
    outline: none;
    box-shadow: none !important;
}
.icon-input-group .btn-select {
    background: #6c757d;
    color: #fff;
    border: none;
    padding: 0 15px;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    transition: background 0.2s;
}
.icon-input-group .btn-select:hover {
    background: #5a6268;
}
.icon-picker-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    width: 350px;
    z-index: 1000;
    display: none;
    flex-direction: column;
}
.icon-picker-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 999;
    display: none;
}
.icon-picker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}
.icon-picker-header h4 {
    margin: 0;
    font-size: 13px;
    font-weight: 700;
    color: #333;
    text-transform: uppercase;
}
.icon-picker-header .close-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #888;
    line-height: 1;
}
.icon-picker-body {
    padding: 15px;
}
.icon-picker-body input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    outline: none;
    font-size: 13px;
}
.icon-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    max-height: 250px;
    overflow-y: auto;
}
.icon-btn {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 5px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    color: #555;
    transition: all 0.2s;
}
.icon-btn:hover {
    background: #f8fafc;
    border-color: #ddd;
    color: var(--accent-color);
}
</style>

<div class="icon-picker-overlay" id="iconPickerOverlay" onclick="closeIconPicker()"></div>
<div class="icon-picker-modal" id="iconPickerModal">
    <div class="icon-picker-header">
        <h4>Select An Icon</h4>
        <button type="button" class="close-btn" onclick="closeIconPicker()">&times;</button>
    </div>
    <div class="icon-picker-body">
        <input type="text" id="iconSearchInput" placeholder="Search icons..." onkeyup="filterIcons()">
        <div class="icon-grid" id="iconGrid"></div>
    </div>
</div>

    </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active");
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    document.getElementById(tabName).style.display = "block";
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

function resetForm(prefix) {
    document.getElementById(prefix+'_id').value = 0;
    if(prefix == 'cat') {
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_icon').value = '';
        document.getElementById('cat_icon_preview').className = 'fa-solid fa-house';
    }
    if(prefix == 'type') {
        document.getElementById('type_name').value = '';
        document.getElementById('type_sqft').value = '';
        document.getElementById('type_icon').value = '';
        document.getElementById('type_icon_preview').className = 'fa-solid fa-bed';
    }
    if(prefix == 'style') {
        document.getElementById('style_name').value = '';
        document.getElementById('style_pct').value = '';
        document.getElementById('style_pos').value = '0';
        document.getElementById('style_icon').value = '';
        document.getElementById('style_cat').value = 'residential';
        document.getElementById('style_icon_preview').className = 'fa-solid fa-couch';
    }
    if(prefix == 'pkg') {
        document.getElementById('pkg_name').value = '';
        document.getElementById('pkg_price').value = '';
        document.getElementById('pkg_pdf').value = '';
    }
    if(prefix == 'addon') {
        document.getElementById('addon_name').value = '';
        document.getElementById('addon_pct').value = '';
        document.getElementById('addon_cat').value = 'residential';
    }
    if(prefix == 'bd') {
        document.getElementById('bd_name').value = '';
        document.getElementById('bd_pct').value = '';
        document.getElementById('bd_pos').value = '0';
        document.getElementById('bd_cat').value = 'residential';
    }
}

function editCat(id, name, icon) {
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_name').value = name;
    document.getElementById('cat_icon').value = icon;
    let v = (icon || '').trim();
    document.getElementById('cat_icon_preview').className = v ? (v.includes('fa-') ? v : 'fa-solid fa-' + v) : 'fa-solid fa-house';
}
function editType(id, cat, name, sqft, icon) {
    document.getElementById('type_id').value = id;
    document.getElementById('type_cat').value = cat;
    document.getElementById('type_name').value = name;
    document.getElementById('type_sqft').value = sqft;
    document.getElementById('type_icon').value = icon;
    let v = (icon || '').trim();
    document.getElementById('type_icon_preview').className = v ? (v.includes('fa-') ? v : 'fa-solid fa-' + v) : 'fa-solid fa-bed';
}
function editStyle(id, cat, name, pct, icon, pos) {
    document.getElementById('style_id').value = id;
    document.getElementById('style_cat').value = cat;
    document.getElementById('style_name').value = name;
    document.getElementById('style_pct').value = pct;
    document.getElementById('style_pos').value = pos;
    document.getElementById('style_icon').value = icon;
    let v = (icon || '').trim();
    document.getElementById('style_icon_preview').className = v ? (v.includes('fa-') ? v : 'fa-solid fa-' + v) : 'fa-solid fa-couch';
}
function editPackage(id, cat, name, price, pdf) {
    document.getElementById('pkg_id').value = id;
    document.getElementById('pkg_cat').value = cat;
    document.getElementById('pkg_name').value = name;
    document.getElementById('pkg_price').value = price;
    document.getElementById('pkg_pdf').value = pdf;
}
function editAddon(id, cat, name, pct) {
    document.getElementById('addon_id').value = id;
    document.getElementById('addon_cat').value = cat;
    document.getElementById('addon_name').value = name;
    document.getElementById('addon_pct').value = pct;
}
function editBreakdown(id, cat, name, pct, pos) {
    document.getElementById('bd_id').value = id;
    document.getElementById('bd_cat').value = cat;
    document.getElementById('bd_name').value = name;
    document.getElementById('bd_pct').value = pct;
    document.getElementById('bd_pos').value = pos;
}

// Icon Picker Logic
const faIcons = [
    'fa-house', 'fa-couch', 'fa-bed', 'fa-chair', 'fa-bath', 'fa-kitchen-set', 
    'fa-building', 'fa-paint-roller', 'fa-hammer', 'fa-ruler-combined', 'fa-lightbulb', 
    'fa-tree', 'fa-sink', 'fa-door-open', 'fa-window-maximize', 'fa-plug', 'fa-fan', 
    'fa-tv', 'fa-utensils', 'fa-mug-hot', 'fa-cube', 'fa-table-cells', 'fa-wifi', 
    'fa-tools', 'fa-palette', 'fa-key', 'fa-briefcase', 'fa-house-chimney', 'fa-building-user',
    'fa-star', 'fa-heart', 'fa-check', 'fa-plus', 'fa-user', 'fa-image', 'fa-book', 
    'fa-chess-rook', 'fa-crown', 'fa-gem', 'fa-trophy', 'fa-clock'
];

let targetIconInput = '';
let targetIconPreview = '';

function updatePreview(inputElem, previewId, defaultIcon) {
    let v = inputElem.value.trim();
    document.getElementById(previewId).className = v ? (v.includes('fa-') ? v : 'fa-solid fa-' + v) : 'fa-solid ' + defaultIcon;
}

function openIconPicker(inputId, previewId) {
    targetIconInput = inputId;
    targetIconPreview = previewId;
    document.getElementById('iconPickerOverlay').style.display = 'block';
    document.getElementById('iconPickerModal').style.display = 'flex';
    document.getElementById('iconSearchInput').value = '';
    renderIcons(faIcons);
}

function closeIconPicker() {
    document.getElementById('iconPickerOverlay').style.display = 'none';
    document.getElementById('iconPickerModal').style.display = 'none';
}

function renderIcons(icons) {
    const grid = document.getElementById('iconGrid');
    grid.innerHTML = '';
    icons.forEach(icon => {
        let fullClass = 'fa-solid ' + icon;
        let btn = document.createElement('div');
        btn.className = 'icon-btn';
        btn.innerHTML = `<i class="${fullClass}"></i>`;
        btn.title = icon;
        btn.onclick = function() {
            document.getElementById(targetIconInput).value = fullClass;
            document.getElementById(targetIconPreview).className = fullClass;
            closeIconPicker();
        };
        grid.appendChild(btn);
    });
}

function filterIcons() {
    let term = document.getElementById('iconSearchInput').value.toLowerCase();
    let filtered = faIcons.filter(icon => icon.includes(term));
    renderIcons(filtered);
}

// Button loading states for Save and Delete
document.addEventListener('DOMContentLoaded', function() {
    // For Save buttons
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.style.backgroundColor = '#2ECC71';
                btn.style.color = '#fff';
                btn.style.border = 'none';
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            }
        });
    });

    // For Delete buttons
    document.querySelectorAll('.btn-icon.delete').forEach(btn => {
        // Remove the inline onclick to manage it via JS
        btn.removeAttribute('onclick');
        btn.addEventListener('click', function(e) {
            if(confirm('Are you sure you want to delete this item?')) {
                this.style.backgroundColor = '#2ECC71';
                this.style.color = '#fff';
                this.style.border = 'none';
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                this.style.pointerEvents = 'none';
            } else {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>

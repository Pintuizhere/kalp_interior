<?php
$pageTitle = 'Categories';
$currentPage = 'categories';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

// Handle POST request to add a new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    $icon = isset($_POST['icon']) ? $conn->real_escape_string($_POST['icon']) : '';

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $name, $slug, $icon);
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

// Handle GET request to delete a category
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success_msg = "Category deleted successfully!";
        } else {
            $error_msg = "Error deleting category.";
        }
        $stmt->close();
    }
}

// Fetch categories
$cat_query = "SELECT * FROM categories ORDER BY order_index ASC, name ASC";
$cat_result = $conn->query($cat_query);
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
            <h1>Categories</h1>
        </div>

        <div class="form-grid-1x2">
            
            <!-- ADD CATEGORY FORM -->
            <div class="table-wrapper" style="align-self: start; padding: 25px; overflow: visible;">
                <h3 style="margin-top:0; margin-bottom: 20px; font-size: 16px;">Add New Category</h3>
                <form action="categories.php" method="POST">
                    <input type="hidden" name="add_category" value="1">
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">The name is how it appears on your site.</p>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display:block; margin-bottom:8px; font-weight:500;">Icon</label>
                        <div style="position: relative; display: block; width: 100%;">
                            <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 5px; background: white; padding: 2px;">
                                <div id="selected-icon-display" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 5px; margin-right: 5px; border: 1px solid #eee;">
                                    <i id="selected-icon-preview" class="fa-solid fa-couch" style="color: #333;"></i>
                                </div>
                                <input type="text" id="category_icon_input" name="icon" value="fa-solid fa-couch" onfocus="toggleIconPicker(true)" oninput="updateIconPreview(this.value)" placeholder="fa-solid fa-couch" style="border: none; outline: none; font-size: 14px; flex-grow: 1; background: transparent; padding: 5px;">
                                <button type="button" onclick="toggleIconPicker()" style="background: #6b7280; color: white; border: none; padding: 8px 15px; border-radius: 5px; font-size: 13px; cursor: pointer; margin-right: 2px;">Select</button>
                            </div>

                            <div id="icon-picker-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 5px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; padding: 15px; z-index: 100;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <strong style="font-size: 12px; color: #333;">SELECT AN ICON</strong>
                                    <button type="button" onclick="toggleIconPicker(false)" style="background: none; border: none; cursor: pointer; color: #888;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <input type="text" id="icon-search" placeholder="Search icons..." oninput="filterIcons()" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 4px; margin-bottom: 15px; font-size: 13px; box-sizing: border-box;">
                                <div id="icon-grid" style="display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto;">
                                    <!-- Icons populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 20px; width:100%; justify-content:center;">
                        Add New Category
                    </button>
                </form>
            </div>

            <!-- MANAGE CATEGORIES TABLE -->
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($cat_result && $cat_result->num_rows > 0): ?>
                            <?php while($cat = $cat_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if(!empty($cat['icon'])): ?><i class="<?php echo htmlspecialchars($cat['icon']); ?>" style="margin-right: 8px; color: var(--accent-color);"></i><?php endif; ?>
                                    <strong style="color:var(--text-dark);"><?php echo htmlspecialchars($cat['name']); ?></strong>
                                </td>
                                <td style="color:var(--text-muted);"><?php echo htmlspecialchars($cat['slug']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="categories.php?delete_id=<?php echo $cat['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this category?');"><i class="fa-solid fa-trash"></i></a>
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

<script>
    const iconList = [
        'fa-solid fa-couch', 'fa-solid fa-house', 'fa-regular fa-building', 'fa-solid fa-chair', 
        'fa-solid fa-pen-ruler', 'fa-solid fa-mug-hot', 'fa-solid fa-cube', 'fa-solid fa-border-all', 
        'fa-solid fa-wifi', 'fa-solid fa-lightbulb', 'fa-solid fa-paint-roller', 'fa-solid fa-hammer', 
        'fa-solid fa-screwdriver-wrench', 'fa-solid fa-palette', 'fa-solid fa-bed', 'fa-solid fa-kitchen-set',
        'fa-solid fa-briefcase', 'fa-solid fa-house-user', 'fa-solid fa-building-user'
    ];

    function toggleIconPicker(show) {
        const picker = document.getElementById('icon-picker-dropdown');
        if (show === undefined) {
            picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        } else {
            picker.style.display = show ? 'block' : 'none';
        }
        if (picker.style.display === 'block') {
            populateIconGrid();
        }
    }

    function populateIconGrid(filter = '') {
        const grid = document.getElementById('icon-grid');
        grid.innerHTML = '';
        iconList.filter(icon => icon.includes(filter)).forEach(icon => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'icon-grid-item';
            btn.style.cssText = 'width: 35px; height: 35px; border: 1px solid #eee; border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;';
            btn.innerHTML = `<i class="${icon}" style="color: #555;"></i>`;
            btn.onclick = () => selectIcon(icon);
            btn.onmouseover = () => btn.style.borderColor = 'var(--accent-color)';
            btn.onmouseout = () => btn.style.borderColor = '#eee';
            grid.appendChild(btn);
        });
    }

    function filterIcons() {
        populateIconGrid(document.getElementById('icon-search').value.toLowerCase());
    }

    function selectIcon(iconClass) {
        document.getElementById('category_icon_input').value = iconClass;
        updateIconPreview(iconClass);
        toggleIconPicker(false);
    }

    function updateIconPreview(iconClass) {
        document.getElementById('selected-icon-preview').className = iconClass;
    }

    // Close picker when clicking outside
    document.addEventListener('click', function(event) {
        const picker = document.getElementById('icon-picker-dropdown');
        const inputContainer = document.getElementById('category_icon_input').parentElement;
        
        if (picker.style.display === 'block' && 
            !picker.contains(event.target) && 
            !inputContainer.contains(event.target)) {
            toggleIconPicker(false);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

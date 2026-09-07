<?php
require_once 'config/db.php';

// --- AJAX Handlers for inline categories ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    if ($_POST['ajax_action'] === 'add_category') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $icon = $conn->real_escape_string($_POST['icon'] ?? '');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, icon) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $slug, $icon);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'id' => $conn->insert_id, 'name' => $name, 'icon' => $icon]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Name cannot be empty']);
        }
    } elseif ($_POST['ajax_action'] === 'delete_category') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        if (!empty($name)) {
            $stmt = $conn->prepare("DELETE FROM categories WHERE name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $name);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
            }
        }
    } elseif ($_POST['ajax_action'] === 'update_category_order') {
        $order_data = json_decode($_POST['order_data'], true);
        if (is_array($order_data)) {
            $stmt = $conn->prepare("UPDATE categories SET order_index = ? WHERE name = ?");
            if ($stmt) {
                foreach ($order_data as $index => $name) {
                    $stmt->bind_param("is", $index, $name);
                    $stmt->execute();
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $conn->error]);
            }
        }
    } elseif ($_POST['ajax_action'] === 'add_gallery_category') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $icon = $conn->real_escape_string($_POST['icon'] ?? '');
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO gallery_categories (name, slug, icon) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $slug, $icon);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'id' => $conn->insert_id, 'name' => $name, 'icon' => $icon]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Name cannot be empty']);
        }
    } elseif ($_POST['ajax_action'] === 'delete_gallery_category') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        if (!empty($name)) {
            $stmt = $conn->prepare("DELETE FROM gallery_categories WHERE name = ?");
            if ($stmt) {
                $stmt->bind_param("s", $name);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => $stmt->error]);
                }
            }
        }
    } elseif ($_POST['ajax_action'] === 'update_gallery_category_order') {
        $order_data = json_decode($_POST['order_data'], true);
        if (is_array($order_data)) {
            $stmt = $conn->prepare("UPDATE gallery_categories SET order_index = ? WHERE name = ?");
            if ($stmt) {
                foreach ($order_data as $index => $name) {
                    $stmt->bind_param("is", $index, $name);
                    $stmt->execute();
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $conn->error]);
            }
        }
    }
    exit;
}
// --- End AJAX Handlers ---
$pageTitle = 'Projects';
$currentPage = 'projects';
$success_msg = '';
$error_msg = '';

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Try to delete the associated cover image file
    $res = $conn->query("SELECT cover_image FROM projects WHERE id = $delete_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['cover_image'])) {
            $file = '../' . ltrim($row['cover_image'], '/');
            if (file_exists($file)) @unlink($file);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            header("Location: projects.php?success=delete");
            exit;
        } else {
            $error_msg = "Error deleting project.";
        }
        $stmt->close();
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'delete') {
    $success_msg = "Project deleted successfully!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $slug = $conn->real_escape_string($_POST['slug'] ?? '');
    if(empty($slug) && !empty($title)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    $project_id = $_POST['project_id'] ?? '';
    
    // Ensure unique slug
    $original_slug = $slug;
    $slug_count = 1;
    while(true) {
        if (!empty($project_id)) {
            $check_stmt = $conn->prepare("SELECT id FROM projects WHERE slug = ? AND id != ?");
            if($check_stmt) $check_stmt->bind_param("si", $slug, $project_id);
        } else {
            $check_stmt = $conn->prepare("SELECT id FROM projects WHERE slug = ?");
            if($check_stmt) $check_stmt->bind_param("s", $slug);
        }
        
        if($check_stmt) {
            $check_stmt->execute();
            $check_res = $check_stmt->get_result();
            if($check_res->num_rows > 0) {
                $slug = $original_slug . '-' . $slug_count;
                $slug_count++;
            } else {
                $check_stmt->close();
                break;
            }
            $check_stmt->close();
        } else {
            break;
        }
    }
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $category = $conn->real_escape_string($_POST['project_category'] ?? '');
    $property_type = $conn->real_escape_string($_POST['property_type'] ?? '');
    $area = $conn->real_escape_string($_POST['area'] ?? '');
    $year = $conn->real_escape_string($_POST['year'] ?? '');
    $style = $conn->real_escape_string($_POST['style'] ?? '');
    $scope = $conn->real_escape_string($_POST['scope'] ?? '');
    $short_desc = $conn->real_escape_string($_POST['short_desc'] ?? '');
    $about_title = $conn->real_escape_string($_POST['about_title'] ?? '');
    $about_subtitle = $conn->real_escape_string($_POST['about_subtitle'] ?? '');
    $long_desc = $conn->real_escape_string($_POST['long_desc'] ?? '');
    $meta_title = $conn->real_escape_string($_POST['meta_title'] ?? '');
    $meta_keywords = $conn->real_escape_string($_POST['meta_keywords'] ?? '');
    $meta_description = $conn->real_escape_string($_POST['meta_description'] ?? '');

    if (!empty($project_id)) {
        // UPDATE existing project
        $stmt = $conn->prepare("UPDATE projects SET title=?, slug=?, location=?, category=?, property_type=?, area=?, year=?, style=?, scope=?, short_desc=?, about_title=?, about_subtitle=?, long_desc=?, meta_title=?, meta_keywords=?, meta_description=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("ssssssssssssssssi", $title, $slug, $location, $category, $property_type, $area, $year, $style, $scope, $short_desc, $about_title, $about_subtitle, $long_desc, $meta_title, $meta_keywords, $meta_description, $project_id);
            if ($stmt->execute()) {
                $success_msg = "Project updated successfully!";
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
    } else {
        // INSERT new project
        $stmt = $conn->prepare("INSERT INTO projects (title, slug, location, category, property_type, area, year, style, scope, short_desc, about_title, about_subtitle, long_desc, meta_title, meta_keywords, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssssssssssssss", $title, $slug, $location, $category, $property_type, $area, $year, $style, $scope, $short_desc, $about_title, $about_subtitle, $long_desc, $meta_title, $meta_keywords, $meta_description);
            if ($stmt->execute()) {
                $success_msg = "Project added successfully!";
            } else {
                $error_msg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Database error: " . $conn->error;
        }
    }
}

// Fetch Categories for the form
$cat_query = "SELECT * FROM categories ORDER BY order_index ASC, name ASC";
$categories = $conn->query($cat_query);

// Fetch Gallery Categories for the form
$gallery_cat_query = "SELECT * FROM gallery_categories ORDER BY order_index ASC, name ASC";
$gallery_categories = $conn->query($gallery_cat_query);
// Fetch Projects for the table
$proj_query = "SELECT * FROM projects ORDER BY created_at DESC";
$projects = $conn->query($proj_query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Manage Projects</h1>
            <button class="btn-primary" onclick="addProject()">
                <i class="fa-solid fa-plus"></i> Add New Project
            </button>
        </div>

        <?php if($success_msg): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $success_msg; ?>
            </div>
            <script>
                // Automatically switch to the correct tab if there was a message
                document.addEventListener('DOMContentLoaded', () => switchTab('manage'));
            </script>
        <?php endif; ?>
        <?php if($error_msg): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>



        <!-- MANAGE PROJECTS VIEW -->
        <div class="tab-content active" id="view-manage">
            <div class="table-wrapper">
                <div class="table-toolbar">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Search projects...">
                    </div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($projects && $projects->num_rows > 0): ?>
                            <?php while($proj = $projects->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="project-item">
                                        <img src="<?php echo !empty($proj['cover_image']) ? htmlspecialchars($proj['cover_image']) : 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=100&h=80&fit=crop'; ?>" class="project-thumb" alt="Project">
                                        <div class="user-details">
                                            <h4><?php echo htmlspecialchars($proj['title'] ?: 'Untitled Project'); ?></h4>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($proj['location'] ?: 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($proj['category'] ?: 'N/A'); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="#" class="btn-icon" onclick="editProject(<?php echo $proj['id']; ?>); return false;"><i class="fa-solid fa-pen"></i></a>
                                        <a href="?delete_id=<?php echo $proj['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this project?');"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 20px;">No projects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD PROJECT VIEW (INLINE EDITOR) -->
        <div class="tab-content" id="view-add-project">
            
            <div class="live-editor-toolbar">
                <div>
                    <h3 id="live-editor-title" style="margin: 0; display: flex; align-items: center; gap: 10px; color: var(--text-dark);">
                        <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent-color);"></i> Live Add Project
                    </h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 13px;">Click directly on any text or image below to edit it.</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button class="btn-primary" style="background:#f1f5f9; color:var(--text-main);" onclick="switchTab('manage')">Cancel</button>
                    <button class="btn-primary" onclick="saveLiveProject()" id="btn-save-project">
                        <i class="fa-solid fa-floppy-disk"></i> Save Project
                    </button>
                </div>
            </div>
            
            <form id="live-add-form" action="projects.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="project_id" id="project_id" value="">
                
                <!-- Category and SEO Section -->
                <div class="project-meta-section">
                    <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--text-dark); font-size: 16px;">Project Category</h4>
                    
                    <style>
                        .category-selector {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 10px;
                            margin-bottom: 20px;
                            align-items: center;
                        }
                        .category-selector label {
                            cursor: pointer;
                            display: inline-block;
                            position: relative;
                        }
                        .category-selector input[type="radio"] {
                            display: none;
                        }
                        .category-selector .tag-span {
                            background-color: white; 
                            border: 1px solid rgba(0,0,0,0.1); 
                            padding: 10px 20px; 
                            border-radius: 25px;
                            font-size: 14px;
                            color: var(--text-dark);
                            display: flex;
                            align-items: center;
                            transition: all 0.3s ease;
                        }
                        .category-selector .tag-span i.cat-icon {
                            margin-right: 8px;
                            color: var(--text-muted);
                        }
                        .category-selector input[type="radio"]:checked + .tag-span {
                            background-color: #fcebdc;
                            border-color: #fcebdc;
                            font-weight: 600;
                        }
                        .category-selector input[type="radio"]:checked + .tag-span i.cat-icon {
                            color: var(--text-dark);
                        }
                        .del-cat-btn {
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            background: #ef4444;
                            color: white;
                            border: none;
                            border-radius: 50%;
                            width: 18px;
                            height: 18px;
                            font-size: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            opacity: 0;
                            transition: opacity 0.2s;
                        }
                        .category-selector label:hover .del-cat-btn {
                            opacity: 1;
                        }
                        .draggable-cat:hover .tag-span, .draggable-gallery-cat:hover .tag-span {
                            border-color: #000;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                        .add-cat-btn {
                            background: #fff;
                            border: 1px dashed rgba(0,0,0,0.2);
                            padding: 10px 15px;
                            border-radius: 25px;
                            cursor: pointer;
                            font-size: 14px;
                            color: var(--text-muted);
                            display: flex;
                            align-items: center;
                            transition: all 0.2s;
                        }
                        .add-cat-btn:hover {
                            border-color: var(--accent-color);
                            color: var(--text-dark);
                        }
                        #inline-add-cat-form {
                            display: none;
                            align-items: center;
                            gap: 10px;
                            background: #f9f9f9;
                            padding: 5px 10px;
                            border-radius: 25px;
                            border: 1px solid #ddd;
                            cursor: grab;
                        }
                        .category-selector label:active {
                            cursor: grabbing;
                        }
                    </style>
                    
                    <div class="category-selector" id="cat-selector-container">
                        <?php 
                        if ($categories && $categories->num_rows > 0): 
                            $first = true;
                            while($cat = $categories->fetch_assoc()):
                        ?>
                        <label data-catname="<?php echo htmlspecialchars($cat['name']); ?>" draggable="true" class="draggable-cat">
                            <input type="radio" name="project_category" value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $first ? 'checked' : ''; ?>>
                            <span class="tag-span">
                                <?php if(!empty($cat['icon'])): ?><i class="cat-icon <?php echo htmlspecialchars($cat['icon']); ?>"></i><?php endif; ?>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </span>
                            <button type="button" class="del-cat-btn" onclick="deleteInlineCategory('<?php echo htmlspecialchars(addslashes($cat['name'])); ?>')"><i class="fa-solid fa-xmark"></i></button>
                        </label>
                        <?php 
                                $first = false;
                            endwhile;
                            $categories->data_seek(0);
                        endif; 
                        ?>
                        
                        <div id="inline-add-cat-form">
                            <input type="text" id="inline_cat_name" placeholder="Category Name" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 15px; font-size: 13px; width: 120px;" required>
                            
                            <div style="position: relative; display: inline-block;">
                                <div style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 15px; background: white; padding: 2px;">
                                    <div id="selected-icon-display" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px; margin-right: 5px; border: 1px solid #eee;">
                                        <i id="selected-icon-preview" class="fa-solid fa-couch" style="color: #333;"></i>
                                    </div>
                                    <input type="text" id="inline_cat_icon" value="fa-solid fa-couch" onfocus="toggleIconPicker(true)" oninput="updateIconPreview(this.value)" placeholder="fa-solid fa-couch" style="border: none; outline: none; font-size: 13px; width: 130px; background: transparent; padding: 5px;">
                                    <button type="button" onclick="toggleIconPicker()" style="background: #6b7280; color: white; border: none; padding: 6px 12px; border-radius: 10px; font-size: 12px; cursor: pointer; margin-right: 2px;">Select</button>
                                </div>

                                <div id="icon-picker-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 5px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 320px; padding: 15px; z-index: 100;">
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

                            <button type="button" onclick="saveInlineCategory()" style="background: var(--accent-color); color: var(--text-dark); border: none; padding: 8px 20px; border-radius: 15px; font-size: 13px; cursor: pointer;">Save</button>
                            <button type="button" onclick="toggleInlineAddCat()" style="background: none; border: none; color: #888; cursor: pointer; padding: 5px;"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <button type="button" class="add-cat-btn" id="add-cat-btn-trigger" onclick="toggleInlineAddCat()">
                            <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Add Category
                        </button>
                    </div>

                    <!-- Gallery Categories section -->
                    <label style="margin-bottom: 10px; display: block; font-weight: 600; font-size: 14px; color: var(--text-dark); margin-top: 20px;">Gallery Categories (For Filter Buttons)</label>
                    <div class="category-selector" id="gallery-cat-selector-container">
                        <?php 
                        if ($gallery_categories && $gallery_categories->num_rows > 0): 
                            while($cat = $gallery_categories->fetch_assoc()):
                        ?>
                        <label data-catname="<?php echo htmlspecialchars($cat['name']); ?>" class="gallery-cat draggable-gallery-cat" draggable="true" style="cursor: grab;">
                            <span class="tag-span" style="background-color: white; font-weight: normal; border-color: rgba(0,0,0,0.1); color: var(--text-dark);">
                                <?php if(!empty($cat['icon'])): ?><i class="cat-icon <?php echo htmlspecialchars($cat['icon']); ?>" style="color: var(--text-muted); margin-right: 8px;"></i><?php endif; ?>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </span>
                            <button type="button" class="del-cat-btn" onclick="deleteInlineGalleryCategory('<?php echo htmlspecialchars(addslashes($cat['name'])); ?>')"><i class="fa-solid fa-xmark"></i></button>
                        </label>
                        <?php 
                            endwhile;
                            $gallery_categories->data_seek(0);
                        endif; 
                        ?>
                        
                        <div id="inline-add-gallery-cat-form" style="display: none; align-items: center; gap: 10px; background: #f9f9f9; padding: 5px 10px; border-radius: 25px; border: 1px solid #ddd;">
                            <input type="text" id="inline_gallery_cat_name" placeholder="Category Name" style="padding: 5px 10px; border: 1px solid #ddd; border-radius: 15px; font-size: 13px; width: 120px;" required>
                            
                            <div style="position: relative; display: inline-block;">
                                <div style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 15px; background: white; padding: 2px;">
                                    <div style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px; margin-right: 5px; border: 1px solid #eee;">
                                        <i id="selected-gallery-icon-preview" class="fa-solid fa-couch" style="color: #333;"></i>
                                    </div>
                                    <input type="text" id="inline_gallery_cat_icon" value="fa-solid fa-couch" onfocus="toggleGalleryIconPicker(true)" oninput="updateGalleryIconPreview(this.value)" placeholder="fa-solid fa-couch" style="border: none; outline: none; font-size: 13px; width: 130px; background: transparent; padding: 5px;">
                                    <button type="button" onclick="toggleGalleryIconPicker()" style="background: #6b7280; color: white; border: none; padding: 6px 12px; border-radius: 10px; font-size: 12px; cursor: pointer; margin-right: 2px;">Select</button>
                                </div>

                                <div id="gallery-icon-picker-dropdown" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 5px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 320px; padding: 15px; z-index: 100;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <strong style="font-size: 12px; color: #333;">SELECT AN ICON</strong>
                                        <button type="button" onclick="toggleGalleryIconPicker(false)" style="background: none; border: none; cursor: pointer; color: #888;"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <input type="text" id="gallery-icon-search" placeholder="Search icons..." oninput="filterGalleryIcons()" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 4px; margin-bottom: 15px; font-size: 13px; box-sizing: border-box;">
                                    <div id="gallery-icon-grid" style="display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto;">
                                        <!-- Icons populated by JS -->
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="saveInlineGalleryCategory()" style="background: var(--accent-color); color: var(--text-dark); border: none; padding: 8px 20px; border-radius: 15px; font-size: 13px; cursor: pointer;">Save</button>
                            <button type="button" onclick="toggleInlineAddGalleryCat()" style="background: none; border: none; color: #888; cursor: pointer; padding: 5px;"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                        <button type="button" class="add-cat-btn" id="add-gallery-cat-btn-trigger" onclick="toggleInlineAddGalleryCat()">
                            <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Add Category
                        </button>
                    </div>

                    <!-- Custom Delete Confirmation Modal -->
                    <div id="delete-cat-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 25px; border-radius: 10px; width: 350px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center;">
                            <div style="font-size: 40px; color: #ef4444; margin-bottom: 15px;"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <h3 style="margin-top: 0; color: var(--text-dark);">Delete Category?</h3>
                            <p style="color: var(--text-muted); margin-bottom: 25px;">Are you sure you want to delete "<strong id="delete-cat-name-display"></strong>"? This action cannot be undone.</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <button type="button" onclick="closeDeleteCatModal()" style="padding: 10px 20px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer; color: var(--text-dark); font-weight: 500;">Cancel</button>
                                <button type="button" id="confirm-delete-cat-btn" style="padding: 10px 20px; border: none; background: #ef4444; color: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Delete</button>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Delete Confirmation Modal (Gallery Categories) -->
                    <div id="delete-gallery-cat-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
                        <div style="background: white; padding: 25px; border-radius: 10px; width: 350px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: center;">
                            <div style="font-size: 40px; color: #ef4444; margin-bottom: 15px;"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <h3 style="margin-top: 0; color: var(--text-dark);">Delete Gallery Category?</h3>
                            <p style="color: var(--text-muted); margin-bottom: 25px;">Are you sure you want to delete "<strong id="delete-gallery-cat-name-display"></strong>"? This action cannot be undone.</p>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <button type="button" onclick="closeDeleteGalleryCatModal()" style="padding: 10px 20px; border: 1px solid #ddd; background: white; border-radius: 6px; cursor: pointer; color: var(--text-dark); font-weight: 500;">Cancel</button>
                                <button type="button" id="confirm-delete-gallery-cat-btn" style="padding: 10px 20px; border: none; background: #ef4444; color: white; border-radius: 6px; cursor: pointer; font-weight: 500;">Delete</button>
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

                        function initIconPicker() {
                            const grid = document.getElementById('icon-grid');
                            grid.innerHTML = '';
                            iconList.forEach(icon => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'icon-picker-item';
                                btn.style.cssText = 'width: 36px; height: 36px; border: 1px solid #eee; border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; color: #333; transition: all 0.2s;';
                                btn.innerHTML = `<i class="${icon}"></i>`;
                                btn.onclick = () => selectIcon(icon);
                                btn.onmouseover = () => btn.style.borderColor = '#aaa';
                                btn.onmouseout = () => btn.style.borderColor = '#eee';
                                grid.appendChild(btn);
                            });
                        }

                        function toggleIconPicker(forceState) {
                            const picker = document.getElementById('icon-picker-dropdown');
                            if (forceState === true) {
                                picker.style.display = 'block';
                            } else if (forceState === false) {
                                picker.style.display = 'none';
                            } else {
                                picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                            }
                            if(picker.style.display === 'block') {
                                initIconPicker();
                                document.getElementById('icon-search').value = '';
                                filterIcons();
                            }
                        }

                        function updateIconPreview(val) {
                            document.getElementById('selected-icon-preview').className = val;
                        }

                        function selectIcon(icon) {
                            document.getElementById('inline_cat_icon').value = icon;
                            updateIconPreview(icon);
                            toggleIconPicker(false);
                        }

                        function filterIcons() {
                            const term = document.getElementById('icon-search').value.toLowerCase();
                            const items = document.querySelectorAll('.icon-picker-item');
                            items.forEach(item => {
                                const iconClass = item.querySelector('i').className.toLowerCase();
                                if (iconClass.includes(term)) {
                                    item.style.display = 'flex';
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                        }

                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(event) {
                            const dropdown = document.getElementById('icon-picker-dropdown');
                            const inputContainer = document.getElementById('inline-add-cat-form');
                            if (dropdown && dropdown.style.display === 'block' && !inputContainer.contains(event.target)) {
                                toggleIconPicker(false);
                            }
                        });

                        function toggleInlineAddCat() {
                            const form = document.getElementById('inline-add-cat-form');
                            const btn = document.getElementById('add-cat-btn-trigger');
                            if (form.style.display === 'flex') {
                                form.style.display = 'none';
                                btn.style.display = 'flex';
                            } else {
                                form.style.display = 'flex';
                                btn.style.display = 'none';
                            }
                        }

                        function saveInlineCategory() {
                            const name = document.getElementById('inline_cat_name').value;
                            const icon = document.getElementById('inline_cat_icon').value;
                            if(!name) { alert('Please enter a category name'); return; }

                            const formData = new FormData();
                            formData.append('ajax_action', 'add_category');
                            formData.append('name', name);
                            formData.append('icon', icon);

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const label = document.createElement('label');
                                    label.setAttribute('data-catname', data.name);
                                    label.innerHTML = `
                                        <input type="radio" name="project_category" value="${data.name}" checked>
                                        <span class="tag-span">
                                            ${data.icon ? '<i class="cat-icon ' + data.icon + '"></i> ' : ''}${data.name}
                                        </span>
                                        <button type="button" class="del-cat-btn" onclick="deleteInlineCategory('${data.name}')"><i class="fa-solid fa-xmark"></i></button>
                                    `;
                                    const container = document.getElementById('cat-selector-container');
                                    container.insertBefore(label, document.getElementById('inline-add-cat-form'));
                                    
                                    document.getElementById('inline_cat_name').value = '';
                                    toggleInlineAddCat();
                                } else {
                                    alert('Error adding category: ' + (data.error || 'Unknown error'));
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Failed to save category. Check console.');
                            });
                        }

                        let categoryToDelete = '';

                        function deleteInlineCategory(name) {
                            categoryToDelete = name;
                            document.getElementById('delete-cat-name-display').innerText = name;
                            document.getElementById('delete-cat-modal').style.display = 'flex';
                        }
                        
                        function closeDeleteCatModal() {
                            document.getElementById('delete-cat-modal').style.display = 'none';
                            categoryToDelete = '';
                        }
                        
                        document.getElementById('confirm-delete-cat-btn').addEventListener('click', function() {
                            if(!categoryToDelete) return;
                            
                            const formData = new FormData();
                            formData.append('ajax_action', 'delete_category');
                            formData.append('name', categoryToDelete);

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const label = document.querySelector('label[data-catname="' + categoryToDelete + '"]');
                                    if(label) label.remove();
                                    closeDeleteCatModal();
                                } else {
                                    alert('Error deleting category: ' + (data.error || 'Unknown error'));
                                    closeDeleteCatModal();
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                closeDeleteCatModal();
                            });
                        });

                        // --- Gallery Categories JS ---
                        function toggleGalleryIconPicker(forceState) {
                            const picker = document.getElementById('gallery-icon-picker-dropdown');
                            if (forceState === true) {
                                picker.style.display = 'block';
                            } else if (forceState === false) {
                                picker.style.display = 'none';
                            } else {
                                picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                            }
                            if(picker.style.display === 'block') {
                                const grid = document.getElementById('gallery-icon-grid');
                                grid.innerHTML = '';
                                iconList.forEach(icon => {
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'gallery-icon-picker-item';
                                    btn.style.cssText = 'width: 36px; height: 36px; border: 1px solid #eee; border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; color: #333; transition: all 0.2s;';
                                    btn.innerHTML = `<i class="${icon}"></i>`;
                                    btn.onclick = () => selectGalleryIcon(icon);
                                    btn.onmouseover = () => btn.style.borderColor = '#aaa';
                                    btn.onmouseout = () => btn.style.borderColor = '#eee';
                                    grid.appendChild(btn);
                                });
                                document.getElementById('gallery-icon-search').value = '';
                                filterGalleryIcons();
                            }
                        }

                        function updateGalleryIconPreview(val) {
                            document.getElementById('selected-gallery-icon-preview').className = val;
                        }

                        function selectGalleryIcon(icon) {
                            document.getElementById('inline_gallery_cat_icon').value = icon;
                            updateGalleryIconPreview(icon);
                            toggleGalleryIconPicker(false);
                        }

                        function filterGalleryIcons() {
                            const term = document.getElementById('gallery-icon-search').value.toLowerCase();
                            const items = document.querySelectorAll('.gallery-icon-picker-item');
                            items.forEach(item => {
                                const iconClass = item.querySelector('i').className.toLowerCase();
                                if(iconClass.includes(term)) {
                                    item.style.display = 'flex';
                                } else {
                                    item.style.display = 'none';
                                }
                            });
                        }

                        document.addEventListener('click', function(e) {
                            if(!e.target.closest('#inline-add-gallery-cat-form')) {
                                toggleGalleryIconPicker(false);
                            }
                        });

                        function toggleInlineAddGalleryCat() {
                            const form = document.getElementById('inline-add-gallery-cat-form');
                            const btn = document.getElementById('add-gallery-cat-btn-trigger');
                            if (form.style.display === 'flex') {
                                form.style.display = 'none';
                                btn.style.display = 'flex';
                            } else {
                                form.style.display = 'flex';
                                btn.style.display = 'none';
                            }
                        }

                        function saveInlineGalleryCategory() {
                            const name = document.getElementById('inline_gallery_cat_name').value;
                            const icon = document.getElementById('inline_gallery_cat_icon').value;
                            if(!name) { alert('Please enter a gallery category name'); return; }

                            const formData = new FormData();
                            formData.append('ajax_action', 'add_gallery_category');
                            formData.append('name', name);
                            formData.append('icon', icon);

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const label = document.createElement('label');
                                    label.setAttribute('data-catname', data.name);
                                    label.className = 'gallery-cat draggable-gallery-cat';
                                    label.draggable = true;
                                    label.style.cursor = 'grab';
                                    label.innerHTML = `
                                        <span class="tag-span" style="background-color: white; font-weight: normal; border-color: rgba(0,0,0,0.1); color: var(--text-dark);">
                                            ${data.icon ? '<i class="cat-icon ' + data.icon + '" style="color: var(--text-muted); margin-right: 8px;"></i> ' : ''}${data.name}
                                        </span>
                                        <button type="button" class="del-cat-btn" onclick="deleteInlineGalleryCategory('${data.name}')"><i class="fa-solid fa-xmark"></i></button>
                                    `;
                                    const container = document.getElementById('gallery-cat-selector-container');
                                    container.insertBefore(label, document.getElementById('inline-add-gallery-cat-form'));
                                    
                                    document.getElementById('inline_gallery_cat_name').value = '';
                                    toggleInlineAddGalleryCat();
                                } else {
                                    alert('Error adding gallery category: ' + (data.error || 'Unknown error'));
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Failed to save gallery category. Check console.');
                            });
                        }

                        let galleryCategoryToDelete = '';

                        function deleteInlineGalleryCategory(name) {
                            galleryCategoryToDelete = name;
                            document.getElementById('delete-gallery-cat-name-display').innerText = name;
                            document.getElementById('delete-gallery-cat-modal').style.display = 'flex';
                        }
                        
                        function closeDeleteGalleryCatModal() {
                            document.getElementById('delete-gallery-cat-modal').style.display = 'none';
                            galleryCategoryToDelete = '';
                        }
                        
                        document.getElementById('confirm-delete-gallery-cat-btn').addEventListener('click', function() {
                            if(!galleryCategoryToDelete) return;
                            
                            const formData = new FormData();
                            formData.append('ajax_action', 'delete_gallery_category');
                            formData.append('name', galleryCategoryToDelete);

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    const label = document.querySelector('#gallery-cat-selector-container label[data-catname="' + galleryCategoryToDelete + '"]');
                                    if(label) label.remove();
                                    closeDeleteGalleryCatModal();
                                } else {
                                    alert('Error deleting gallery category: ' + (data.error || 'Unknown error'));
                                    closeDeleteGalleryCatModal();
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                closeDeleteGalleryCatModal();
                            });
                        });
                        // --- End Gallery Categories JS ---

                        // Drag and drop for categories
                        const catContainer = document.getElementById('cat-selector-container');
                        let draggedItem = null;

                        catContainer.addEventListener('dragstart', function(e) {
                            if(e.target.closest('.draggable-cat')) {
                                draggedItem = e.target.closest('.draggable-cat');
                                setTimeout(() => draggedItem.style.opacity = '0.5', 0);
                            }
                        });

                        catContainer.addEventListener('dragend', function(e) {
                            if(draggedItem) {
                                draggedItem.style.opacity = '1';
                                draggedItem = null;
                                saveCategoryOrder();
                            }
                        });

                        catContainer.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            if(!draggedItem) return;
                            const afterElement = getDragAfterElement(catContainer, e.clientX, e.clientY);
                            if (afterElement == null) {
                                // insert before the add form
                                const addForm = document.getElementById('inline-add-cat-form');
                                if(addForm && addForm.parentNode === catContainer) {
                                    catContainer.insertBefore(draggedItem, addForm);
                                } else {
                                    catContainer.appendChild(draggedItem);
                                }
                            } else {
                                catContainer.insertBefore(draggedItem, afterElement);
                            }
                        });

                        function getDragAfterElement(container, x, y) {
                            const draggableElements = [...container.querySelectorAll('.draggable-cat:not([style*="opacity: 0.5"])')];
                            return draggableElements.reduce((closest, child) => {
                                const box = child.getBoundingClientRect();
                                const offsetX = x - box.left - box.width / 2;
                                const offsetY = y - box.top - box.height / 2;
                                if (offsetX < 0 && offsetY < box.height && offsetY > -box.height && offsetX > closest.offset) {
                                    return { offset: offsetX, element: child };
                                } else {
                                    return closest;
                                }
                            }, { offset: Number.NEGATIVE_INFINITY }).element;
                        }

                        function saveCategoryOrder() {
                            const items = [...document.querySelectorAll('.draggable-cat')];
                            const orderData = items.map(item => item.getAttribute('data-catname'));
                            
                            const formData = new FormData();
                            formData.append('ajax_action', 'update_category_order');
                            formData.append('order_data', JSON.stringify(orderData));

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            }).catch(err => console.error(err));
                        }

                        // Drag and drop for gallery categories
                        const galleryCatContainer = document.getElementById('gallery-cat-selector-container');
                        let galleryDraggedItem = null;

                        galleryCatContainer.addEventListener('dragstart', function(e) {
                            if(e.target.closest('.draggable-gallery-cat')) {
                                galleryDraggedItem = e.target.closest('.draggable-gallery-cat');
                                setTimeout(() => galleryDraggedItem.style.opacity = '0.5', 0);
                            }
                        });

                        galleryCatContainer.addEventListener('dragend', function(e) {
                            if(galleryDraggedItem) {
                                galleryDraggedItem.style.opacity = '1';
                                galleryDraggedItem = null;
                                saveGalleryCategoryOrder();
                            }
                        });

                        galleryCatContainer.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            if(!galleryDraggedItem) return;
                            const afterElement = getGalleryDragAfterElement(galleryCatContainer, e.clientX, e.clientY);
                            if (afterElement == null) {
                                // insert before the add form
                                const addForm = document.getElementById('inline-add-gallery-cat-form');
                                if(addForm && addForm.parentNode === galleryCatContainer) {
                                    galleryCatContainer.insertBefore(galleryDraggedItem, addForm);
                                } else {
                                    galleryCatContainer.appendChild(galleryDraggedItem);
                                }
                            } else {
                                galleryCatContainer.insertBefore(galleryDraggedItem, afterElement);
                            }
                        });

                        function getGalleryDragAfterElement(container, x, y) {
                            const draggableElements = [...container.querySelectorAll('.draggable-gallery-cat:not([style*="opacity: 0.5"])')];
                            return draggableElements.reduce((closest, child) => {
                                const box = child.getBoundingClientRect();
                                const offsetX = x - box.left - box.width / 2;
                                const offsetY = y - box.top - box.height / 2;
                                if (offsetX < 0 && offsetY < box.height && offsetY > -box.height && offsetX > closest.offset) {
                                    return { offset: offsetX, element: child };
                                } else {
                                    return closest;
                                }
                            }, { offset: Number.NEGATIVE_INFINITY }).element;
                        }

                        function saveGalleryCategoryOrder() {
                            const items = [...document.querySelectorAll('.draggable-gallery-cat')];
                            const orderData = items.map(item => item.getAttribute('data-catname'));
                            
                            const formData = new FormData();
                            formData.append('ajax_action', 'update_gallery_category_order');
                            formData.append('order_data', JSON.stringify(orderData));

                            fetch('projects.php', {
                                method: 'POST',
                                body: formData
                            }).catch(err => console.error(err));
                        }

                        // Live preview update for category
                        document.addEventListener('change', function(e) {
                            if(e.target && e.target.name === 'project_category') {
                                const catName = e.target.value;
                                const label = e.target.closest('label');
                                const iconEl = label.querySelector('.cat-icon');
                                const iconClass = iconEl ? iconEl.className.replace('cat-icon ', '').trim() : '';
                                
                                try {
                                    const iframe = document.getElementById('editor-iframe');
                                    if(!iframe) return;
                                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                                    const catBadge = doc.querySelector('.hero-tag');
                                    if (catBadge) {
                                        catBadge.innerHTML = (iconClass ? `<i class="${iconClass}" style="margin-right: 5px;"></i> ` : '') + catName;
                                    }
                                    const metaCategory = doc.querySelector('.meta-value:nth-of-type(1)');
                                    if(metaCategory) {
                                        metaCategory.innerText = catName;
                                    }
                                } catch (err) {}
                            }
                        });
                    </script>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                    <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--text-dark); font-size: 16px;">SEO Settings</h4>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr)); gap: 20px; margin-bottom: 15px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Title</label>
                            <input type="text" name="meta_title" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="e.g. Modern Luxury Villa Interior Design">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Keywords</label>
                            <input type="text" name="meta_keywords" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="e.g. interior design, villa, luxury">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px;">Meta Description</label>
                        <textarea name="meta_description" rows="2" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px;" placeholder="Brief description for search engines..."></textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <label style="margin-bottom: 0; font-weight: 700; font-size: 18px; color: var(--text-dark); flex-shrink: 0;">Permalink:</label>
                        <div style="display: flex; align-items: center; gap: 5px; flex: 1; min-width: 250px; flex-wrap: wrap;">
                            <span style="font-size: 16px; color: #666; word-break: break-all;">/project-details.php?slug=</span>
                            <input type="text" name="slug" id="slug" style="flex: 1; min-width: 150px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 16px; font-family: monospace;" placeholder="project-title-slug">
                        </div>
                    </div>
                </div>

                <div class="preview-panel" style="width: 100%; height: 75vh; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <iframe id="editor-iframe" src="editor-project.php" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>

                <!-- Hidden inputs for Image Uploads triggered by iframe and iframe extraction -->
                <input type="file" id="hidden_cover_image" name="cover_image" accept="image/*" style="display:none;">
                <input type="hidden" name="title" id="hdn_title">
                <input type="hidden" name="location" id="hdn_location">
                <input type="hidden" name="category" id="hdn_category">
                <input type="hidden" name="property_type" id="hdn_property_type">
                <input type="hidden" name="area" id="hdn_area">
                <input type="hidden" name="year" id="hdn_year">
                <input type="hidden" name="style" id="hdn_style">
                <input type="hidden" name="scope" id="hdn_scope">
                <input type="hidden" name="short_desc" id="hdn_short_desc">
                <input type="hidden" name="about_title" id="hdn_about_title">
                <input type="hidden" name="about_subtitle" id="hdn_about_subtitle">
                <input type="hidden" name="long_desc" id="hdn_long_desc">
            </form>

        </div>

    <div class="admin-footer">
        <div>© 2025 Kalp Interior Design Studio. All rights reserved.</div>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('view-' + tabId).classList.add('active');
    
    const tabBtn = document.getElementById('tab-' + tabId);
    if(tabBtn) tabBtn.classList.add('active');
}

function addProject() {
    document.getElementById('live-editor-title').innerHTML = '<i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent-color);"></i> Live Add Project';
    document.getElementById('btn-save-project').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Project';
    document.getElementById('project_id').value = '';
    
    // Reset iframe to blank template
    document.getElementById('editor-iframe').src = 'editor-project.php';
    
    switchTab('add-project');
}

function editProject(id) {
    document.getElementById('live-editor-title').innerHTML = '<i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent-color);"></i> Live Edit Project';
    document.getElementById('btn-save-project').innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Update Project';
    document.getElementById('project_id').value = id;
    
    // Pass the project ID to the iframe
    document.getElementById('editor-iframe').src = 'editor-project.php?id=' + id;
    
    switchTab('add-project');
}

document.addEventListener('DOMContentLoaded', () => {
    const iframe = document.getElementById('editor-iframe');
    const fileInput = document.getElementById('hidden_cover_image');
    
    // Listen for image clicks inside the iframe
    iframe.addEventListener('load', () => {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        
        doc.querySelectorAll('.hero-main-img').forEach(img => {
            img.style.cursor = 'pointer';
            img.title = 'Click to change cover image';
            img.addEventListener('click', () => {
                fileInput.click();
            });
        });
    });

    // Handle Cover Image Preview
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    const mainImg = doc.querySelector('.hero-main-img');
                    if (mainImg) mainImg.src = e.target.result;
                } catch (err) {}
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function saveLiveProject() {
    const iframe = document.getElementById('editor-iframe');
    const doc = iframe.contentDocument || iframe.contentWindow.document;
    
    // Extract text values safely
    const getText = (selector) => {
        const el = doc.querySelector(selector);
        return el ? el.innerText.trim() : '';
    };

    const getHtml = (selector) => {
        const el = doc.querySelector(selector);
        return el ? el.innerHTML.trim() : '';
    };

    // Populate hidden inputs
    document.getElementById('hdn_title').value = getText('.project-title').replace(/\n/g, ' ');
    document.getElementById('hdn_location').value = getText('.location-pin').replace('Location', '').trim();
    const getMetaValue = (index) => {
        const els = doc.querySelectorAll('.meta-value');
        return els.length > index ? els[index].innerText.trim() : '';
    };

    document.getElementById('hdn_category').value = getMetaValue(0);
    document.getElementById('hdn_property_type').value = getMetaValue(1);
    document.getElementById('hdn_area').value = getMetaValue(2);
    document.getElementById('hdn_year').value = getMetaValue(3);
    document.getElementById('hdn_style').value = getMetaValue(4);
    document.getElementById('hdn_scope').value = getMetaValue(5);
    
    document.getElementById('hdn_short_desc').value = getText('.short-desc');
    document.getElementById('hdn_about_title').value = getText('.section-title').split('\n')[0];
    document.getElementById('hdn_about_subtitle').value = getText('.signature-text');
    document.getElementById('hdn_long_desc').value = getHtml('.long-desc-container');

    // For Top Features and Highlights, we can capture them dynamically and add to form, 
    // but for MVP we are just submitting the main form.
    
    document.getElementById('btn-save-project').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    
    // Submit the form
    document.getElementById('live-add-form').submit();
}

let slugEditedManually = false;
document.getElementById('slug').addEventListener('input', function() {
    slugEditedManually = true;
});

setInterval(() => {
    if (slugEditedManually) return;
    try {
        const iframe = document.getElementById('editor-iframe');
        if(!iframe) return;
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        const titleEl = doc.querySelector('.project-title');
        if (titleEl) {
            const titleText = titleEl.innerText.trim();
            // Don't auto-generate if it's still the default text
            if (titleText && !titleText.includes('MODERN 4 BHK')) {
                const generatedSlug = titleText.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                if (generatedSlug) {
                    document.getElementById('slug').value = generatedSlug;
                }
            }
        }
    } catch(e) {}
}, 1000);
</script>

<?php include 'includes/footer.php'; ?>

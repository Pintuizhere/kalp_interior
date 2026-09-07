<?php
require_once 'config/db.php';
$pageTitle = 'Add New Post';
$currentPage = 'blog';

$success_msg = '';
$error_msg = '';

// Fetch for edit if ?edit=ID is passed
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
        $pageTitle = 'Edit Post';
    }
    $stmt->close();
}

// Default HTML template for New Posts
$default_template = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_post'])) {
    $blog_id = isset($_POST['blog_id']) && !empty($_POST['blog_id']) ? (int)$_POST['blog_id'] : null;
    $title = $_POST['title'];
    $slug = $_POST['slug'] ?? '';
    $content = $_POST['content'];
    $status = $_POST['status'];
    $category = $_POST['category'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $author = 'Admin'; 

    $image_name = '';

    if (empty($title)) {
        $error_msg = "Post title is required.";
    } elseif (empty($slug)) {
        $error_msg = "Slug is required.";
    } elseif (empty($category)) {
        $error_msg = "Please select a category.";
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/blogs/';
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_extension, $allowed_exts)) {
                $image_name = 'blog_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $image_name;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error_msg = "Failed to move uploaded image.";
                }
            } else {
                $error_msg = "Invalid image format.";
            }
        }

        if (empty($error_msg)) {
            if ($blog_id) {
                // Update
                if ($image_name !== '') {
                    $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, category=?, image=?, content=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, tags=? WHERE id=?");
                    $stmt->bind_param("ssssssssssi", $title, $slug, $category, $image_name, $content, $status, $meta_title, $meta_description, $meta_keywords, $tags, $blog_id);
                } else {
                    $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, category=?, content=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, tags=? WHERE id=?");
                    $stmt->bind_param("sssssssssi", $title, $slug, $category, $content, $status, $meta_title, $meta_description, $meta_keywords, $tags, $blog_id);
                }
                if ($stmt->execute()) {
                    header("Location: editor-blog.php?edit=" . $blog_id . "&success=update");
                    exit;
                } else {
                    $error_msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO blogs (title, slug, author, category, image, content, status, meta_title, meta_description, meta_keywords, tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssss", $title, $slug, $author, $category, $image_name, $content, $status, $meta_title, $meta_description, $meta_keywords, $tags);
                if ($stmt->execute()) {
                    header("Location: blog.php?success=insert");
                    exit;
                } else {
                    $error_msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'update') {
    $success_msg = "Post updated successfully!";
}

include 'includes/header.php';
include 'includes/sidebar.php';

// Fetch categories for the sidebar
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

?>

<!-- jQuery (required for Summernote) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote Lite CDN -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
  $(document).ready(function() {
      $('#content').summernote({
          placeholder: 'Write your post content here...',
          tabsize: 2,
          height: 500,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ],
          callbacks: {
              onImageUpload: function(files) {
                  uploadImage(files[0], this);
              }
          }
      });

      function uploadImage(file, editor) {
          var data = new FormData();
          data.append("file", file);
          $.ajax({
              url: 'upload_handler_summernote.php',
              cache: false,
              contentType: false,
              processData: false,
              data: data,
              type: "post",
              success: function(url) {
                  $(editor).summernote('insertImage', url);
              },
              error: function(data) {
                  console.log(data);
              }
          });
      }
  });
</script>

<style>
/* Fix Summernote Modal Close Button */
.note-modal .close {
    position: absolute !important;
    right: 15px !important;
    top: 15px !important;
    font-size: 28px !important;
    font-weight: bold !important;
    color: #333 !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
    z-index: 1050 !important;
}
.note-modal .close:hover {
    color: #ff0000 !important;
}

/* Fix Fullscreen Mode */
.note-editor.note-frame.fullscreen {
    z-index: 100000 !important;
    background: #fff;
}
</style>

<style>
    .wp-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        align-items: start;
    }

    .wp-panel {
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        margin-bottom: 20px;
    }
    .wp-panel-header {
        padding: 10px 12px;
        border-bottom: 1px solid #ccd0d4;
        font-weight: 600;
        font-size: 14px;
        color: #1d2327;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wp-panel-body {
        padding: 12px;
        font-size: 13px;
        color: #3c434a;
    }
    
    .wp-title-input {
        width: 100%;
        padding: 10px;
        font-size: 24px;
        border: 1px solid #8c8f94;
        margin-bottom: 15px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
        border-radius: 4px;
        background-color: #fff;
    }
    .wp-title-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 1px var(--primary-color);
        outline: none;
    }

    .wp-seo-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #8c8f94;
        border-radius: 3px;
        margin-top: 5px;
        margin-bottom: 15px;
    }

    .wp-publish-actions {
        padding: 10px 12px;
        background: #f6f7f7;
        border-top: 1px solid #ccd0d4;
        display: flex;
        justify-content: flex-end;
    }

    .wp-btn-primary {
        background: #2271b1;
        border-color: #2271b1;
        color: #fff;
        text-decoration: none;
        text-shadow: none;
        cursor: pointer;
        display: inline-block;
        font-size: 13px;
        line-height: 2.15384615;
        min-height: 30px;
        margin: 0;
        padding: 0 10px;
        border-width: 1px;
        border-style: solid;
        border-radius: 3px;
    }
    .wp-btn-primary:hover {
        background: #135e96;
        border-color: #135e96;
    }

    .wp-cat-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccd0d4;
        padding: 10px;
        background: #fdfdfd;
        margin-bottom: 10px;
    }
    .wp-cat-item {
        margin-bottom: 8px;
    }

    @media (max-width: 992px) {
        .wp-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content" style="background-color: #f0f0f1; padding: 20px;">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#fff; border-left:4px solid #00a32a; padding:12px; margin-bottom:20px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
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
            <div style="background:#fff; border-left:4px solid #d63638; padding:12px; margin-bottom:20px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <h1 style="margin:0; font-size: 23px; font-weight: 400; color: #1d2327;"><?php echo $pageTitle; ?></h1>
            <a href="blog.php" style="border:1px solid #2271b1; color:#2271b1; background:#f6f7f7; padding:4px 8px; font-size:13px; text-decoration:none; border-radius:3px;">Back to Posts</a>
        </div>

        <form action="editor-blog.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_post" value="1">
            <?php if($edit_data): ?>
                <input type="hidden" name="blog_id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>
            
            <div class="wp-layout">
                
                <!-- Left Column (Main Content & SEO) -->
                <div>
                    <input type="text" name="title" class="wp-title-input" placeholder="Add title" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required>
                    <input type="text" name="slug" class="wp-slug-input" placeholder="URL Slug (e.g., my-awesome-post)" value="<?php echo $edit_data ? htmlspecialchars($edit_data['slug']) : ''; ?>" required>
                    
                    <div style="margin-bottom: 20px;">
                        <textarea id="content" name="content"><?php echo $edit_data ? htmlspecialchars($edit_data['content']) : htmlspecialchars($default_template); ?></textarea>
                    </div>

                    <!-- SEO Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            <span><i class="fa-solid fa-magnifying-glass" style="margin-right:8px;"></i> Search Engine Optimization (SEO)</span>
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="wp-panel-body">
                            <label>Meta Title</label>
                            <input type="text" name="meta_title" class="wp-seo-input" placeholder="Enter SEO Title..." value="<?php echo $edit_data ? htmlspecialchars($edit_data['meta_title']) : ''; ?>">
                            <p style="font-size: 11px; color: #646970; margin-top:-10px; margin-bottom:15px;">Keep it under 60 characters for best results on Google.</p>

                            <label>Meta Description</label>
                            <textarea name="meta_description" class="wp-seo-input" rows="3" placeholder="Enter a brief summary of this post..."><?php echo $edit_data ? htmlspecialchars($edit_data['meta_description']) : ''; ?></textarea>
                            <p style="font-size: 11px; color: #646970; margin-top:-10px; margin-bottom:15px;">Aim for 150-160 characters. This is the snippet shown in search results.</p>

                            <label>Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="wp-seo-input" placeholder="e.g. interior design, modern home, 2026 trends" value="<?php echo $edit_data ? htmlspecialchars($edit_data['meta_keywords']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar Panels) -->
                <div>
                    <!-- Publish Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Publish
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="wp-panel-body">
                            <div style="margin-bottom: 10px;">
                                <i class="fa-solid fa-map-pin" style="color:#8c8f94; margin-right:5px;"></i> Status: 
                                <select name="status" style="border: none; background: none; font-weight: 600; cursor: pointer;">
                                    <option value="Draft" <?php echo ($edit_data && $edit_data['status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="Published" <?php echo ($edit_data && $edit_data['status'] == 'Published') ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <i class="fa-regular fa-eye" style="color:#8c8f94; margin-right:5px;"></i> Visibility: <b>Public</b>
                            </div>
                            <div style="margin-bottom: 10px;">
                                <i class="fa-regular fa-calendar" style="color:#8c8f94; margin-right:5px;"></i> Publish: <b>Immediately</b>
                            </div>
                        </div>
                        <div class="wp-publish-actions">
                            <button type="submit" class="wp-btn-primary"><?php echo $edit_data ? 'Update' : 'Publish'; ?></button>
                        </div>
                    </div>

                    <!-- Categories Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Categories
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="wp-panel-body">
                            <div class="wp-cat-list">
                                <?php if($categories_result && $categories_result->num_rows > 0): ?>
                                    <?php while($cat = $categories_result->fetch_assoc()): ?>
                                        <div class="wp-cat-item">
                                            <label style="cursor: pointer;">
                                                <input type="radio" name="category" value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($edit_data && $edit_data['category'] == $cat['name']) ? 'checked' : ''; ?> required>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p>No categories found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Tags
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="wp-panel-body">
                            <input type="text" name="tags" style="width: 100%; padding: 6px; border: 1px solid #8c8f94; margin-bottom: 5px;" placeholder="Add tags..." value="<?php echo $edit_data ? htmlspecialchars($edit_data['tags']) : ''; ?>">
                            <p style="font-size: 11px; color: #646970; margin: 0;">Separate tags with commas</p>
                        </div>
                    </div>

                    <!-- Featured Image Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Featured Image
                            <i class="fa-solid fa-caret-down"></i>
                        </div>
                        <div class="wp-panel-body">
                            <?php if($edit_data && !empty($edit_data['image'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="../uploads/blogs/<?php echo $edit_data['image']; ?>" style="max-width: 100%; border-radius: 4px;">
                                </div>
                                <p style="font-size: 11px; color: #646970; margin-bottom:10px;">Upload a new image to replace the current one.</p>
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*" style="width: 100%;" <?php echo !$edit_data ? 'required' : ''; ?>>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

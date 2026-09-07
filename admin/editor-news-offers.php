<?php
require_once 'config/db.php';
$pageTitle = 'Add New Item';
$currentPage = 'news_offers';

$success_msg = '';
$error_msg = '';

// Fetch for edit if ?edit=ID is passed
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM news_offers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
        $pageTitle = 'Edit Item';
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_post'])) {
    $item_id = isset($_POST['item_id']) && !empty($_POST['item_id']) ? (int)$_POST['item_id'] : null;
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $category = $_POST['category'];
    $short_description = $_POST['short_description'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    $offer_badge_text = $_POST['offer_badge_text'] ?? null;
    
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';

    $image_name = '';

    if (empty($title)) {
        $error_msg = "Title is required.";
    } elseif (empty($slug)) {
        $error_msg = "Slug is required.";
    } else {
        // Ensure slug is lowercase and dashed
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));

        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_extension, $allowed_exts)) {
                $image_name = 'news_' . time() . '.' . $file_extension;
                $target_file = $upload_dir . $image_name;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error_msg = "Failed to move uploaded image.";
                }
            } else {
                $error_msg = "Invalid image format.";
            }
        }

        if (empty($error_msg)) {
            if ($item_id) {
                // Update
                if ($image_name !== '') {
                    $stmt = $conn->prepare("UPDATE news_offers SET title=?, slug=?, category=?, short_description=?, image=?, content=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, offer_badge_text=? WHERE id=?");
                    $stmt->bind_param("sssssssssssi", $title, $slug, $category, $short_description, $image_name, $content, $status, $meta_title, $meta_description, $meta_keywords, $offer_badge_text, $item_id);
                } else {
                    $stmt = $conn->prepare("UPDATE news_offers SET title=?, slug=?, category=?, short_description=?, content=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, offer_badge_text=? WHERE id=?");
                    $stmt->bind_param("ssssssssssi", $title, $slug, $category, $short_description, $content, $status, $meta_title, $meta_description, $meta_keywords, $offer_badge_text, $item_id);
                }
                
                if ($stmt->execute()) {
                    header("Location: editor-news-offers.php?edit=" . $item_id . "&success=update");
                    exit;
                } else {
                    $error_msg = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO news_offers (title, slug, category, short_description, image, content, status, meta_title, meta_description, meta_keywords, offer_badge_text) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssssss", $title, $slug, $category, $short_description, $image_name, $content, $status, $meta_title, $meta_description, $meta_keywords, $offer_badge_text);
                if ($stmt->execute()) {
                    header("Location: manage_news_offers.php?success=insert");
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
    $success_msg = "Item updated successfully!";
}

include 'includes/header.php';
include 'includes/sidebar.php';

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
                  // If you want to handle image uploads via Summernote, you'd send them to upload_handler.php via AJAX
                  // For now, it embeds images as base64 strings by default if no callback is provided, 
                  // but you can implement a standard AJAX upload here.
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
    .wp-title-input, .wp-slug-input {
        width: 100%;
        padding: 10px;
        font-size: 20px;
        border: 1px solid #8c8f94;
        margin-bottom: 15px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
        border-radius: 4px;
        background-color: #fff;
    }
    .wp-slug-input { font-size: 16px; font-family: monospace; }
    .wp-title-input:focus, .wp-slug-input:focus {
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
        background: var(--primary-color);
        color: #fff;
        border: none;
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: 500;
    .wp-btn-primary:hover {
        background: #23342c;
    }

    @media (max-width: 992px) {
        .wp-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

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

        <div class="page-header" style="margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #1d2327; font-family: 'Inter', sans-serif; text-transform:none;"><?php echo $pageTitle; ?></h1>
            <a href="manage_news_offers.php" class="btn-primary" style="text-decoration: none; display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_post" value="1">
            <?php if($edit_data): ?>
                <input type="hidden" name="item_id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>

            <div class="wp-layout">
                <!-- Left Column (Main Content) -->
                <div>
                    <input type="text" name="title" class="wp-title-input" placeholder="Add title" value="<?php echo $edit_data ? htmlspecialchars($edit_data['title']) : ''; ?>" required id="title-input">
                    
                    <div style="display:flex; align-items:center; margin-bottom: 20px;">
                        <strong style="margin-right:10px;">Permalink:</strong>
                        <span style="color:#666;">/news-details.php?slug=</span>
                        <input type="text" name="slug" id="slug-input" class="wp-slug-input" style="margin-bottom:0; flex:1; margin-left:5px; border:1px solid #ddd; padding:5px;" value="<?php echo $edit_data ? htmlspecialchars($edit_data['slug']) : ''; ?>" required>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <strong>Short Description (For cards on home page):</strong>
                        <textarea name="short_description" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; height:80px; font-family:inherit; margin-top:5px;"><?php echo $edit_data ? htmlspecialchars($edit_data['short_description']) : ''; ?></textarea>
                    </div>

                    <div style="background: #fff; border: 1px solid #ccd0d4; margin-bottom: 20px;">
                        <textarea id="content" name="content"><?php echo $edit_data ? htmlspecialchars($edit_data['content']) : ''; ?></textarea>
                    </div>

                    <!-- SEO Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">Search Engine Optimization (SEO)</div>
                        <div class="wp-panel-body">
                            <label><strong>Meta Title</strong></label>
                            <input type="text" name="meta_title" class="wp-seo-input" placeholder="e.g. Summer Offer | Kalp Interiors" value="<?php echo $edit_data ? htmlspecialchars($edit_data['meta_title']) : ''; ?>">
                            
                            <label><strong>Meta Description</strong></label>
                            <textarea name="meta_description" class="wp-seo-input" rows="3" placeholder="Brief description for search results..."><?php echo $edit_data ? htmlspecialchars($edit_data['meta_description']) : ''; ?></textarea>
                            
                            <label><strong>Meta Keywords</strong></label>
                            <input type="text" name="meta_keywords" class="wp-seo-input" placeholder="e.g. interior offer, summer discount, ranchi interiors" value="<?php echo $edit_data ? htmlspecialchars($edit_data['meta_keywords']) : ''; ?>">
                            <p style="font-size: 11px; color: #646970; margin-top:-10px;">Separate keywords with commas</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar Panels) -->
                <div>
                    <!-- Publish Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Publish
                        </div>
                        <div class="wp-panel-body">
                            <div style="margin-bottom: 10px;">
                                <i class="fa-solid fa-map-pin" style="color:#8c8f94; margin-right:5px;"></i> Status: 
                                <select name="status" style="border: none; background: none; font-weight: 600; cursor: pointer;">
                                    <option value="Draft" <?php echo ($edit_data && $edit_data['status'] == 'Draft') ? 'selected' : ''; ?>>Draft</option>
                                    <option value="Published" <?php echo ($edit_data && $edit_data['status'] == 'Published') ? 'selected' : ''; ?>>Published</option>
                                </select>
                            </div>
                        </div>
                        <div class="wp-publish-actions">
                            <button type="submit" class="wp-btn-primary"><?php echo $edit_data ? 'Update' : 'Publish'; ?></button>
                        </div>
                    </div>

                    <!-- Categories Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Category
                        </div>
                        <div class="wp-panel-body">
                            <select name="category" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:3px;" required>
                                <option value="Offers" <?php echo ($edit_data && $edit_data['category'] == 'Offers') ? 'selected' : ''; ?>>Offers</option>
                                <option value="News" <?php echo ($edit_data && $edit_data['category'] == 'News') ? 'selected' : ''; ?>>News</option>
                                <option value="Notifications" <?php echo ($edit_data && $edit_data['category'] == 'Notifications') ? 'selected' : ''; ?>>Notifications</option>
                            </select>
                            
                            <div style="margin-top: 15px;">
                                <label style="display:block; font-weight:600; margin-bottom:5px;">Offer Badge Text (Optional)</label>
                                <input type="text" name="offer_badge_text" placeholder="e.g. 20% OFF" value="<?php echo $edit_data ? htmlspecialchars($edit_data['offer_badge_text']) : ''; ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:3px;">
                                <p style="font-size: 11px; color: #646970; margin-top:3px;">Displays a circle badge on the card if set.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image Panel -->
                    <div class="wp-panel">
                        <div class="wp-panel-header">
                            Featured Image
                        </div>
                        <div class="wp-panel-body">
                            <?php if($edit_data && !empty($edit_data['image'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="../uploads/news/<?php echo $edit_data['image']; ?>" style="max-width: 100%; border-radius: 4px;">
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

<script>
// Auto-generate slug from title
document.getElementById('title-input').addEventListener('input', function() {
    <?php if(!$edit_data): // Only auto-generate on new post ?>
    let title = this.value;
    let slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    document.getElementById('slug-input').value = slug;
    <?php endif; ?>
});
</script>

<?php include 'includes/footer.php'; ?>

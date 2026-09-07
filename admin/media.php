<?php
$pageTitle = 'Media Library';
$currentPage = 'media';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

$upload_dir = '../uploads/media/';

// Handle POST request to upload new media
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_media'])) {
    if (isset($_FILES['media_files']) && !empty($_FILES['media_files']['name'][0])) {
        $files = $_FILES['media_files'];
        $total_files = count($files['name']);
        $success_count = 0;
        $error_count = 0;
        
        $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $video_exts = ['mp4', 'webm', 'ogg', 'mov'];
        
        for ($i = 0; $i < $total_files; $i++) {
            if ($files['error'][$i] == 0) {
                $original_name = $files['name'][$i];
                $tmp_name = $files['tmp_name'][$i];
                
                $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                $new_file_name = 'media_' . time() . '_' . uniqid() . '.' . $file_extension;
                $target_file = $upload_dir . $new_file_name;
                
                $file_type = 'unknown';
                if (in_array($file_extension, $image_exts)) {
                    $file_type = 'image';
                } elseif (in_array($file_extension, $video_exts)) {
                    $file_type = 'video';
                }
                
                if ($file_type !== 'unknown') {
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $stmt = $conn->prepare("INSERT INTO media (file_name, file_path, file_type) VALUES (?, ?, ?)");
                        if ($stmt) {
                            $stmt->bind_param("sss", $original_name, $new_file_name, $file_type);
                            if ($stmt->execute()) {
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                            $stmt->close();
                        }
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++;
                }
            } else {
                $error_count++;
            }
        }
        
        if ($success_count > 0) {
            $success_msg = "$success_count media file(s) uploaded successfully!";
        }
        if ($error_count > 0) {
            $error_msg = "$error_count file(s) failed to upload (unsupported format or errors).";
        }
    } else {
        $error_msg = "Please select a valid file to upload.";
    }
}

// Handle GET request to delete a media file
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // First fetch the file path
    $get_file = $conn->prepare("SELECT file_path FROM media WHERE id = ?");
    $get_file->bind_param("i", $delete_id);
    $get_file->execute();
    $get_file->bind_result($file_path_db);
    if ($get_file->fetch()) {
        $full_path = $upload_dir . $file_path_db;
        $get_file->close();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                // Delete actual file
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
                $success_msg = "Media deleted successfully!";
            } else {
                $error_msg = "Error deleting from database.";
            }
            $stmt->close();
        }
    } else {
        $get_file->close();
    }
}

// Fetch all media
$media_query = "SELECT * FROM media ORDER BY uploaded_at DESC";
$media_result = $conn->query($media_query);
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

        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="margin:0;">Media Library</h1>
            <button class="btn-primary" onclick="document.getElementById('upload-modal').style.display='flex'">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload Media
            </button>
        </div>

        <div class="media-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <?php if($media_result && $media_result->num_rows > 0): ?>
                <?php while($media = $media_result->fetch_assoc()): 
                    $file_url = 'http://' . $_SERVER['HTTP_HOST'] . '/kalp_interior/uploads/media/' . $media['file_path'];
                ?>
                <div class="media-item" style="background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05); position:relative; group;">
                    <div class="media-preview" style="height:150px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        <?php if($media['file_type'] == 'image'): ?>
                            <img src="../uploads/media/<?php echo $media['file_path']; ?>" style="width:100%; height:100%; object-fit:cover;" alt="<?php echo htmlspecialchars($media['file_name']); ?>">
                        <?php else: ?>
                            <i class="fa-solid fa-file-video" style="font-size: 3rem; color: var(--text-muted);"></i>
                        <?php endif; ?>
                    </div>
                    <div class="media-details" style="padding:10px;">
                        <p style="margin:0; font-size:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:500; color:var(--text-dark);" title="<?php echo htmlspecialchars($media['file_name']); ?>">
                            <?php echo htmlspecialchars($media['file_name']); ?>
                        </p>
                    </div>
                    
                    <div class="media-actions" style="position:absolute; top:10px; right:10px; display:flex; gap:5px;">
                        <button onclick="copyToClipboard('<?php echo $file_url; ?>')" title="Copy Link" style="background:#fff; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; box-shadow:0 2px 5px rgba(0,0,0,0.2); color:var(--primary-color); display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-link"></i>
                        </button>
                        <a href="media.php?delete_id=<?php echo $media['id']; ?>" onclick="return confirm('Are you sure you want to permanently delete this file?');" title="Delete" style="background:#ef4444; color:#fff; border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; box-shadow:0 2px 5px rgba(0,0,0,0.2); display:flex; align-items:center; justify-content:center; text-decoration:none;">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #fff; border-radius: 8px;">
                    <i class="fa-regular fa-image" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 10px; display: block;"></i>
                    <p style="color: var(--text-muted); margin: 0;">Your media library is empty.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; width:90%; max-width:500px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.2); overflow:hidden;">
        <div class="modal-header" style="background:var(--bg-white); padding:20px; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-family:var(--font-headline); color:var(--text-dark);">Upload New Media</h3>
            <button onclick="document.getElementById('upload-modal').style.display='none'" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-muted);"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="media.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body" style="padding:20px;">
                <input type="hidden" name="upload_media" value="1">
                <div class="form-group">
                    <label style="display:block; margin-bottom:8px; font-weight:500;">Select Files (Images or Videos) <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="media_files[]" multiple required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;" accept="image/*,video/*">
                    <p style="font-size:12px; color:var(--text-muted); margin-top:5px;">You can select multiple files at once. Supported formats: JPG, PNG, GIF, WEBP, MP4, WEBM</p>
                </div>
            </div>
            <div class="modal-footer" style="padding:20px; background:#f9f9f9; border-top:1px solid var(--border-color); text-align:right;">
                <button type="button" class="btn-secondary" onclick="document.getElementById('upload-modal').style.display='none'" style="background:none; border:1px solid var(--border-color); padding:8px 20px; border-radius:5px; margin-right:10px; cursor:pointer;">Cancel</button>
                <button type="submit" class="btn-primary" style="padding:8px 20px;">Upload File</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyToClipboard(text) {
    // Create a temporary textarea element
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    
    // Optional: Show a small toast or alert
    alert("Link copied to clipboard: " + text);
}
</script>

<?php include 'includes/footer.php'; ?>

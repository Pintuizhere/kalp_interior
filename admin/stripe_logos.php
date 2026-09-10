<?php
session_start();
require 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$success_msg = '';
$error_msg = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_logo'])) {
    $stripe_type = $conn->real_escape_string($_POST['stripe_type']);
    $alt_text = $conn->real_escape_string($_POST['alt_text']);
    
    $upload_dir = '../assets/images/stripes/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (!empty($_POST['cropped_image'])) {
        $base64_string = $_POST['cropped_image'];
        $image_parts = explode(";base64,", $base64_string);
        $image_type_aux = explode("image/", $image_parts[0]);
        $file_ext = isset($image_type_aux[1]) ? $image_type_aux[1] : 'png';
        $image_base64 = base64_decode($image_parts[1]);
        
        $new_filename = time() . '_' . rand(100, 999) . '.' . $file_ext;
        $target_file = $upload_dir . $new_filename;
        
        if (file_put_contents($target_file, $image_base64)) {
            $db_path = 'assets/images/stripes/' . $new_filename;
            $sql = "INSERT INTO stripe_logos (stripe_type, image_path, alt_text) VALUES ('$stripe_type', '$db_path', '$alt_text')";
            if ($conn->query($sql) === TRUE) {
                $success_msg = "Logo uploaded and cropped successfully!";
            } else {
                $error_msg = "Database Error: " . $conn->error;
            }
        } else {
            $error_msg = "Failed to save cropped image.";
        }
    } elseif (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
        $file_info = pathinfo($_FILES['logo_image']['name']);
        $file_ext = strtolower($file_info['extension']);
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = time() . '_' . rand(100, 999) . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target_file)) {
                $db_path = 'assets/images/stripes/' . $new_filename;
                $sql = "INSERT INTO stripe_logos (stripe_type, image_path, alt_text) VALUES ('$stripe_type', '$db_path', '$alt_text')";
                if ($conn->query($sql) === TRUE) {
                    $success_msg = "Logo uploaded successfully!";
                } else {
                    $error_msg = "Database Error: " . $conn->error;
                }
            } else {
                $error_msg = "Failed to move uploaded file.";
            }
        } else {
            $error_msg = "Invalid file type. Allowed types: " . implode(', ', $allowed_ext);
        }
    } else {
        $error_msg = "Please select a valid image file.";
    }
}

// Handle Logo Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    // Get file path to delete file from server
    $res = $conn->query("SELECT image_path FROM stripe_logos WHERE id = $delete_id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $file_to_delete = '../' . $row['image_path'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
        
        // Delete from DB
        if ($conn->query("DELETE FROM stripe_logos WHERE id = $delete_id")) {
            $success_msg = "Logo deleted successfully!";
        } else {
            $error_msg = "Failed to delete from database.";
        }
    }
}

$pageTitle = "Stripe Logos";
$currentPage = "stripe_logos";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;">
                <i class="fa-solid fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if(!empty($error_msg)): ?>
            <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="page-header">
            <h1>Manage Stripe Logos</h1>
        </div>
        <p class="text-muted" style="margin-bottom: 30px;">Upload and manage the logos that appear in the horizontal scrolling stripes on your website.</p>

        <!-- Upload Form -->
        <div class="table-wrapper" style="margin-bottom: 40px; padding: 25px;">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px;">Add New Logo</h3>
            <form action="stripe_logos.php" id="logoForm" method="POST" enctype="multipart/form-data" style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <input type="hidden" name="cropped_image" id="cropped_image">
                
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label style="display:block; margin-bottom:8px; font-weight:500;">Stripe Category <span style="color:#ef4444;">*</span></label>
                    <select name="stripe_type" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px; background:white;">
                        <option value="partner">Trusted Partners Stripe</option>
                        <option value="project">Our Projects Stripe</option>
                    </select>
                </div>

                <div class="form-group" style="flex: 2; min-width: 250px; margin-bottom: 0;">
                    <label style="display:block; margin-bottom:8px; font-weight:500;">Logo Image <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="logo_image" id="logo_image_input" accept="image/*" required style="width:100%; padding:9px; border:1px solid var(--border-color); border-radius:5px; background:white;">
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">Recommended: SVG, PNG with transparent background. Height ~40px.</p>
                </div>

                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label style="display:block; margin-bottom:8px; font-weight:500;">Alt Text (Optional)</label>
                    <input type="text" name="alt_text" placeholder="e.g. Amazon Logo" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:5px;">
                </div>
                
                <div class="form-group" style="flex: 0 0 auto; margin-bottom: 0; align-self: center; margin-top: 25px;">
                    <button type="submit" name="upload_logo" class="btn-primary" style="padding: 10px 20px;">
                        Upload Logo
                    </button>
                </div>
            </form>
        </div>

        <div class="form-grid" style="gap: 30px;">
            
            <!-- Trusted Partners Logos -->
            <div class="table-wrapper">
                <div style="padding: 20px 25px 0; border-bottom: 1px solid var(--border-color); margin-bottom: 20px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">
                        <i class="fa-solid fa-handshake" style="color: var(--accent-color); margin-right: 8px;"></i> Trusted Partners
                    </h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Alt Text</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $partners = $conn->query("SELECT * FROM stripe_logos WHERE stripe_type = 'partner' ORDER BY created_at DESC");
                        if($partners->num_rows > 0):
                            while($row = $partners->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="background-color: #f8f9fa; text-align: center;">
                                <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" alt="Logo" style="max-height: 30px; max-width: 100px; object-fit: contain;">
                            </td>
                            <td><?php echo htmlspecialchars($row['alt_text']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="stripe_logos.php?delete_id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this logo?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">No partner logos found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Our Projects Logos -->
            <div class="table-wrapper">
                <div style="padding: 20px 25px 0; border-bottom: 1px solid var(--border-color); margin-bottom: 20px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px;">
                        <i class="fa-solid fa-city" style="color: var(--accent-color); margin-right: 8px;"></i> Our Projects
                    </h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Alt Text</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $projects = $conn->query("SELECT * FROM stripe_logos WHERE stripe_type = 'project' ORDER BY created_at DESC");
                        if($projects->num_rows > 0):
                            while($row = $projects->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="background-color: #f8f9fa; text-align: center;">
                                <img src="../<?php echo htmlspecialchars($row['image_path']); ?>" alt="Logo" style="max-height: 40px; max-width: 100px; object-fit: contain; border-radius: 5px;">
                            </td>
                            <td><?php echo htmlspecialchars($row['alt_text']); ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="stripe_logos.php?delete_id=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this logo?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">No project logos found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Crop Modal -->
<div id="cropModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:20px; border-radius:10px; width:90%; max-width:600px; text-align:center;">
        <h3 style="margin-top: 0;">Zoom & Crop Logo</h3>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Use your mouse wheel to zoom in/out, and drag to position.</p>
        <div style="max-height:400px; overflow:hidden; margin-bottom:20px;">
            <img id="imageToCrop" src="" alt="Image to crop" style="max-width:100%; display:block;">
        </div>
        <button type="button" id="btnCancelCrop" class="btn-secondary" style="padding:10px 20px; margin-right:10px; border:1px solid #ccc; background:#eee; cursor:pointer; border-radius: 5px;">Cancel</button>
        <button type="button" id="btnSaveCrop" class="btn-primary" style="padding:10px 20px; cursor:pointer; border-radius: 5px; background: var(--accent-color); color: var(--text-dark); border: none; font-weight: 600;">Apply Crop</button>
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper;
const imageInput = document.getElementById('logo_image_input');
const imageToCrop = document.getElementById('imageToCrop');
const cropModal = document.getElementById('cropModal');
const croppedImageInput = document.getElementById('cropped_image');

if (imageInput) {
    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];
            // Only use cropper for non-svg images
            if (file.type === 'image/svg+xml') {
                return;
            }
            const url = URL.createObjectURL(file);
            imageToCrop.src = url;
            cropModal.style.display = 'flex';
            
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(imageToCrop, {
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                background: false
            });
            // We want to clear the required attribute since we have cropped image
            imageInput.removeAttribute('required');
        }
    });
}

document.getElementById('btnCancelCrop').addEventListener('click', function() {
    cropModal.style.display = 'none';
    if(cropper) {
        cropper.destroy();
    }
    imageInput.value = ''; 
    croppedImageInput.value = '';
    imageInput.setAttribute('required', 'required');
});

document.getElementById('btnSaveCrop').addEventListener('click', function() {
    if (!cropper) return;
    
    const canvas = cropper.getCroppedCanvas({
        maxWidth: 1024,
        maxHeight: 1024,
        fillColor: 'transparent'
    });
    
    if (canvas) {
        const base64Image = canvas.toDataURL('image/png');
        croppedImageInput.value = base64Image;
        cropModal.style.display = 'none';
        
        const applyBtn = document.getElementById('btnSaveCrop');
        applyBtn.innerText = 'Applied! You can now Upload';
        setTimeout(() => { applyBtn.innerText = 'Apply Crop'; }, 2000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>

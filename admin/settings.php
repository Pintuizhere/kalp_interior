<?php
require_once 'config/db.php';
$pageTitle = 'Global Settings';
$currentPage = 'settings';
include 'includes/header.php';
include 'includes/sidebar.php';

$success_msg = '';
$error_msg = '';

// Handle Save Settings
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) {
    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        
        $success = true;
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->bind_param("ss", $value, $key);
            if (!$stmt->execute()) {
                $success = false;
            }
        }
        $stmt->close();
        
        if ($success) {
            $success_msg = "Global settings updated successfully!";
        } else {
            $error_msg = "Error updating settings. Please try again.";
        }
    }
}

// Fetch current settings
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM site_settings");
if ($res) {
    while($row = $res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Helper to safely get value
function val($key, $data) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}
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

        <div class="page-header" style="margin-bottom: 30px;">
            <div>
                <h1 style="margin:0;">Global Settings</h1>
                <p style="color:var(--text-muted); margin-top:5px;">Manage website contact information and global variables.</p>
            </div>
        </div>

        <style>
            .settings-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(100%, 350px), 1fr));
                gap: 25px;
                margin-bottom: 30px;
                max-width: 1200px;
            }
            .settings-card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                padding: 25px;
                border: 1px solid #f1f5f9;
            }
            .settings-card h3 {
                margin-top: 0;
                margin-bottom: 20px;
                color: var(--primary-color);
                font-size: 1.1rem;
                display: flex;
                align-items: center;
                gap: 10px;
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: 15px;
            }
            .settings-form-group {
                margin-bottom: 20px;
            }
            .settings-form-group:last-child {
                margin-bottom: 0;
            }
            .settings-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: #475569;
                font-size: 0.9rem;
            }
            .settings-input {
                width: 100%;
                padding: 10px 15px;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                font-size: 0.95rem;
                color: #334155;
                transition: all 0.2s ease;
                background: #f8fafc;
            }
            .settings-input:focus {
                outline: none;
                border-color: var(--accent-color);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(200, 169, 114, 0.2); /* Soft accent */
            }
            textarea.settings-input {
                resize: vertical;
                min-height: 80px;
                font-family: inherit;
            }
            .settings-actions {
                background: #fff;
                padding: 20px 25px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
                max-width: 1200px;
                border: 1px solid #f1f5f9;
            }
            .btn-save-settings {
                background: var(--accent-color);
                color: #fff;
                border: none;
                padding: 12px 35px;
                font-weight: 600;
                font-size: 1rem;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.3s ease;
            }
            .btn-save-settings:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(200, 169, 114, 0.4);
            }
            .btn-save-settings i {
                font-size: 1.1rem;
            }
        </style>

        <form action="settings.php" method="POST">
            <input type="hidden" name="save_settings" value="1">
            
            <div class="settings-grid">
                <!-- General Info -->
                <div class="settings-card">
                    <h3><i class="fa-solid fa-globe"></i> General Information</h3>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Website Name</label>
                        <input type="text" name="settings[site_name]" value="<?php echo val('site_name', $settings); ?>" required class="settings-input">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Footer Short Text</label>
                        <textarea name="settings[footer_text]" class="settings-input"><?php echo val('footer_text', $settings); ?></textarea>
                    </div>
                </div>
                
                <!-- Contact Details -->
                <div class="settings-card">
                    <h3><i class="fa-solid fa-address-card"></i> Contact Details</h3>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Primary Email Address</label>
                        <input type="email" name="settings[contact_email]" value="<?php echo val('contact_email', $settings); ?>" required class="settings-input">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Phone Number</label>
                        <input type="text" name="settings[contact_phone]" value="<?php echo val('contact_phone', $settings); ?>" required class="settings-input">
                    </div>

                    <div class="settings-form-group">
                        <label class="settings-label">Office Address</label>
                        <textarea name="settings[contact_address]" required class="settings-input"><?php echo val('contact_address', $settings); ?></textarea>
                    </div>

                    <div class="settings-form-group">
                        <label class="settings-label">Open Time (Weekdays)</label>
                        <input type="text" name="settings[open_time_weekdays]" value="<?php echo val('open_time_weekdays', $settings); ?>" class="settings-input">
                    </div>

                    <div class="settings-form-group">
                        <label class="settings-label">Open Time (Weekends)</label>
                        <input type="text" name="settings[open_time_weekends]" value="<?php echo val('open_time_weekends', $settings); ?>" class="settings-input">
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="settings-card">
                    <h3><i class="fa-solid fa-hashtag"></i> Social Media Links</h3>
                    
                    <div class="settings-form-group">
                        <label class="settings-label"><i class="fa-brands fa-facebook" style="color:#1877f2;"></i> Facebook URL</label>
                        <input type="url" name="settings[social_facebook]" value="<?php echo val('social_facebook', $settings); ?>" class="settings-input">
                    </div>

                    <div class="settings-form-group">
                        <label class="settings-label"><i class="fa-brands fa-instagram" style="color:#e1306c;"></i> Instagram URL</label>
                        <input type="url" name="settings[social_instagram]" value="<?php echo val('social_instagram', $settings); ?>" class="settings-input">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label"><i class="fa-brands fa-x-twitter" style="color:#000;"></i> Twitter/X URL</label>
                        <input type="url" name="settings[social_twitter]" value="<?php echo val('social_twitter', $settings); ?>" class="settings-input">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label"><i class="fa-brands fa-linkedin" style="color:#0077b5;"></i> LinkedIn URL</label>
                        <input type="url" name="settings[social_linkedin]" value="<?php echo val('social_linkedin', $settings); ?>" class="settings-input">
                    </div>
                </div>
                
                <!-- Footer Links -->
                <div class="settings-card">
                    <h3><i class="fa-solid fa-link"></i> Copyright & Policy</h3>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Copyright Display Name</label>
                        <input type="text" name="settings[footer_copyright_name]" value="<?php echo val('footer_copyright_name', $settings); ?>" class="settings-input">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Terms & Conditions URL</label>
                        <input type="text" name="settings[footer_terms_url]" value="<?php echo val('footer_terms_url', $settings); ?>" class="settings-input" placeholder="e.g. terms.php or https://...">
                    </div>
                    
                    <div class="settings-form-group">
                        <label class="settings-label">Privacy Policy URL</label>
                        <input type="text" name="settings[footer_privacy_url]" value="<?php echo val('footer_privacy_url', $settings); ?>" class="settings-input" placeholder="e.g. privacy.php or https://...">
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <div style="color: #64748b; font-size: 0.9rem;">
                    <i class="fa-solid fa-circle-info"></i> Make sure to double-check all URLs before saving.
                </div>
                <button type="submit" class="btn-save-settings">
                    <i class="fa-solid fa-floppy-disk"></i> Save All Settings
                </button>
            </div>
        </form>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'Security Settings';
$currentPage = 'security';
require_once 'config/db.php';

$success_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'save_security') {
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            $stmt = $conn->prepare("INSERT INTO calculator_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            foreach ($_POST['settings'] as $key => $value) {
                $stmt->bind_param("sss", $key, $value, $value);
                $stmt->execute();
            }
            $stmt->close();
            $success_msg = "Security Settings updated successfully!";
        }
    }
}

// Fetch current settings
$settings = [];
$res = $conn->query("SELECT setting_key, setting_value FROM calculator_settings");
while($row = $res->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<?php include 'includes/header.php'; ?>

<style>
.form-panel { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.form-group { margin-bottom: 20px; }
.form-label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 13px; color: #333; }
.form-control { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 5px; font-size: 14px; transition: border-color 0.3s ease; }
.form-control:focus { outline: none; border-color: var(--accent-color); }
.btn-primary { background: var(--accent-color); color: #fff; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: 500; transition: background 0.3s ease; }
.btn-primary:hover { background: #c29528; }
.help-text { color: #66756c; font-size: 12px; margin-top: 6px; display: block; }
</style>

<div class="main-wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <?php if(!empty($success_msg)): ?>
            <div style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px; font-size: 14px;">
                <i class="fa-solid fa-circle-check" style="margin-right: 8px;"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <div class="page-header" style="margin-bottom: 30px;">
            <h1 style="font-size: 24px; color: #1e2723; margin-bottom: 8px;">Security Settings</h1>
            <p style="color:var(--text-muted); font-size: 14px;">Manage API rate limits and protect your system from abuse.</p>
        </div>

        <div class="form-panel" style="max-width: 700px;">
            <div style="display: flex; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(234, 177, 54, 0.1); color: var(--accent-color); display: flex; justify-content: center; align-items: center; font-size: 18px; margin-right: 15px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 16px; color: #1e2723;">OTP Rate Limiting</h3>
                    <p style="margin: 4px 0 0; font-size: 12px; color: #666;">Configure rate limits to prevent malicious bots from draining your Fast2SMS wallet or getting your WhatsApp Business account blocked.</p>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="save_security">
                
                <div class="form-group">
                    <label class="form-label">OTP Cooldown Time (Seconds)</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-stopwatch" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="number" name="settings[otp_cooldown]" value="<?php echo $settings['otp_cooldown'] ?? '60'; ?>" class="form-control" style="padding-left: 45px;" required min="10">
                    </div>
                    <span class="help-text">The required wait time before a user can request another OTP. Recommended: 60 seconds.</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Daily OTP Limit Per User</label>
                    <div style="position: relative;">
                        <i class="fa-solid fa-chart-line" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="number" name="settings[otp_daily_limit]" value="<?php echo $settings['otp_daily_limit'] ?? '5'; ?>" class="form-control" style="padding-left: 45px;" required min="1">
                    </div>
                    <span class="help-text">Maximum number of OTP requests a single user (IP/Session) can make in a 24-hour period. Recommended: 5 requests.</span>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Save Security Settings
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'All Notifications';
$currentPage = 'notifications';
include 'includes/header.php';
include 'includes/sidebar.php';
require_once 'config/db.php';

// Handle Clear All
if (isset($_GET['action']) && $_GET['action'] == 'clear_all') {
    $conn->query("UPDATE leads SET is_cleared = 1");
    $conn->query("UPDATE estimate_requests SET is_cleared = 1");
    header("Location: notifications.php");
    exit;
}

// Fetch all notifications (Up to 100 recent)
$notifs = [];
if (isset($conn)) {
    $q_leads = $conn->query("SELECT id, name, created_at, 'Lead' as type FROM leads WHERE is_cleared = 0 ORDER BY created_at DESC LIMIT 50");
    if ($q_leads) {
        while($r = $q_leads->fetch_assoc()) $notifs[] = $r;
    }
    $q_ests = $conn->query("SELECT id, name, created_at, 'Estimate' as type FROM estimate_requests WHERE is_cleared = 0 ORDER BY created_at DESC LIMIT 50");
    if ($q_ests) {
        while($r = $q_ests->fetch_assoc()) $notifs[] = $r;
    }
    usort($notifs, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}
?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="margin: 0; font-size: 18px; color: #333;">Recent Activity & Notifications</h3>
                <?php if(!empty($notifs)): ?>
                <a href="?action=clear_all" class="btn-primary" style="background-color: #dc3545; color: #fff; padding: 8px 15px; font-size: 13px; text-decoration: none;" onclick="return confirm('Are you sure you want to clear all notifications?');">Clear All</a>
                <?php endif; ?>
            </div>
            
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Notification</th>
                            <th>Date & Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($notifs)): ?>
                            <tr><td colspan="4" style="text-align:center;">No notifications found.</td></tr>
                        <?php else: ?>
                            <?php foreach($notifs as $n): ?>
                                <tr>
                                    <td>
                                        <span style="background: #f8fafc; color: var(--accent-color); padding: 5px 10px; border-radius: 5px; font-size: 11px; text-transform: uppercase; border: 1px solid #eee;">
                                            <i class="<?php echo $n['type'] == 'Lead' ? 'fa-solid fa-user-plus' : 'fa-solid fa-file-invoice'; ?>" style="margin-right: 5px;"></i> <?php echo $n['type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($n['name']); ?></strong> submitted a new <?php echo strtolower($n['type']); ?> request.
                                    </td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($n['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo $n['type'] == 'Lead' ? 'leads.php' : 'estimate_requests.php'; ?>" class="btn-primary" style="padding: 5px 12px; font-size: 11px;">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

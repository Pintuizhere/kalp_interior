<?php
// Fetch latest notifications
$notifs = [];
if (isset($conn)) {
    $q_leads = $conn->query("SELECT id, name, created_at, 'Lead' as type FROM leads WHERE is_cleared = 0 ORDER BY created_at DESC LIMIT 5");
    if ($q_leads) {
        while($r = $q_leads->fetch_assoc()) $notifs[] = $r;
    }
    $q_ests = $conn->query("SELECT id, name, created_at, 'Estimate' as type FROM estimate_requests WHERE is_cleared = 0 ORDER BY created_at DESC LIMIT 5");
    if ($q_ests) {
        while($r = $q_ests->fetch_assoc()) $notifs[] = $r;
    }
    usort($notifs, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    $notifs = array_slice($notifs, 0, 5);
}
$notif_count = count($notifs);
?>
<header class="topbar">
    <div class="topbar-left">
        <button class="mobile-toggle" id="mobile-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
            <h2><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h2>
            <div class="breadcrumb">
                Home <span>&gt;</span> <?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?>
            </div>
        </div>
    </div>
    
    <div class="topbar-right">


        <div class="notification-bell" style="position: relative; cursor: pointer;" onclick="document.getElementById('notif-dropdown').classList.toggle('show')">
            <i class="fa-regular fa-bell"></i>
            <?php if($notif_count > 0): ?>
                <span class="badge"><?php echo $notif_count; ?></span>
            <?php endif; ?>
            
            <div id="notif-dropdown" class="notif-dropdown" style="display: none; position: absolute; right: -50px; top: 130%; background: #fff; width: 320px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); z-index: 1000; border: 1px solid #eee; text-align: left; cursor: default;" onclick="event.stopPropagation()">
                <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: 700; font-size: 14px; display: flex; justify-content: space-between; align-items: center;">
                    <span>Notifications</span>
                    <span style="background: var(--accent-color); color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 10px;"><?php echo $notif_count; ?> New</span>
                </div>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php if (empty($notifs)): ?>
                        <div style="padding: 20px; text-align: center; color: #888; font-size: 13px;">No new notifications</div>
                    <?php else: ?>
                        <?php foreach($notifs as $n): ?>
                            <a href="<?php echo $n['type'] == 'Lead' ? 'leads.php' : 'estimate_requests.php'; ?>" style="display: flex; gap: 15px; padding: 15px; border-bottom: 1px solid #f5f5f5; text-decoration: none; color: #333; transition: background 0.2s;">
                                <div style="width: 35px; height: 35px; min-width: 35px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: var(--accent-color); font-size: 14px;">
                                    <i class="<?php echo $n['type'] == 'Lead' ? 'fa-solid fa-user-plus' : 'fa-solid fa-file-invoice'; ?>"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600;">New <?php echo $n['type']; ?>: <?php echo htmlspecialchars($n['name']); ?></div>
                                    <div style="font-size: 11px; color: #888; margin-top: 4px;"><i class="fa-regular fa-clock" style="margin-right: 3px;"></i> <?php echo date('d M, h:i A', strtotime($n['created_at'])); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div style="padding: 12px; text-align: center; border-top: 1px solid #eee; background: #fafafa; border-radius: 0 0 8px 8px;">
                    <a href="notifications.php" style="font-size: 13px; color: var(--accent-color); text-decoration: none; font-weight: 600;">View All Notifications</a>
                </div>
            </div>
        </div>

        <style>
            .notif-dropdown.show { display: block !important; }
            .notif-dropdown a:hover { background: #f8fafc !important; }
            @media (max-width: 768px) {
                .notif-dropdown { right: -80px !important; width: 280px !important; }
            }
        </style>
        
        <div class="admin-profile">
            <?php
            $u_name = 'Admin User';
            $u_role = 'Administrator';
            
            if (isset($_SESSION['admin_id']) && isset($conn)) {
                $curr_id = $_SESSION['admin_id'];
                $q = $conn->query("SELECT profile_image, email, full_name, role, job_title FROM admin_users WHERE id = '$curr_id'");
                if ($q && $q->num_rows > 0) {
                    $u = $q->fetch_assoc();
                    
                    if (!empty($u['full_name'])) {
                        $u_name = htmlspecialchars($u['full_name']);
                    } else {
                        $u_name = htmlspecialchars(explode('@', $u['email'])[0]);
                    }
                    
                    if (!empty($u['job_title'])) {
                        $u_role = htmlspecialchars($u['job_title']);
                    } elseif (!empty($u['role'])) {
                        $u_role = htmlspecialchars(strtoupper($u['role']));
                    }
                }
            }
            ?>
            <div class="avatar">
                <?php
                if (isset($u) && !empty($u['profile_image'])) {
                    echo '<img src="../uploads/profiles/' . htmlspecialchars($u['profile_image']) . '" alt="Admin">';
                } else {
                    echo '<div style="width: 100%; height: 100%; background: var(--primary-color); color: white; display: flex; justify-content: center; align-items: center; font-size: 16px;"><i class="fa-solid fa-user"></i></div>';
                }
                ?>
            </div>
            <div class="admin-info">
                <span class="name"><?php echo $u_name; ?></span>
                <span class="role"><?php echo $u_role; ?></span>
            </div>
        </div>
    </div>
</header>

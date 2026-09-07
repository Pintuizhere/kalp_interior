<?php
require_once 'config/db.php';
$pageTitle = 'Estimate Requests';
$currentPage = 'estimate_requests';

$success_msg = '';
$error_msg = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM estimate_requests WHERE id=$id")) {
        $success_msg = "Request deleted successfully!";
    } else {
        $error_msg = "Error deleting request: " . $conn->error;
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';

$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$property_category = isset($_GET['property_category']) ? $conn->real_escape_string($_GET['property_category']) : '';

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(name LIKE '%$search%' OR phone LIKE '%$search%' OR location LIKE '%$search%')";
}
if (!empty($property_category)) {
    $where_clauses[] = "property_category = '$property_category'";
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$total_res = $conn->query("SELECT COUNT(*) as cnt FROM estimate_requests $where_sql");
$total_row = $total_res->fetch_assoc();
$total_records = $total_row['cnt'];
$total_pages = ceil($total_records / $limit);

$qs_array = $_GET;
unset($qs_array['page']);
$base_qs = http_build_query($qs_array);
if (!empty($base_qs)) $base_qs .= '&';

?>

<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="page-header">
            <h1>Estimate Requests</h1>
        </div>
        
        <?php if(!empty($success_msg)): ?>
            <div id="notification-msg" style="background:#d4edda; color:#155724; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:500; transition: opacity 0.5s ease;">
                <i class="fa-solid fa-check-circle" style="margin-right:8px;"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($error_msg)): ?>
            <div id="notification-msg" style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:500; transition: opacity 0.5s ease;">
                <i class="fa-solid fa-exclamation-circle" style="margin-right:8px;"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <script>
            setTimeout(() => {
                const notif = document.getElementById('notification-msg');
                if(notif) {
                    notif.style.opacity = '0';
                    setTimeout(() => notif.style.display = 'none', 500);
                }
            }, 3000);
        </script>

        <div class="tabs-header">
            <button class="tab-btn active" id="tab-manage" onclick="switchTab('manage')">All Requests</button>
        </div>

        <!-- ALL REQUESTS LIST -->
        <div class="tab-content active" id="view-manage">
            <div class="table-wrapper">
                <form method="GET" class="table-toolbar" style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                    <div class="search-box" style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 5px; padding: 0 10px; background: #fff; flex-grow: 1; max-width: 300px;">
                        <input type="text" name="search" placeholder="Search requests..." value="<?php echo htmlspecialchars($search); ?>" style="border: none; padding: 8px 10px 8px 30px; outline: none; width: 100%;" onchange="this.form.submit()">
                    </div>
                    <div>
                        <select name="property_category" class="form-control" style="padding: 8px 15px; width: 150px; border-radius: 5px; border: 1px solid var(--border-color);" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <option value="Residential" <?php echo ($property_category == 'Residential') ? 'selected' : ''; ?>>Residential</option>
                            <option value="Commercial" <?php echo ($property_category == 'Commercial') ? 'selected' : ''; ?>>Commercial</option>
                        </select>
                    </div>
                    <?php if (!empty($search) || !empty($property_category)): ?>
                        <a href="estimate_requests.php" style="font-size: 13px; color: var(--text-muted); margin-left: 10px; text-decoration: underline;">Clear</a>
                    <?php endif; ?>
                </form>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client Info</th>
                            <th>Property Type</th>
                            <th>Design Package</th>
                            <th>Est. Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $requests = $conn->query("SELECT * FROM estimate_requests $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
                        if ($requests && $requests->num_rows > 0):
                            while ($row = $requests->fetch_assoc()):
                        ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-details">
                                        <h4 style="font-size: 14px; margin-bottom: 2px;"><?php echo htmlspecialchars($row['name']); ?></h4>
                                        <p style="color: var(--text-muted); font-size: 11px;">
                                            <a href="https://wa.me/91<?php echo htmlspecialchars($row['phone']); ?>" target="_blank" style="color: #25D366; text-decoration: none; margin-right: 3px;"><i class="fa-brands fa-whatsapp"></i></a> 
                                            <?php echo htmlspecialchars($row['phone']); ?> 
                                            • <?php echo htmlspecialchars($row['location']); ?>
                                        </p>
                                        <p style="color: var(--text-muted); font-size: 10px; margin-top:2px;"><?php echo date('d M, Y h:i A', strtotime($row['created_at'])); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--text-main); font-weight:600;"><?php echo htmlspecialchars($row['property_category'] ?: 'N/A'); ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($row['property_type'] ?: 'N/A'); ?></div>
                            </td>
                            <td>
                                <div style="font-size: 12px; color: var(--text-main);"><i class="fa-solid fa-crown" style="color:#F4B41A; margin-right:5px;"></i> <?php echo htmlspecialchars($row['package'] ?: 'N/A'); ?></div>
                                <div style="font-size: 11px; color: var(--text-muted);"><?php echo htmlspecialchars($row['design_style'] ?: 'N/A'); ?></div>
                            </td>
                            <td><strong style="color:var(--sidebar-active);"><?php echo htmlspecialchars($row['estimated_cost'] ?: '₹0'); ?></strong></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this request?');"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">No estimate requests found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 25px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo $base_qs; ?>page=<?php echo $page-1; ?>" class="btn-primary" style="padding: 8px 12px; text-decoration: none; font-size: 13px; background: #fff; color: var(--text-main); border: 1px solid var(--border-color);">&laquo; Prev</a>
                    <?php endif; ?>
                    
                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                        <a href="?<?php echo $base_qs; ?>page=<?php echo $i; ?>" class="btn-primary" style="padding: 8px 12px; text-decoration: none; font-size: 13px; <?php echo ($i == $page) ? 'background: var(--accent-color); color: #fff; border: 1px solid var(--accent-color);' : 'background: #fff; color: var(--text-main); border: 1px solid var(--border-color);'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?php echo $base_qs; ?>page=<?php echo $page+1; ?>" class="btn-primary" style="padding: 8px 12px; text-decoration: none; font-size: 13px; background: #fff; color: var(--text-main); border: 1px solid var(--border-color);">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <div class="admin-footer">
        <div>© 2025 Kalp Interior Design Studio. All rights reserved.</div>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.getElementById('view-' + tabId).classList.add('active');
    
    // Handle tab buttons visibility if needed
    if(tabId === 'manage') {
        document.getElementById('tab-manage').classList.add('active');
    }
}
</script>

<?php include 'includes/footer.php'; ?>

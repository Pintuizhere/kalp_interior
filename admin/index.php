<?php
require_once 'config/db.php';
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Fetch statistics
$total_projects = 0;
$proj_res = $conn->query("SELECT COUNT(*) FROM projects");
if($proj_res) { $total_projects = $proj_res->fetch_row()[0]; }

$total_estimates = 0;
$est_res = $conn->query("SELECT COUNT(*) FROM estimate_requests");
if($est_res) { $total_estimates = $est_res->fetch_row()[0]; }

$total_leads = 0;
$leads_res = $conn->query("SELECT COUNT(*) FROM leads");
if($leads_res) { $total_leads = $leads_res->fetch_row()[0]; }

$total_blogs = 0;
$blogs_res = $conn->query("SELECT COUNT(*) FROM blogs");
if($blogs_res) { $total_blogs = $blogs_res->fetch_row()[0]; }

$total_users = 0;
$users_res = $conn->query("SELECT COUNT(*) FROM admin_users");
if($users_res) { $total_users = $users_res->fetch_row()[0]; }

function get_growth_percentage($conn, $table) {
    $curr_res = $conn->query("SELECT COUNT(*) FROM $table WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $curr_count = $curr_res ? $curr_res->fetch_row()[0] : 0;
    
    $prev_res = $conn->query("SELECT COUNT(*) FROM $table WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
    $prev_count = $prev_res ? $prev_res->fetch_row()[0] : 0;
    
    if ($prev_count == 0) {
        $growth = $curr_count > 0 ? 100 : 0;
    } else {
        $growth = round((($curr_count - $prev_count) / $prev_count) * 100, 1);
    }
    
    return [
        'is_up' => $growth >= 0,
        'formatted' => abs($growth) . '%'
    ];
}

$proj_growth = get_growth_percentage($conn, 'projects');
$leads_growth = get_growth_percentage($conn, 'leads');
$est_growth = get_growth_percentage($conn, 'estimate_requests');
$blogs_growth = get_growth_percentage($conn, 'blogs');
$users_growth = get_growth_percentage($conn, 'admin_users');

// Fetch project categories for chart
$category_counts = [];
$cat_res = $conn->query("SELECT category, COUNT(*) as count FROM projects GROUP BY category");
if($cat_res) {
    while($row = $cat_res->fetch_assoc()) {
        $cat = $row['category'] ?: 'Uncategorized';
        $category_counts[$cat] = $row['count'];
    }
}
$total_proj_for_chart = array_sum($category_counts);
if($total_proj_for_chart == 0) $total_proj_for_chart = 1;

// Define a palette of beautiful colors for the dynamic categories
$chart_colors = ['#334C40', '#EAB136', '#f97316', '#cbd5e1', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6'];
$chart_data = [];
$color_index = 0;
foreach($category_counts as $cat_name => $count) {
    $chart_data[] = [
        'name' => $cat_name,
        'count' => $count,
        'pct' => round(($count / $total_proj_for_chart) * 100, 1),
        'color' => $chart_colors[$color_index % count($chart_colors)]
    ];
    $color_index++;

}

// Fetch Overview Chart Data (Projects vs Leads)
$range = $_GET['range'] ?? '7d';
if ($range == '30d') {
    $num_days = 29;
} else if ($range == 'this_month') {
    $num_days = date('j') - 1; // days since 1st of this month
} else {
    $num_days = 6;
}

$overview_labels = [];
$overview_projects_data = [];
$overview_leads_data = [];

for($i = $num_days; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d M', strtotime("-$i days"));
    $overview_labels[] = $label;
    
    // Count projects for this day
    $p_count = 0;
    $p_res = $conn->query("SELECT COUNT(*) FROM projects WHERE DATE(created_at) = '$date'");
    if($p_res) $p_count = $p_res->fetch_row()[0];
    $overview_projects_data[] = $p_count;
    
    // Count leads for this day
    $l_count = 0;
    $l_res = $conn->query("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = '$date'");
    if($l_res) $l_count = $l_res->fetch_row()[0];
    $overview_leads_data[] = $l_count;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-wrapper">
    <?php include 'includes/topbar.php'; ?>
    
    <div class="main-content">
        
        <div class="date-picker" style="position: relative;">
            <button class="date-picker-btn" onclick="document.getElementById('date-dropdown').classList.toggle('show')">
                <i class="fa-regular fa-calendar"></i>
                <?php 
                if ($range == '30d') {
                    echo date('M d, Y', strtotime('-29 days')) . ' - ' . date('M d, Y');
                } else if ($range == 'this_month') {
                    echo date('M 01, Y') . ' - ' . date('M d, Y');
                } else {
                    echo date('M d, Y', strtotime('-6 days')) . ' - ' . date('M d, Y');
                }
                ?>
                <i class="fa-solid fa-chevron-down" style="font-size: 10px; margin-left: 5px;"></i>
            </button>
            <div id="date-dropdown" class="notif-dropdown" style="display: none; position: absolute; left: 0; top: 110%; background: #fff; width: 100%; min-width: 200px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 1000; border: 1px solid #eee; text-align: left;">
                <a href="?range=7d" style="display: block; padding: 10px 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Last 7 Days</a>
                <a href="?range=30d" style="display: block; padding: 10px 15px; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Last 30 Days</a>
                <a href="?range=this_month" style="display: block; padding: 10px 15px; text-decoration: none; color: #333;">This Month</a>
            </div>
        </div>

        <!-- Dashboard Statistics -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon projects"><i class="fa-solid fa-table-cells-large"></i></div>
                    <div class="stat-title">Total Projects</div>
                </div>
                <div class="stat-info-right">
                    <div class="stat-value"><?php echo $total_projects; ?></div>
                    <div class="stat-trend <?php echo $proj_growth['is_up'] ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fa-solid fa-arrow-trend-<?php echo $proj_growth['is_up'] ? 'up' : 'down'; ?>"></i> 
                        <?php echo $proj_growth['formatted']; ?> <span style="color:var(--text-muted); font-weight:400;">from last month</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon leads"><i class="fa-solid fa-user-group"></i></div>
                    <div class="stat-title">Total Leads</div>
                </div>
                <div class="stat-info-right">
                    <div class="stat-value"><?php echo $total_leads; ?></div>
                    <div class="stat-trend <?php echo $leads_growth['is_up'] ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fa-solid fa-arrow-trend-<?php echo $leads_growth['is_up'] ? 'up' : 'down'; ?>"></i> 
                        <?php echo $leads_growth['formatted']; ?> <span style="color:var(--text-muted); font-weight:400;">from last month</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon estimates"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="stat-title">Estimate Requests</div>
                </div>
                <div class="stat-info-right">
                    <div class="stat-value"><?php echo $total_estimates; ?></div>
                    <div class="stat-trend <?php echo $est_growth['is_up'] ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fa-solid fa-arrow-trend-<?php echo $est_growth['is_up'] ? 'up' : 'down'; ?>"></i> 
                        <?php echo $est_growth['formatted']; ?> <span style="color:var(--text-muted); font-weight:400;">from last month</span>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon blogs"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div class="stat-title">Blog Posts</div>
                </div>
                <div class="stat-info-right">
                    <div class="stat-value"><?php echo $total_blogs; ?></div>
                    <div class="stat-trend <?php echo $blogs_growth['is_up'] ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fa-solid fa-arrow-trend-<?php echo $blogs_growth['is_up'] ? 'up' : 'down'; ?>"></i> 
                        <?php echo $blogs_growth['formatted']; ?> <span style="color:var(--text-muted); font-weight:400;">from last month</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon users"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-title">Total Users</div>
                </div>
                <div class="stat-info-right">
                    <div class="stat-value"><?php echo $total_users; ?></div>
                    <div class="stat-trend <?php echo $users_growth['is_up'] ? 'trend-up' : 'trend-down'; ?>">
                        <i class="fa-solid fa-arrow-trend-<?php echo $users_growth['is_up'] ? 'up' : 'down'; ?>"></i> 
                        <?php echo $users_growth['formatted']; ?> <span style="color:var(--text-muted); font-weight:400;">from last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Grid -->
        <div class="dashboard-grid">
            
            <!-- Overview Chart -->
            <div class="card-panel">
                <div class="card-header">
                    <h3 class="card-title">Overview</h3>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-color" style="background: #334C40;"></div> Projects
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background: #EAB136;"></div> Leads
                        </div>
                        <select class="chart-select">
                            <option>This Month</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="overviewChart"></canvas>
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="card-panel">
                <div class="card-header">
                    <h3 class="card-title">Recent Estimate Requests</h3>
                    <a href="estimate_requests.php" class="card-action">View All</a>
                </div>
                <div class="list-container">
                    <?php
                    $recent_requests = $conn->query("SELECT * FROM estimate_requests ORDER BY created_at DESC LIMIT 5");
                    if ($recent_requests && $recent_requests->num_rows > 0):
                        while($req = $recent_requests->fetch_assoc()):
                    ?>
                    <div class="list-item">
                        <div class="user-info">
                            <div class="user-details">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 2px; color: var(--text-main);"><?php echo htmlspecialchars($req['name']); ?></h4>
                                <p style="font-size: 12px; color: var(--text-muted);"><?php echo htmlspecialchars($req['property_category']); ?> • <?php echo htmlspecialchars($req['estimated_cost']); ?></p>
                            </div>
                        </div>
                        <div class="item-meta">
                            <span class="pill new">New</span>
                            <span class="time-ago" style="font-size: 11px;"><?php echo date('M d, g:i a', strtotime($req['created_at'])); ?></span>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">No recent requests found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="dashboard-bottom-grid">
            
            <!-- Project Categories -->
            <div class="card-panel">
                <div class="card-header">
                    <h3 class="card-title">Project Categories</h3>
                </div>
                <div class="donut-container">
                    <div style="position: relative; width: 140px; height: 140px;">
                        <canvas id="statusChart"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <div style="font-size: 20px; font-weight: 700; color: var(--text-main); font-family: var(--font-primary);"><?php echo $total_projects; ?></div>
                            <div style="font-size: 11px; color: var(--text-muted);">Total</div>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 11px;">
                        <?php foreach($chart_data as $cat_data): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; width: 120px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $cat_data['color']; ?>;"></div> 
                                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 60px;" title="<?php echo htmlspecialchars($cat_data['name']); ?>">
                                    <?php echo htmlspecialchars($cat_data['name']); ?>
                                </span>
                            </div>
                            <span style="color: var(--text-muted);"><?php echo $cat_data['count']; ?> (<?php echo $cat_data['pct']; ?>%)</span>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($chart_data)): ?>
                            <div style="color: var(--text-muted);">No categories</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Latest Projects -->
            <div class="card-panel">
                <div class="card-header">
                    <h3 class="card-title">Latest Projects</h3>
                    <a href="#" class="card-action">View All</a>
                </div>
                <div class="list-container">
                    <?php
                    $latest_projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 4");
                    if ($latest_projects && $latest_projects->num_rows > 0):
                        while($proj = $latest_projects->fetch_assoc()):
                            $cat = $proj['category'] ?: 'Uncategorized';
                            // Assign color based on category position in chart colors
                            $color_code = '#3b82f6'; // default blue
                            foreach($chart_data as $cd) {
                                if($cd['name'] == $cat) { $color_code = $cd['color']; break; }
                            }
                    ?>
                    <div class="list-item">
                        <div class="project-item">
                            <?php 
                            if(!empty($proj['cover_image'])) {
                                $img_src = strpos($proj['cover_image'], 'http') === 0 ? $proj['cover_image'] : '../' . $proj['cover_image'];
                            } else {
                                $img_src = 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=100&h=80&fit=crop';
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($img_src); ?>" class="project-thumb" alt="Project" style="object-fit: cover;">
                            <div class="user-details">
                                <h4><?php echo htmlspecialchars($proj['title']); ?></h4>
                                <p><?php echo htmlspecialchars($proj['location'] ?? 'Location N/A'); ?></p>
                            </div>
                        </div>
                        <div class="progress-meta">
                            <span style="background: <?php echo $color_code; ?>15; color: <?php echo $color_code; ?>; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                <?php echo htmlspecialchars($cat); ?>
                            </span>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">No projects found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-panel">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="quick-actions-grid">
                    <a href="editor-project.php" class="action-box">
                        <i class="fa-solid fa-square-plus"></i>
                        <span>Add New Project</span>
                    </a>
                    <a href="editor-blog.php" class="action-box">
                        <i class="fa-solid fa-pen-to-square" style="color: #EAB136;"></i>
                        <span>Add Blog Post</span>
                    </a>
                    <a href="leads.php" class="action-box">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Add New Lead</span>
                    </a>
                    <a href="estimate_requests.php" class="action-box">
                        <i class="fa-solid fa-file-invoice-dollar" style="color: #EAB136;"></i>
                        <span>Estimate Requests</span>
                    </a>
                    <a href="media.php" class="action-box">
                        <i class="fa-solid fa-image"></i>
                        <span>Media Library</span>
                    </a>
                    <a href="leads.php" class="action-box">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span>View Messages</span>
                    </a>
                </div>
            </div>
            
        </div>

    </div>

    <!-- Simple Footer inside Main Content -->
    <div class="admin-footer">
        <div>© 2025 Kalp Interior Design Studio. All rights reserved.</div>
    </div>

</div>

<!-- Chart Initialization Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Overview Line Chart
    const ctxOverview = document.getElementById('overviewChart');
    if(ctxOverview) {
        new Chart(ctxOverview, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($overview_labels); ?>,
                datasets: [
                    {
                        label: 'Projects',
                        data: <?php echo json_encode($overview_projects_data); ?>,
                        borderColor: '#334C40',
                        backgroundColor: 'rgba(51, 76, 64, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#334C40',
                        pointRadius: 3
                    },
                    {
                        label: 'Leads',
                        data: <?php echo json_encode($overview_leads_data); ?>,
                        borderColor: '#EAB136',
                        backgroundColor: 'rgba(234, 177, 54, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#EAB136',
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Project Categories Donut Chart
    const ctxStatus = document.getElementById('statusChart');
    if(ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($chart_data, 'name')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($chart_data, 'pct')); ?>,
                    backgroundColor: <?php echo json_encode(array_column($chart_data, 'color')); ?>,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>

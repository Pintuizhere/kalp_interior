<?php
$pageTitle = 'News & Offers';
$currentPage = 'news_offers';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'delete') $success_msg = "Item deleted successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Optional: Fetch image to delete from server if needed
    // $stmt = $conn->prepare("SELECT image FROM news_offers WHERE id = ?"); ...
    
    $stmt = $conn->prepare("DELETE FROM news_offers WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: manage_news_offers.php?success=delete");
        exit;
    } else {
        $error_msg = "Failed to delete item.";
    }
    $stmt->close();
}

include 'includes/header.php';
include 'includes/sidebar.php';

// Fetch news_offers
$news_query = "SELECT * FROM news_offers ORDER BY created_at DESC";
$news_result = $conn->query($news_query);

?>

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

        <div class="page-header">
            <h1>Manage News & Offers</h1>
            <a href="editor-news-offers.php" class="btn-primary" style="text-decoration: none; display: inline-block;">
                <i class="fa-solid fa-plus"></i> Add New
            </a>
        </div>

        <div id="view-manage" class="tab-content active">
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item Info</th>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($news_result && $news_result->num_rows > 0): ?>
                            <?php while($item = $news_result->fetch_assoc()): 
                                $date = date('M d, Y', strtotime($item['created_at']));
                                $status_class = ($item['status'] == 'Published') ? 'new' : 'progress';
                            ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <?php if(!empty($item['image'])): ?>
                                            <img src="../uploads/news/<?php echo htmlspecialchars($item['image']); ?>" class="user-avatar" alt="Image" style="object-fit:cover; border-radius:5px;">
                                        <?php else: ?>
                                            <div class="user-avatar" style="background:var(--primary-color); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:5px;">
                                                <i class="fa-solid fa-bullhorn"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="user-details">
                                            <h4 style="font-size: 14px; margin-bottom: 2px;"><?php echo htmlspecialchars($item['title']); ?></h4>
                                            <p style="color: var(--text-muted); font-size: 11px;"><?php echo $date; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td><code style="background:#f0f0f0; padding:2px 5px; border-radius:3px; font-size:12px;"><?php echo htmlspecialchars($item['slug']); ?></code></td>
                                <td><span class="pill <?php echo $status_class; ?>"><?php echo htmlspecialchars($item['status']); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="editor-news-offers.php?edit=<?php echo $item['id']; ?>" class="btn-icon"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a href="?delete=<?php echo $item['id']; ?>" class="btn-icon delete"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 20px;">No items found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php include 'includes/footer.php'; ?>

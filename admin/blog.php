<?php
$pageTitle = 'Blog Posts';
$currentPage = 'blog';
require_once 'config/db.php';

$success_msg = '';
$error_msg = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'delete') $success_msg = "Blog post deleted successfully!";
    if ($_GET['success'] == 'insert') $success_msg = "Blog post published successfully!";
    if ($_GET['success'] == 'update') $success_msg = "Blog post updated successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Optional: Fetch image to delete from server if needed
    // $stmt = $conn->prepare("SELECT image FROM blogs WHERE id = ?"); ...
    
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: blog.php?success=delete");
        exit;
    } else {
        $error_msg = "Failed to delete blog post.";
    }
    $stmt->close();
}

// Handle POST request logic has been moved to editor-blog.php

include 'includes/header.php';
include 'includes/sidebar.php';
// Pagination setup
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total blogs
$count_query = "SELECT COUNT(*) as total FROM blogs";
$count_result = $conn->query($count_query);
$total_blogs = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_blogs / $limit);

// Fetch blogs
$blogs_query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$blogs_result = $conn->query($blogs_query);

// Fetch categories for the dropdown
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_query);
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
            <h1>Manage Blog Posts</h1>
            <a href="editor-blog.php" class="btn-primary" style="text-decoration: none; display: inline-block;">
                <i class="fa-solid fa-plus"></i> Add New Blog
            </a>
        </div>

        <!-- MANAGE BLOGS VIEW -->
        <div id="view-manage" class="tab-content active">
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Blog Info</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($blogs_result && $blogs_result->num_rows > 0): ?>
                            <?php while($blog = $blogs_result->fetch_assoc()): 
                                $date = date('M d, Y', strtotime($blog['created_at']));
                                $status_class = ($blog['status'] == 'Published') ? 'new' : 'progress';
                            ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <?php if(!empty($blog['image'])): ?>
                                            <img src="../uploads/blogs/<?php echo $blog['image']; ?>" class="user-avatar" alt="Blog Image" style="object-fit:cover; border-radius:5px;">
                                        <?php else: ?>
                                            <div class="user-avatar" style="background:var(--primary-color); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:5px;">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="user-details">
                                            <h4 style="font-size: 14px; margin-bottom: 2px;"><?php echo htmlspecialchars($blog['title']); ?></h4>
                                            <p style="color: var(--text-muted); font-size: 11px;"><?php echo $date; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                <td><?php echo htmlspecialchars($blog['category']); ?></td>
                                <td><span class="pill <?php echo $status_class; ?>"><?php echo htmlspecialchars($blog['status']); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="editor-blog.php?edit=<?php echo $blog['id']; ?>" class="btn-icon"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a href="?delete=<?php echo $blog['id']; ?>" class="btn-icon delete"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 20px;">No blog posts found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="pagination" style="display: flex; justify-content: flex-end; gap: 5px; margin-top: 20px; padding-right: 25px; margin-bottom: 25px;">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-dark); text-decoration: none; font-size: 13px;"><i class="fa-solid fa-angle-left"></i></a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" style="padding: 6px 12px; border: 1px solid <?php echo ($i == $page) ? 'var(--accent-color)' : 'var(--border-color)'; ?>; background: <?php echo ($i == $page) ? 'var(--accent-color)' : 'transparent'; ?>; border-radius: 4px; color: var(--text-dark); text-decoration: none; font-size: 13px; font-weight: <?php echo ($i == $page) ? 'bold' : 'normal'; ?>;"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" style="padding: 6px 12px; border: 1px solid var(--border-color); border-radius: 4px; color: var(--text-dark); text-decoration: none; font-size: 13px;"><i class="fa-solid fa-angle-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        </div>



<?php include 'includes/footer.php'; ?>

<?php
$userName = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User';
$userEmail = isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '';
$userInitial = strtoupper(substr($userName, 0, 1));

// Fetch user addresses from db
$addresses = [];
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $addr_res = $conn->query("SELECT * FROM shop_user_addresses WHERE user_id = $uid ORDER BY is_default DESC, id DESC");
    if ($addr_res) {
        while($row = $addr_res->fetch_assoc()) {
            $addresses[] = $row;
        }
    }
}
?>
        <!-- Logged In UI -->
        <div class="up-logged-in" style="display: flex; flex-direction: column; height: 100%;">
            <div class="up-header">
                <div class="up-header-top">
                    <h2 class="up-brand">kalp interior</h2>
                    <div style="display: flex; align-items: center;">
                        <div class="up-avatar"><?php echo $userInitial; ?></div>
                        <button class="up-close-btn white" style="position: relative; top: 0; right: 0;" onclick="closeUserPanel()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
                <div class="up-tabs">
                    <button class="up-tab" id="tab-orders" onclick="switchUpTab('orders')">Orders</button>
                    <button class="up-tab active" id="tab-profile" onclick="switchUpTab('profile')">Profile</button>
                </div>
            </div>
            
            <div class="up-body">
                <!-- Profile Tab -->
                <div class="up-tab-content active" id="content-profile">
                    <div class="up-section-title">
                        <h3 style="text-transform: uppercase;"><?php echo $userName; ?></h3>
                        <button class="up-btn-small">Edit</button>
                    </div>
                    
                    <div class="up-card">
                        <div class="up-card-row">
                            <span class="up-label">Email</span>
                            <span class="up-value"><?php echo $userEmail; ?></span>
                        </div>
                    </div>

                    <div class="up-section-title" style="margin-top: 30px;">
                        <h3 style="text-transform: uppercase;">Addresses</h3>
                        <button class="up-btn-small">Add</button>
                    </div>

                    <?php if (count($addresses) > 0): ?>
                    <div class="up-card address-card">
                        <?php foreach($addresses as $index => $addr): ?>
                        <div class="up-address-item">
                            <div class="up-address-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="up-address-details">
                                <h4><?php echo htmlspecialchars($addr['name']); ?> <?php if($addr['is_default']) echo '<span>DEFAULT</span>'; ?></h4>
                                <p><?php echo nl2br(htmlspecialchars($addr['address'])); ?></p>
                            </div>
                            <div class="up-address-arrow"><i class="fa-solid fa-chevron-right"></i></div>
                        </div>
                        <?php if($index < count($addresses) - 1) echo '<div class="up-address-divider"></div>'; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="up-card" style="padding: 20px; text-align: center; color: var(--text-gray);">
                        No addresses saved yet.
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" style="margin-top: 40px; text-align: center;">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="up-btn-logout">Log out</button>
                    </form>
                </div>

                <!-- Orders Tab -->
                <div class="up-tab-content" id="content-orders">
                    <div class="up-empty-state">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <p>You haven't placed any orders yet.</p>
                    </div>
                    <!-- Example empty state since order fetching by email isn't fully set up yet -->
                </div>

            </div>
            
            <div class="up-footer" style="margin-top: auto;">
                <a href="returns-policy.php">Returns Policy</a>
                <a href="shipping.php">Shipping</a>
                <a href="privacy-policy.php">Privacy policy</a>
                <a href="terms-conditions.php">Terms of service</a>
            </div>
        </div>

<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = 'checkout';
require_once 'admin/config/db.php';

// If cart is empty, redirect to shop
if (empty($_SESSION['shop_cart'])) {
    header("Location: shop-furniture.php");
    exit;
}

$cart_items = [];
$subtotal = 0;
$discount = 2000; // Fixed discount as per invoice image
$shipping = 0; // Free shipping as per invoice image

if (!empty($_SESSION['shop_cart'])) {
    $ids = implode(',', array_keys($_SESSION['shop_cart']));
    $query = "SELECT p.*, (SELECT image_url FROM shop_product_images WHERE product_id = p.id AND is_primary=1 LIMIT 1) as main_image FROM shop_products p WHERE p.id IN ($ids)";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        $qty = $_SESSION['shop_cart'][$row['id']];
        $price = $row['sale_price'] ? $row['sale_price'] : $row['regular_price'];
        $total = $price * $qty;
        $subtotal += $total;
        $row['qty'] = $qty;
        $row['cart_price'] = $price;
        $row['cart_total'] = $total;
        $cart_items[] = $row;
    }
}
$total_amount = $subtotal - $discount + $shipping;

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $billing_address = $conn->real_escape_string($_POST['billing_address']);
    $shipping_address = isset($_POST['same_address']) ? $billing_address : $conn->real_escape_string($_POST['shipping_address']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    
    $order_number = 'WN' . date('YmdHis') . rand(10,99);
    
    // Insert Order
    $stmt = $conn->prepare("INSERT INTO shop_orders (order_number, customer_name, customer_email, customer_phone, billing_address, shipping_address, subtotal, discount, shipping_charges, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdddds", $order_number, $name, $email, $phone, $billing_address, $shipping_address, $subtotal, $discount, $shipping, $total_amount, $payment_method);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Insert Order Items
        $item_stmt = $conn->prepare("INSERT INTO shop_order_items (order_id, product_id, product_name, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)");
        foreach($cart_items as $item) {
            $item_stmt->bind_param("iisidd", $order_id, $item['id'], $item['name'], $item['qty'], $item['cart_price'], $item['cart_total']);
            $item_stmt->execute();
        }
        $item_stmt->close();
        
        // Clear cart
        $_SESSION['shop_cart'] = [];
        
        // Redirect to Invoice
        header("Location: invoice.php?order_id=" . $order_number);
        exit;
    }
}

include 'includes/header.php'; 
?>

<style>
    :root {
        --brand-green: #0d3b2e;
        --brand-light-green: #1a5944;
        --bg-color: #fcfaf8;
        --card-shadow: 0 12px 40px rgba(0,0,0,0.04);
        --input-border: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }
    
    body { background: var(--bg-color); font-family: 'Outfit', sans-serif; color: var(--text-dark); }
    
    .checkout-header { text-align: center; margin: 40px 0 20px; }
    .checkout-header h1 { font-size: 2.5rem; font-weight: 700; color: var(--brand-green); margin-bottom: 10px; }
    .checkout-header p { color: var(--text-muted); font-size: 1.1rem; }

    .checkout-container { max-width: 1200px; margin: 0 auto 100px; padding: 0 5%; display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; align-items: start; }
    
    .checkout-card { background: white; padding: 40px; border-radius: 20px; box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.02); }
    
    .section-title { font-size: 1.4rem; font-weight: 600; margin: 0 0 25px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }
    .section-title i { color: var(--brand-green); }
    
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group.full-width { grid-column: span 2; }
    
    .form-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.95rem; color: #475569; }
    .form-control { width: 100%; padding: 14px 16px; border: 1.5px solid var(--input-border); border-radius: 10px; font-family: inherit; font-size: 1rem; color: var(--text-dark); transition: all 0.3s; background: #f8fafc; outline: none; box-sizing: border-box; }
    .form-control:focus { border-color: var(--brand-green); background: white; box-shadow: 0 0 0 4px rgba(13,59,46,0.1); }
    
    .checkbox-wrapper { display: flex; align-items: center; gap: 12px; margin-top: 10px; padding: 15px; background: #f8fafc; border-radius: 10px; border: 1px solid var(--input-border); cursor: pointer; }
    .checkbox-wrapper input { width: 20px; height: 20px; accent-color: var(--brand-green); cursor: pointer; }
    .checkbox-wrapper label { margin: 0; cursor: pointer; font-weight: 500; color: var(--text-dark); }
    
    .payment-method-box { border: 1.5px solid var(--brand-green); border-radius: 10px; padding: 15px 20px; background: rgba(13,59,46,0.03); display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
    .payment-method-box i { font-size: 1.5rem; color: var(--brand-green); }
    .payment-method-box select { flex: 1; background: transparent; border: none; font-size: 1.05rem; font-weight: 600; color: var(--brand-green); outline: none; font-family: inherit; cursor: pointer; }
    
    .secure-badge { display: flex; align-items: center; gap: 10px; justify-content: center; margin-top: 25px; color: var(--text-muted); font-size: 0.9rem; }
    .secure-badge i { color: #10b981; font-size: 1.1rem; }

    /* Order Summary Styles */
    .order-summary { position: sticky; top: 100px; }
    
    .cart-item { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
    .cart-item-img { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; background: #f8fafc; }
    .cart-item-info { flex: 1; }
    .cart-item-info h4 { margin: 0 0 8px; font-size: 1.05rem; font-weight: 600; color: var(--text-dark); }
    .cart-qty-controls { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .cart-qty-controls a { display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #e2e8f0; border-radius: 4px; color: var(--text-dark); text-decoration: none; font-weight: bold; }
    .cart-qty-controls a:hover { background: #cbd5e1; }
    .cart-qty-controls span { font-size: 0.95rem; font-weight: 500; }
    .cart-item-price { font-weight: 700; font-size: 1.1rem; color: var(--brand-green); }
    
    .totals-wrapper { background: #f8fafc; padding: 25px; border-radius: 16px; margin-top: 30px; }
    .totals-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1rem; color: var(--text-muted); font-weight: 500; }
    .totals-row.grand-total { font-size: 1.4rem; font-weight: 700; border-top: 2px dashed #cbd5e1; padding-top: 20px; margin-top: 10px; color: var(--text-dark); margin-bottom: 0; }
    
    .btn-checkout { background: var(--brand-green); color: white; border: none; width: 100%; padding: 18px; border-radius: 12px; font-weight: 700; font-size: 1.15rem; cursor: pointer; transition: all 0.3s; margin-top: 30px; display: flex; align-items: center; justify-content: center; gap: 10px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 10px 20px rgba(13,59,46,0.2); }
    .btn-checkout:hover { background: #114d3b; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(13,59,46,0.3); }

    @media (max-width: 991px) {
        .checkout-container { grid-template-columns: 1fr; }
        .order-summary { position: relative; top: 0; order: -1; } /* Show summary first on mobile */
    }
</style>

<div class="checkout-header">
    <h1>Secure Checkout</h1>
    <p>Please review your order and enter your details below.</p>
</div>

<div class="checkout-container">
    <div class="checkout-card">
        <form action="checkout.php" method="POST" id="checkoutForm">
            <input type="hidden" name="place_order" value="1">
            
            <h2 class="section-title"><i class="fa-solid fa-user-circle"></i> Contact Information</h2>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Aman Sharma" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" required>
                </div>
            </div>

            <h2 class="section-title" style="margin-top: 40px;"><i class="fa-solid fa-map-location-dot"></i> Shipping Details</h2>
            <div class="form-group full-width">
                <label>Complete Shipping Address</label>
                <textarea name="shipping_address" class="form-control" rows="3" placeholder="House/Flat No., Street, Landmark, City, State, Pincode" required></textarea>
            </div>

            <label class="checkbox-wrapper">
                <input type="checkbox" name="same_address" id="same_address" checked>
                Billing address is the same as shipping address
            </label>

            <div id="billing_box" style="display:none; margin-top: 20px;">
                <div class="form-group full-width">
                    <label>Billing Address</label>
                    <textarea name="billing_address" id="billing_address" class="form-control" rows="3" placeholder="Billing Address"></textarea>
                </div>
            </div>

            <h2 class="section-title" style="margin-top: 40px;"><i class="fa-solid fa-wallet"></i> Payment Method</h2>
            <div class="payment-method-box">
                <i class="fa-brands fa-whatsapp"></i>
                <select name="payment_method" required>
                    <option value="UPI">Pay via UPI / WhatsApp Order</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>
            </div>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-top: 10px;">
                <i class="fa-solid fa-circle-info" style="color:var(--brand-green);"></i> Upon placing the order, you will receive an automated invoice on your WhatsApp number to complete the payment securely.
            </p>
        </form>
    </div>
    
    <div class="checkout-card order-summary">
        <h2 class="section-title"><i class="fa-solid fa-bag-shopping"></i> Order Summary</h2>
        
        <div class="cart-items">
            <?php foreach($cart_items as $item): ?>
            <div class="cart-item">
                <img src="assets/images/products/<?php echo $item['main_image'] ? htmlspecialchars($item['main_image']) : 'furniture_sofa.jpg'; ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=200&q=80'" class="cart-item-img">
                <div class="cart-item-info">
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <div class="cart-qty-controls">
                        <span style="font-size: 0.9rem; color: var(--text-muted); margin-right: 5px;">Qty:</span>
                        <a href="cart_action.php?action=decrease&id=<?php echo $item['id']; ?>">-</a>
                        <span><?php echo $item['qty']; ?></span>
                        <a href="cart_action.php?action=add&id=<?php echo $item['id']; ?>&qty=1&redirect=checkout">+</a>
                    </div>
                    <a href="cart_action.php?action=remove&id=<?php echo $item['id']; ?>" style="color:#ef4444; font-size:0.85rem; font-weight: 500; text-decoration:none;"><i class="fa-solid fa-trash-can"></i> Remove</a>
                </div>
                <div class="cart-item-price">₹<?php echo number_format($item['cart_total']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="totals-wrapper">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($subtotal); ?></span>
            </div>
            <div class="totals-row">
                <span>Special Discount</span>
                <span style="color:#10b981; font-weight:600;">- ₹<?php echo number_format($discount); ?></span>
            </div>
            <div class="totals-row">
                <span>Shipping Charges</span>
                <span style="color:var(--text-dark);"><?php echo $shipping == 0 ? 'Free Delivery' : '₹'.number_format($shipping); ?></span>
            </div>
            <div class="totals-row grand-total">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total_amount); ?></span>
            </div>
        </div>
        
        <button type="button" class="btn-checkout" onclick="document.getElementById('checkoutForm').submit();">
            Place Order via WhatsApp <i class="fa-brands fa-whatsapp" style="font-size: 1.2em;"></i>
        </button>

        <div class="secure-badge">
            <i class="fa-solid fa-lock"></i> 100% Secure Checkout Guaranteed
        </div>
    </div>
</div>

<script>
    // Handle Same Address Checkbox
    document.getElementById('same_address').addEventListener('change', function() {
        var billingBox = document.getElementById('billing_box');
        var billingInput = document.getElementById('billing_address');
        if(this.checked) {
            billingBox.style.display = 'none';
            billingInput.required = false;
        } else {
            billingBox.style.display = 'block';
            billingInput.required = true;
        }
    });
</script>

<?php include 'includes/footer.php'; ?>

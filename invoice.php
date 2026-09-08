<?php
require_once 'admin/config/db.php';

$order_id = isset($_GET['order_id']) ? $conn->real_escape_string($_GET['order_id']) : '';

if(empty($order_id)) {
    die("Invalid Order ID");
}

// Fetch Order
$stmt = $conn->prepare("SELECT * FROM shop_orders WHERE order_number = ?");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$order) {
    die("Order not found.");
}

// Fetch Order Items
$items_query = "SELECT i.*, p.sku, (SELECT image_url FROM shop_product_images WHERE product_id = p.id AND is_primary=1 LIMIT 1) as image, c.name as category_name FROM shop_order_items i LEFT JOIN shop_products p ON i.product_id = p.id LEFT JOIN shop_categories c ON p.category_id = c.id WHERE i.order_id = " . $order['id'];
$items_result = $conn->query($items_query);
$items = [];
while($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

// Format Phone for WhatsApp
$wa_phone = preg_replace('/[^0-9]/', '', $order['customer_phone']);
if (strlen($wa_phone) == 10) {
    $wa_phone = '91' . $wa_phone; // default India code if none
}

// Prepare WhatsApp Message
$wa_text = "🎉 Thank you for your purchase!\n";
$wa_text .= "Your order " . $order['order_number'] . " has been confirmed.\n\n";
$wa_text .= "💰 Amount Paid: ₹" . number_format($order['total_amount']) . "\n";
$delivery_start = date('d', strtotime('+3 days'));
$delivery_end = date('d M', strtotime('+5 days'));
$wa_text .= "📦 Expected Delivery: $delivery_start–$delivery_end\n\n";
$wa_text .= "📄 Your invoice is detailed below:\n\n";

$wa_text .= "WOODNEST FURNITURE\n";
$wa_text .= "                 INVOICE\n\n";
$wa_text .= "Order ID: " . $order['order_number'] . "\n";
$wa_text .= "Order Date: " . date('d M Y') . "\n";
$wa_text .= "Payment: " . $order['payment_method'] . " - PAID\n\n";
$wa_text .= "BILLING ADDRESS\n";
$wa_text .= $order['customer_name'] . "\n";
$wa_text .= $order['billing_address'] . "\n";
$wa_text .= "Phone: " . $order['customer_phone'] . "\n\n";

$wa_text .= "SHIPPING ADDRESS\n";
$wa_text .= $order['customer_name'] . "\n";
$wa_text .= $order['shipping_address'] . "\n\n";

$wa_text .= "-----------------------------------------\n";
$wa_text .= "Product          Qty     Total\n";
foreach($items as $item) {
    $wa_text .= $item['product_name'] . "       " . $item['quantity'] . "     ₹" . number_format($item['total']) . "\n";
}
$wa_text .= "-----------------------------------------\n";
$wa_text .= "Subtotal                         ₹" . number_format($order['subtotal']) . "\n";
$wa_text .= "Discount                        -₹" . number_format($order['discount']) . "\n";
$wa_text .= "Shipping                            ₹" . number_format($order['shipping_charges']) . "\n";
$wa_text .= "-----------------------------------------\n";
$wa_text .= "TOTAL                            ₹" . number_format($order['total_amount']) . "\n";
$wa_text .= "-----------------------------------------\n\n";
$wa_text .= "Thank you for shopping with us!";
$wa_url = "https://api.whatsapp.com/send?phone=" . $wa_phone . "&text=" . rawurlencode($wa_text);

// Show the invoice or redirect to WA if a param is passed
if (isset($_GET['wa_redirect'])) {
    header("Location: " . $wa_url);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order['order_number']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand-color: #0d3b2e;
            --brand-light: #f0f7f4;
            --text-dark: #1f2937;
            --text-gray: #4b5563;
            --border-color: #e5e7eb;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            color: var(--text-dark);
        }
        .invoice-box {
            max-width: 900px;
            margin: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            position: relative;
        }
        
        /* Header */
        .invoice-header {
            background-color: var(--brand-color);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }
        .brand-info {
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 2;
        }
        .brand-logo {
            font-size: 3rem;
            color: white;
        }
        .brand-text h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        .brand-text p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            opacity: 0.8;
            letter-spacing: 1px;
        }
        
        .invoice-meta {
            text-align: right;
            z-index: 2;
        }
        .invoice-meta h2 {
            margin: 0 0 15px;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-meta p {
            margin: 5px 0;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }
        .status-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 10px;
            font-weight: 500;
        }

        /* Abstract Shape */
        .header-shape {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 40%;
            background-image: url('assets/images/furniture_living.jpg');
            background-size: cover;
            background-position: center;
            border-radius: 50% 0 0 50% / 100% 0 0 100%;
            opacity: 0.4;
            mix-blend-mode: overlay;
        }

        .invoice-body {
            padding: 40px;
        }

        /* Addresses */
        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .address-box h3 {
            color: var(--brand-color);
            margin: 0 0 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        .address-text {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-gray);
        }
        .address-text strong {
            color: var(--text-dark);
            font-size: 1.05rem;
        }

        /* Order Details Table */
        .details-title {
            color: var(--brand-color);
            margin: 0 0 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th {
            background-color: var(--brand-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .invoice-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .product-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .product-img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            background: #f9f9f9;
        }
        .product-name {
            font-weight: 600;
            margin: 0 0 5px;
            color: var(--text-dark);
        }
        .product-meta {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin: 0;
        }

        /* Totals Area */
        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .delivery-info {
            background: var(--brand-light);
            padding: 20px;
            border-radius: 8px;
        }
        .delivery-info h4 { margin: 0 0 10px; color: var(--brand-color); display: flex; align-items: center; gap: 10px;}
        .delivery-info p { margin: 5px 0; font-size: 0.9rem; color: var(--text-gray); }

        .totals-box {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: var(--text-gray);
        }
        .total-row.grand-total {
            background: var(--brand-color);
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Footer */
        .invoice-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        .payment-status {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .payment-icon {
            background: var(--brand-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .payment-text h4 { margin: 0 0 5px; font-size: 1rem; }
        .payment-text p { margin: 0; font-size: 0.85rem; color: var(--text-gray); }

        .signature {
            font-family: 'Playfair Display', serif;
            color: var(--brand-color);
            text-align: right;
            font-size: 1.5rem;
            font-style: italic;
        }

        /* Bottom Bar */
        .bottom-bar {
            background: var(--brand-color);
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            border-radius: 0 0 12px 12px;
        }
        .bottom-bar a { color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;}

        /* Action Buttons */
        .print-btn, .wa-btn {
            display: block;
            width: 250px;
            margin: 20px auto 0;
            background: var(--brand-color);
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            border: none;
        }
        .wa-btn {
            background: #25D366;
        }

        @media print {
            body { padding: 0; background: white; }
            .invoice-box { box-shadow: none; border: none; }
            .print-btn, .wa-btn { display: none; }
            .header-shape { display: none; }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <div class="invoice-header">
            <div class="header-shape"></div>
            <div class="brand-info">
                <i class="fa-solid fa-chair brand-logo"></i>
                <div class="brand-text">
                    <h1>WoodNest</h1>
                    <p>FURNITURE</p>
                    <p style="text-transform: none; font-size: 0.8rem; margin-top: 2px;">Better Homes. Happier Lives.</p>
                </div>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <p>Order ID: <?php echo htmlspecialchars($order['order_number']); ?></p>
                <p>Order Date: <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                <p>Payment Method: <?php echo htmlspecialchars($order['payment_method']); ?> (Paid)</p>
                <div class="status-badge">Status: <?php echo htmlspecialchars($order['status']); ?></div>
            </div>
        </div>

        <div class="invoice-body">
            <div class="address-grid">
                <div class="address-box">
                    <h3><i class="fa-solid fa-user"></i> Billing Address</h3>
                    <div class="address-text">
                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($order['billing_address'])); ?><br>
                        <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                        <?php echo htmlspecialchars($order['customer_email']); ?>
                    </div>
                </div>
                <div class="address-box">
                    <h3><i class="fa-solid fa-truck"></i> Shipping Address</h3>
                    <div class="address-text">
                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                        <?php echo htmlspecialchars($order['customer_phone']); ?>
                    </div>
                </div>
            </div>

            <h3 class="details-title"><i class="fa-solid fa-box-open"></i> Order Details</h3>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="45%">Product</th>
                        <th width="15%" style="text-align: center;">Qty</th>
                        <th width="15%" style="text-align: right;">Price (₹)</th>
                        <th width="20%" style="text-align: right;">Total (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach($items as $item): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <div class="product-cell">
                                <img src="assets/images/products/<?php echo $item['image'] ? htmlspecialchars($item['image']) : 'furniture_sofa.jpg'; ?>" class="product-img" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=100&q=80'">
                                <div>
                                    <h4 class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                    <p class="product-meta"><?php echo htmlspecialchars($item['category_name']); ?> | SKU: <?php echo htmlspecialchars($item['sku'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">₹ <?php echo number_format($item['price']); ?></td>
                        <td style="text-align: right; font-weight: 500;">₹ <?php echo number_format($item['total']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="totals-grid">
                <div class="delivery-info">
                    <h4><i class="fa-solid fa-truck-fast"></i> Delivery Information</h4>
                    <?php
                        $date = new DateTime($order['created_at']);
                        $date->modify('+2 days');
                        $del_start = $date->format('d M Y');
                        $date->modify('+2 days');
                        $del_end = $date->format('d M Y');
                    ?>
                    <p>Expected Delivery: <?php echo $del_start; ?> - <?php echo $del_end; ?></p>
                    <p>Tracking: Will be shared via SMS & WhatsApp</p>
                </div>
                <div class="totals-box">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>₹ <?php echo number_format($order['subtotal']); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Discount</span>
                        <span>- ₹ <?php echo number_format($order['discount']); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping Charges</span>
                        <span>₹ <?php echo number_format($order['shipping_charges']); ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total Amount</span>
                        <span>₹ <?php echo number_format($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>

            <div class="invoice-footer">
                <div class="payment-status">
                    <div class="payment-icon"><i class="fa-solid fa-check"></i></div>
                    <div class="payment-text">
                        <h4>Payment Details</h4>
                        <p><?php echo htmlspecialchars($order['payment_method']); ?> Payment</p>
                        <p>Transaction ID: <?php echo rand(10000000, 99999999); ?></p>
                        <p>Paid on: <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>
                <div class="signature">
                    Thank you<br>
                    <span style="font-size: 0.9rem; font-family: 'Inter', sans-serif; font-style: normal; color: var(--text-gray);">for choosing</span><br>
                    WoodNest Furniture!
                </div>
            </div>
        </div>

        <div class="bottom-bar">
            <a href="tel:+919876543210"><i class="fa-solid fa-phone"></i> +91 98765 43210</a>
            <a href="mailto:support@woodnest.in"><i class="fa-regular fa-envelope"></i> support@woodnest.in</a>
            <a href="#"><i class="fa-solid fa-globe"></i> www.woodnest.in</a>
            <div style="display:flex; gap:10px; align-items:center;">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-youtube"></i>
                <span style="margin-left:5px;">Follow us for latest offers!</span>
            </div>
        </div>
    </div>

    <!-- User Action Buttons -->
    <a href="<?php echo $wa_url; ?>" target="_blank" class="wa-btn"><i class="fa-brands fa-whatsapp"></i> Send Order via WhatsApp</a>
    <button class="print-btn" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Invoice</button>

    <!-- Auto-redirect to WA if placing order for the first time -->
    <?php if(!isset($_GET['view_only'])): ?>
    <script>
        // Redirect to WhatsApp automatically in a new tab if preferred, or just let user click the green button.
        // For better UX, we just show the invoice and the large green button.
    </script>
    <?php endif; ?>

</body>
</html>

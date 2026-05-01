<?php
session_start();

// Mocking session for testing - remove these two lines in production
// $_SESSION['loggedin'] = true; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['redirect_source'] = 'checkout';
    header("location: register.html");
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$cartJson = $_POST['cart'] ?? '[]';
$mode = $_POST['mode'] ?? 'delivery';
$cart = json_decode($cartJson, true);
$_SESSION['mode'] = $mode;
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid cart data.");
}

include 'db.php';
include 'balance.php';

$allAvailable = true;
$totalOrderValue = 0;
$deliveryFee =   0;
$discount =0;
$address ="";
if($mode === 'delivery' ){
$deliveryFee =   25.00;
//   $stmt = $conn->prepare("SELECT * from user_addresses WHERE user_id=? and is_default=1 ");
//     $stmt->bind_param("i", $itemId);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($row = $result->fetch_assoc()){
//     $address=$row['address_label']+", "+$row['full_name']+", "+$row['phone_number']+", "+$row['street_address']+", "+$row['apartment']+", "+$row['locality'];
//     }
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | RudiiFoodie</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF4757; /* Foody Red */
            --secondary: #2F3542;
            --bg: #F1F2F6;
            --white: #FFFFFF;
            --success: #2ED573;
            --warning: #FFA502;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg);
            color: var(--secondary);
            margin: 0;
            padding: 20px;
        }

        .checkout-container {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .header h2 { margin: 0; font-size: 1.5rem; }
        .header p { margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem; }

        /* Modern Table Styling */
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .order-table th {
            text-align: left;
            background: #F8F9FA;
            padding: 15px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #747D8C;
        }

        .order-table td {
            padding: 15px;
            border-bottom: 1px solid #F1F2F6;
        }

        .item-name { font-weight: 600; display: block; }
        .item-meta { font-size: 0.8rem; color: #747D8C; }

        /* Price Breakdown Section */
        .summary-card {
            padding: 20px;
            background: #FFF;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .total-row {
            border-top: 2px dashed #CED4DA;
            margin-top: 15px;
            padding-top: 15px;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--primary);
        }

        /* Status Badges */
        .error-badge {
            background: #FFE0E3;
            color: var(--primary);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-top: 5px;
            display: inline-block;
        }

        .btn-pay {
            display: block;
            width: 100%;
            background: var(--primary);
            color: white;
            text-align: center;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
        }

        .btn-pay:hover { transform: translateY(-2px); filter: brightness(1.1); }
        
        .footer-note {
            text-align: center;
            font-size: 0.8rem;
            color: #A4B0BE;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <div class="header">
        <h2>Review Your Order</h2>
       <p><i class="fas fa-map-marker-alt"></i> Your primary delivery address is set</p>  <!-- ****** Delivery address ****** -->
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>Items</th>
                <th style="text-align:center">Qty</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
           foreach ($cart as $key => $item) {

    $itemId = (int) str_replace("item","",$item['id']);
    $qty = (int)$item['quantity'];

    $stmt = $conn->prepare("SELECT name, availability, price FROM menu_items WHERE item_id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $itemName = $row['name'];
        $price = (float)$row['price'];
        $stock = (int)$row['availability'];

        $subtotal = $price * $qty;
        $totalOrderValue += $subtotal;

        echo "<tr>
                <td>
                    <span class='item-name'> ".ucwords($itemName)."</span>
                    <span class='item-meta'>₹".number_format($price,2)." per unit</span>
                </td>
                <td style='text-align:center'>×$qty</td>
                <td style='text-align:right;font-weight:600;'>₹".number_format($subtotal,2)."</td>
              </tr>";
    }
}
            ?>
        </tbody>
    </table>

    <div class="summary-card">
        <div class="row">
            <span>Item Total</span>
            <span>₹<?php echo number_format($totalOrderValue, 2); ?></span>
        </div>
        <div class="row">
            <span>Delivery Fee</span>
            <span>₹<?php if($totalOrderValue>499.0) $deliveryFee =0; echo number_format($deliveryFee, 2); ?></span>
        </div>
      
        <div class="row total-row">
            <span>To Pay</span>
            <span>₹<?php echo number_format($totalOrderValue + $deliveryFee - $discount, 2); ?></span>
        </div>
        <div class="row total-row">
            <span>Available Balance</span>
            <span>₹<?php echo balance($_SESSION['user_id']); ?></span>
          
        </div>
       
        <div class="row total-row">
            <span>Discount</span>
            <span>₹<?php $discount=number_format($totalOrderValue*0.05,2); echo $discount; ?></span>
          
        </div>

        <?php if ($allAvailable && !empty($cart)): ?>
            <form action="payment.php" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="cart" value='<?php echo htmlspecialchars($cartJson); ?>'>
                <input type="hidden" name="mode" value='<?php echo htmlspecialchars($mode); ?>'> 
               
     <input type="hidden" name="total" value='<?php echo htmlspecialchars(number_format($totalOrderValue + $deliveryFee, 2)); ?>'>
      <input type="hidden" name="discount" value='<?php echo htmlspecialchars($discount); ?>'> 
                <button type="submit" class="btn-pay">
                    Proceed to Payment <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        <?php else: ?>
            <div class="error-badge" style="display:block; text-align:center; padding: 15px;">
                Some items are unavailable. Please adjust your cart.
            </div>
        <?php endif; ?>
    </div>

    <div class="footer-note">
        Secure Checkout powered by Rudiifoodie.
    </div>
</div>

</body>
</html>
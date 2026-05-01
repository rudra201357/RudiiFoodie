<?php
session_start();
include "db.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: register.html");
    exit;
}

$userId = $_SESSION['user_id'];
$cart = json_decode($_POST['cart'] ?? '[]', true);
$mode = $_POST['mode'] ?? '';
$total = (float)($_POST['total'] ?? 0);
$discount = (float)($_POST['discount'] ?? 0);
$err= "";
if (empty($cart) || $total <= 0) {
    die("Invalid request.");
}

$conn->begin_transaction();

try {

    /* 🔐 Lock wallet */
    $stmtWallet = $conn->prepare("SELECT wallet FROM users WHERE user_id=? FOR UPDATE");
    $stmtWallet->bind_param("i", $userId);
    $stmtWallet->execute();
    $wallet = $stmtWallet->get_result()->fetch_assoc()['wallet'];

    if ($wallet < $total) {
        throw new Exception("Insufficient balance");
    }

    /* 💸 Deduct wallet */
    $stmtDeduct = $conn->prepare("UPDATE users SET wallet = wallet - ? WHERE user_id=?");
    $stmtDeduct->bind_param("di", $total, $userId);
    $stmtDeduct->execute();

    /* 📦 Create order */
    $stmtOrder = $conn->prepare("INSERT INTO orders(user_id,total_amount,order_mode) VALUES(?,?,?)");
    $stmtOrder->bind_param("ids", $userId, $total,$mode);
    $stmtOrder->execute();
    $orderId = $conn->insert_id;

    /* 💳 Payment entry */
    $utr =generateUTR($conn);
    $stmtPayment = $conn->prepare("INSERT INTO payments(user_id,order_id,amount,type,through,utr) VALUES(?,?,?,'Debit','Order',?)");
    $stmtPayment->bind_param("iids", $userId, $orderId, $total,$utr);
    $stmtPayment->execute();

    /* 📦 Items + stock update */
    $stmtPrice = $conn->prepare("SELECT price, availability FROM menu_items WHERE item_id=? FOR UPDATE");

    foreach ($cart as $item) {

        $itemId = (int) str_replace("item", "", $item['id']);
        $qty = (int) $item['quantity'];

        $stmtPrice->bind_param("i", $itemId);
        $stmtPrice->execute();
        $row = $stmtPrice->get_result()->fetch_assoc();

        if (!$row) continue;

        if ($row['availability'] < $qty) {
            throw new Exception("Stock not available");
        }

        /* Update stock */
        $newStock = $row['availability'] - $qty;
        $stmtStock = $conn->prepare("UPDATE menu_items SET availability=? WHERE item_id=?");
        $stmtStock->bind_param("ii", $newStock, $itemId);
        $stmtStock->execute();

        /* Insert order item */
        $subtotal = $row['price'] * $qty;

        $stmtItem = $conn->prepare("INSERT INTO order_items(order_id,item_id,quantity,subtotal) VALUES(?,?,?,?)");
        $stmtItem->bind_param("iiid", $orderId, $itemId, $qty, $subtotal);
        $stmtItem->execute();
    }

    /* 🎁 Cashback (ONLY ONCE) */
    if ($discount > 0) {

        $utr = generateUTR($conn);

        $stmtCash = $conn->prepare("
            INSERT INTO payments (user_id, order_id, amount, type, through, utr)
            VALUES (?, ?, ?, 'Credit', 'CASHBACK', ?)
        ");
        $stmtCash->bind_param("iids", $userId, $orderId, $discount, $utr);
        $stmtCash->execute();

        $stmtWalletAdd = $conn->prepare("UPDATE users SET wallet = wallet + ? WHERE user_id=?");
        $stmtWalletAdd->bind_param("di", $discount, $userId);
        $stmtWalletAdd->execute();
    }

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
   $err= "Order failed: " . $e->getMessage();
}

/* 🔢 Generate UTR */
function generateUTR($conn) {
    do {
        $utr = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT 1 FROM payments WHERE utr=?");
        $stmt->bind_param("s", $utr);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);

    return $utr;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Placed</title>

    <style>
        body {
            font-family: Arial;
            background: #F1F2F6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        #success {
            color: #2ED573;
        }
         #failed {
            color: #d52e2e;
        }

        .orderid {
            font-size: 18px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #FF4757;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>

</head>

<body>

    <div class="box">

        <div class="orderid">
         <?php if($err == ""): ?>
            <h1 id="success">🎉 Order Placed Successfully</h1>
            Order ID : <b>#
                <?php echo $orderId; ?>
            </b>
            <script>
            <?php ?>
            let mode = "<?php echo $mode; ?>";
            localStorage.removeItem("cart_" + mode);
        </script>

            <?php else: ?>
                <h1 id="failed">Order Failed</h1>
                <?php echo "Error: ". $err; ?>
            <?php  endif; ?>
        </div>

        


        <a href="index.php" class="btn">Continue Ordering</a>

    </div>

</body>

</html>
<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

/* =======================
   1. WALLET BALANCE
======================= */
// $balanceQuery = "
// SELECT 
// SUM(CASE WHEN type='Credit' THEN amount ELSE 0 END) -
// SUM(CASE WHEN type='Debit' THEN amount ELSE 0 END) AS balance
// FROM payments
// WHERE user_id='$user_id'
// ";

// $balResult = $conn->query($balanceQuery)->fetch_assoc();
// $balance = $balResult['balance'] ?? 0;


/* =======================
   2. FETCH ORDERS
======================= */
$orderQuery = "
SELECT 
o.order_id,
o.order_date,
o.status,
o.order_mode,

GROUP_CONCAT(CONCAT(m.name,' x',oi.quantity) SEPARATOR ', ') AS items,

p.amount

FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN menu_items m ON oi.item_id = m.item_id
LEFT JOIN payments p 
ON o.order_id = p.order_id AND p.through='Order'

WHERE o.user_id = '$user_id'

GROUP BY o.order_id
ORDER BY o.order_date DESC
";

$orderResult = $conn->query($orderQuery);


/* =======================
   3. WALLET HISTORY
======================= */
$walletQuery = "
SELECT 
amount,
type,
through,
UTR,
time

FROM payments

WHERE user_id = '$user_id'
AND (through != 'Order' OR type='Debit')

ORDER BY time DESC
";

$walletResult = $conn->query($walletQuery);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Activity | Dashboard</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --primary: #6366f1;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 40px auto;
            display: grid;
            /* Orders take more space (1.6) than Wallet (1) */
            grid-template-columns: 1.6fr 1fr; 
            gap: 30px;
        }

        h2.main-title {
            grid-column: 1 / -1;
            margin-bottom: 10px;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* --- CARDS & BOXES --- */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        .card:hover {
            border-color: #cbd5e1;
        }

        /* --- ORDER STYLES --- */
        .order-card {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .order-meta {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-light);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
            margin-bottom: 5px;
        }

        .order-items {
            font-weight: 500;
            font-size: 15px;
            color: var(--text-dark);
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        /* --- STATUS BADGES --- */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-prepared { background: #f3e8ff; color: #6b21a8; }

        /* --- WALLET STYLES --- */
        .wallet-balance-card {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            border: none;
            padding: 25px;
            margin-bottom: 25px;
        }

        .wallet-balance-card h4 { margin: 0; opacity: 0.8; font-weight: 400; }
        .wallet-balance-card .amount { font-size: 32px; font-weight: 700; margin-top: 5px; }

        .txn-card {
            padding: 15px;
            font-size: 14px;
        }

        .txn-header {
            display: flex;
            justify-content: space-between;
            font-weight: 600;
        }

        .txn-time {
            font-size: 12px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .credit { color: var(--success); }
        .debit { color: var(--danger); }
        
        .utr-text {
            font-family: monospace;
            background: #f1f5f9;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 11px;
            color: var(--text-light);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .dashboard-container { grid-template-columns: 1fr; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <h2 class="main-title">📦 My Activity Dashboard</h2>

    <div class="orders-column">
        <h3 class="section-header">Recent Orders</h3>
        
        <?php if($orderResult->num_rows > 0): ?>
            <?php while($row = $orderResult->fetch_assoc()): ?>
                <div class="card order-card">
                    <div class="order-meta">
                        <span>#<?php echo $row['order_id']; ?> • <?php echo date("M d, Y", strtotime($row['order_date'])); ?></span>
                        <span>Mode: <?php echo $row['order_mode']; ?></span>
                    </div>
                    
                    <div class="order-items">
                        <?php echo ucwords($row['items']); ?>
                    </div>

                    <div class="order-footer">
                        <div>
                            <span class="badge status-<?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </div>
                        <div style="font-weight: 700;">
                            ₹<?php echo number_format($row['amount'] ?? 0, 2); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:var(--text-light)">No orders found yet.</p>
        <?php endif; ?>
    </div>

    <div class="wallet-column">
        <h3 class="section-header">Financial Summary</h3>

        <!-- <div class="card wallet-balance-card">
            <h4>Available Balance</h4>
            <div class="amount">₹<?php echo number_format($balance, 2); ?></div>
        </div> -->

        <h4 style="margin-bottom:15px; font-size: 14px; color: var(--text-light);">TRANSACTION HISTORY</h4>

        <?php if($walletResult->num_rows > 0): ?>
            <?php while($row = $walletResult->fetch_assoc()): ?>
                <div class="card txn-card">
                    <div class="txn-time"><?php echo date("d M, h:i A", strtotime($row['time'])); ?></div>
                    <div class="txn-header">
                        <span>
                            <?php 
                            if($row['through'] == 'Order' && $row['type'] == 'Debit') echo "Order Payment";
                            else if($row['through'] == 'CASHBACK') echo "🎁 Cashback";
                            else echo $row['through'] . " Recharge";
                            ?>
                        </span>
                        <span class="<?php echo strtolower($row['type']); ?>">
                            <?php echo ($row['type'] == 'Credit' ? '+' : '-'); ?> ₹<?php echo number_format($row['amount'], 2); ?>
                        </span>
                    </div>
                    <?php if(!empty($row['UTR'])): ?>
                        <div style="margin-top:8px;">
                            <span class="utr-text">UTR: <?php echo $row['UTR']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:var(--text-light)">No transactions found.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

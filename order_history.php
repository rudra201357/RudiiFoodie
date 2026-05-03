<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #f1f5f9;
            --card: #ffffff;
            --primary: #4f46e5;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            margin: 0;
            padding: 40px 20px;
        }

        .table-container {
            max-width: 1200px;
            margin: auto;
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            padding: 14px 20px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:hover {
            background-color: #fbfcfe;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending { background: #fff7ed; color: #9a3412; }
        .status-delivered { background: #f0fdf4; color: #166534; }
        .status-cancelled { background: #fef2f2; color: #991b1b; }
        .status-prepared { background: #faf5ff; color: #6b21a8; }

        .status-cafe{background: #fff7ed; color: #2509d5;}
        .status-delivery{background: #fff7ed; color: #847f18;}

        .phone-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .amount-cell {
            font-weight: 600;
        }

        .time-cell {
            color: var(--text-muted);
            font-size: 12px;
        }

        .items-cell {
            max-width: 250px;
            font-size: 13px;
            color: #475569;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="table-container">
    <div class="table-header">
        <h2>Live Orders Dashboard</h2>
        <div style="font-size: 12px; color: var(--text-muted);">
            Showing all recent transactions
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Time & Date</th>
                <th>Items</th> <!-- NEW -->
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php 
        include 'db.php';

        $query = "SELECT 
                    o.*, 
                    DATE_FORMAT(o.order_date, '%d %b %Y') as date,  
                    DATE_FORMAT(o.order_date, '%h:%i %p') as time, 
                    u.name, 
                    u.phone,

                    GROUP_CONCAT(CONCAT(m.name, ' x', oi.quantity) SEPARATOR ', ') AS items

                  FROM orders o 

                  JOIN users u ON o.user_id = u.user_id 
                  JOIN order_items oi ON o.order_id = oi.order_id
                  JOIN menu_items m ON oi.item_id = m.item_id

                  GROUP BY o.order_id

                  ORDER BY o.order_date DESC";

        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {

            while($row = mysqli_fetch_assoc($result)) {

                $status_lower = strtolower($row['status']);
                $mode = strtolower($row['order_mode']);

                echo "<tr>";

                echo "<td>#{$row['order_id']}</td>";

                echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";

                echo "<td>
                        <a href='tel:{$row['phone']}' class='phone-link'>
                        {$row['phone']}
                        </a>
                      </td>";

                echo "<td>
                        <div class='time-cell'>{$row['time']}</div>
                        <div>{$row['date']}</div>
                      </td>";

                // ✅ ITEMS
                echo "<td class='items-cell'>" 
                     . ucwords($row['items']) . 
                     "</td>";

                echo "<td class='amount-cell'>₹" . number_format($row['total_amount'], 2) . "</td>";

                echo "<td>
                        <span class='status-badge status-{$mode}'>" 
                        . ucfirst($row['order_mode']) . 
                        "</span>
                      </td>";
                echo "<td>
                        <span class='status-badge status-{$status_lower}'>" 
                        . ucfirst($row['status']) . 
                        "</span>
                      </td>";

                echo "</tr>";
            }

        } else {
            echo "<tr>
                    <td colspan='7' style='text-align:center; padding:40px; color:var(--text-muted);'>
                    No orders found.
                    </td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

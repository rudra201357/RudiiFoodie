<?php

include "db.php";

$sql = "SELECT order_id, user_id, total_amount FROM orders WHERE status = 'pending'";
$result = $conn->query($sql);

// --- 3. Start HTML output for the orders list ---
$output = '';

if ($result->num_rows > 0) {
    // Loop through each row of the result
    while($row = $result->fetch_assoc()) {
        // Create an HTML card/div for each order
        $output .= '<div class="order-card">';
        $output .= '    <h4 class="order-id">Order ID: #' . htmlspecialchars($row['order_id']) . '</h4>';
        $output .= '    <p><strong>Customer:</strong> ' . htmlspecialchars($row['user_id']) . '</p>';
        $output .= '    <p><strong>Total:</strong> ₹' . number_format($row['total_amount'], 2) . '</p>';
       
        $output .= '    <span class="status preparing-status">Status: Preparing</span>';
        $output .= '</div>';
    }
} else {
    $output = '<p class="no-orders">No orders currently in the "preparing" status.</p>';
}

$conn->close();


echo $output;

?>
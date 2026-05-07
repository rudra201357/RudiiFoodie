<?php
require_once 'db.php';

// Check session
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'){
    echo "Unauthorized";
    exit;
}

// Check connection
if (!isset($conn) || !$conn || mysqli_connect_errno()) {
    echo "DB Error: Could not connect";
    exit;
}

$code_raw = $_POST['code'] ?? '';

if ($code_raw === '') {
    echo "Missing code";
    exit;
}

$code = trim($code_raw);
$code_esc = mysqli_real_escape_string($conn, $code);

// First, get current stock status
$query_select = "SELECT stock FROM menu_items WHERE code = '$code_esc'";
$result = mysqli_query($conn, $query_select);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Item not found";
    exit;
}

$row = mysqli_fetch_assoc($result);
$current_stock = (int)$row['stock'];

// Toggle: if 1, make 0; if 0, make 1
$new_stock = ($current_stock === 1) ? 0 : 1;

// Update the stock column
$query_update = "UPDATE menu_items SET stock = $new_stock WHERE code = '$code_esc'";

if (mysqli_query($conn, $query_update)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo "success:" . $new_stock;  // Return success with new status
    } else {
        echo "No rows affected";
    }
} else {
    echo "Query error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

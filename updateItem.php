<?php
include 'db.php';

// simple connection check
if (!isset($conn) || !$conn || mysqli_connect_errno()) {
    echo "Error: Could not connect to the database.";
    exit;
}

$price_raw = $_POST['price'] ?? '';
$qty_raw   = $_POST['qty'] ?? 0;
$code_raw  = $_POST['hiddenValue'] ?? '';

$code = trim($code_raw);
if ($code === '') {
    header("Location: admin-portal.php?msg=invalid_code");
    exit;
}

// validate qty
$qty = filter_var($qty_raw, FILTER_VALIDATE_INT);
// if ( $qty < 0) {
//     header("Location: admin-portal.php?msg=invalid_qty");
//     exit;
// }
$qty = (int)$qty;

// decide query: if price left blank -> update only availability; otherwise update both
$code_esc = mysqli_real_escape_string($conn, $code);

if ($price_raw === '' && $qty_raw != '') {
    // only availability
    $query = "UPDATE menu_items
              SET availability = availability + $qty
              WHERE code = '$code_esc'";
}  
else if($qty_raw ==0 && $price !=''){
    $query =  "UPDATE menu_items
              SET price= $price 
              WHERE code = '$code_esc'";
}
else {
    // validate price numeric and positive
    if (!is_numeric($price_raw) || (float)$price_raw <= 0) {
        header("Location: admin-portal.php?msg=invalid_price");
        exit;
    }
    $price = (float)$price_raw;

    // FIXED: correct column name 'availability'
    $query = "UPDATE menu_items
              SET availability = availability + $qty,
                  price = $price
              WHERE code = '$code_esc'";
}

// run query
if (mysqli_query($conn, $query)) {
    if (mysqli_affected_rows($conn) > 0) {
        header("Location: admin-portal.php?msg=success");
    } else {
        header("Location: admin-portal.php?msg=no_change");
    }
} else {
    // send the DB error for debugging (you can log instead)
    $err = urlencode(mysqli_error($conn));
    header("Location: admin-portal.php?msg=db_error&err=$err");
}

mysqli_close($conn);
exit;

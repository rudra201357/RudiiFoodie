<?php
// require/include the DB file correctly
require 'db.php';

// Check connection (adjust depending on how db.php creates $conn)
if (!isset($conn) || !$conn || mysqli_connect_errno()) {
    echo "<p>Error: Could not connect to the database.</p>";
    exit;
}

// whitelist allowed sort columns and directions
$allowed_sort = ['name', 'price', 'availability'];
$allowed_order = ['asc', 'desc'];

// get user input and sanitize via whitelist + lowercasing
$sort_value = strtolower($_POST['sort'] ?? 'name');
$orderby = strtolower($_POST['view_mode'] ?? 'asc');

if (!in_array($sort_value, $allowed_sort, true)) {
    $sort_value = 'name';
}
if (!in_array($orderby, $allowed_order, true)) {
    $orderby = 'asc';
}

// Build query using validated identifiers (no direct user injection)
$query = "SELECT name, price, availability, code, stock
          FROM menu_items
          ORDER BY $sort_value $orderby";

$result = mysqli_query($conn, $query);

if (!$result) {
    // Query error
    echo "<p>Query error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
} else {
    if (mysqli_num_rows($result) > 0) {
        echo '<div style="display: grid;grid-template-columns: 700px 120px 140px; align-items: center; margin: 20px;">';
         echo "<h4>Item Name</h4>";
        echo "<h4>Price</h4>";
        echo "<h4>Available</h4>";
        echo '</div>';
        echo '<div class="row-list" >';
       
   while ($row = mysqli_fetch_assoc($result)) {

    $name = htmlspecialchars($row['name']);
    $price = htmlspecialchars($row['price']);
    $avail = htmlspecialchars($row['availability']);
    $code = htmlspecialchars($row['code']); // item1, item2, etc
    $stock = (int)$row['stock']; // 0 or 1

    echo "<div class='item-row'>";

        echo "<h4>$name</h4>";
        echo "<p>$price</p>";
        echo "<p>$avail</p>";

        // UPDATE BUTTON (unique id using code)
        echo "<button class='update-btn' id= 'btn_$code' onclick=\"showUpdateSection('$code')\">Update</button>";
        
        // DISABLE BUTTON - show current status
        $btn_text = $stock === 1 ? 'Available' : 'Unavailable';
        $btn_class = $stock === 1 ? 'disable-btn available' : 'disable-btn unavailable';
        echo "<button class='$btn_class' id= 'disable_btn_$code' onclick=\"disable('$code', this)\">$btn_text</button>";
        
        // HIDDEN SECTION
        echo "<div class='update-box' id='box_$code' style='display:none;'>";

            echo "<form action='updateItem.php' method='post'>";
            echo '<div class="form-section">';
            echo "New Price: <input type='number' name='price'><br>";
            echo "New Stock: <input type='number' name='qty'><br>";
            echo "<input type='hidden' name='hiddenValue' value='$code'>";
            echo "<button id='save-btn' type='submit'>Save</button>";
            
            
            echo '</div>';
            echo "</form>";
  
        echo "</div>";

    echo "</div>";
}
        echo '</div>'; // close .row-list AFTER loop
    } else {
        echo '<p>No items found.</p>';
    }
    mysqli_free_result($result);
}

mysqli_close($conn);
?>

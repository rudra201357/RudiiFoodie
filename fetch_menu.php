<?php
include 'db.php';

if (!isset($conn) || mysqli_connect_error()) { 
    echo "<p>Error: Could not connect to the database.</p>";
    exit;
}

$sort_value = $_POST['sort'] ?? 'all';
$order_mode = $_POST['order_mode'] ?? 'delivery';

$base_query = "SELECT * FROM menu_items";
$where_clause = "";
$order_by_clause = "";

switch ($sort_value) {
    case 'veg':
    case 'nonveg':
    case 'meal':
    case 'drinks':
    case 'offer':
        $category_map = [
            'veg' => 3,
            'nonveg' => 4,
            'meal' => 5,
            'drinks' => 6,
            'offer' => 2
        ];
        $category_id = $category_map[$sort_value];
        $where_clause = " WHERE stock = 1 and category_id = " . (int)$category_id;
        break;
    default:
        break;
}

switch ($sort_value) {
    case 'price-asc':
        $order_by_clause = " ORDER BY price ASC";
        break;
    case 'price-desc':
        $order_by_clause = " ORDER BY price DESC";
        break;
    case 'name-asc':
        $order_by_clause = " ORDER BY name ASC";
        break;
    case 'name-desc':
        $order_by_clause = " ORDER BY name DESC";
        break;
    default:
        break;
}


$query = $base_query . $where_clause . $order_by_clause;

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $item_status_class = '';
        $button_text = 'Add';
        $button_disabled_attr = '';

        $is_unavailable = ($row['availability'] < 1);

        $is_restricted_by_delivery = (
            $order_mode === 'delivery' && 
            ($row['category_id'] == 6 || $row['category_id'] == 2)
        );

        if ($is_unavailable || $is_restricted_by_delivery) {
            $item_status_class = 'unavailable';
            $button_text = 'Not Available';
            $button_disabled_attr = 'disabled';
         

            if ($is_restricted_by_delivery && !$is_unavailable) {
                $button_text = 'Rejected';
            }
        }
        ?>

        <div class="menu-item <?php echo $item_status_class; ?>">
            <img src="images/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <h4 style= " text-transform: capitalize;"><?php echo htmlspecialchars($row['name']); ?></h4>

            <div class="price-add">
                <span class="price">₹<?php echo htmlspecialchars($row['price']); ?></span>
                  <!-- <i class="fa-solid fa-circle-info info-icon" id="infoIcon" data-id="<?= $row ?>"></i> -->
             
               <button class="add-btn" data-id="<?= $row['code'] ?>" <?= $button_disabled_attr ?>>
                  <?php echo $button_text; ?>
                </button>

            </div>
        </div>
        <?php
    }
} else {
    echo "<p>No items found for the selected options.</p>";
}

mysqli_close($conn);
?>

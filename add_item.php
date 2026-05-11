<?php
require_once 'db.php';
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use Cloudinary\Cloudinary;

function uploadImageToCloudinary(array $file): string
{
    $cloudinary = new Cloudinary([
        'cloud' => [
            'cloud_name' => $_ENV['CLOUD_NAME'],
            'api_key'    => $_ENV['API_KEY'],
            'api_secret' => $_ENV['API_SECRET'],
        ],
        'url' => [
            'secure' => true,
        ],
    ]);

    $upload = $cloudinary->uploadApi()->upload($file['tmp_name'], [
        'folder' => 'rudiifoodie/menu-items',
    ]);

    return $upload['secure_url'] ?? '';
}

// Basic validation
$item_name   = trim($_POST['item_name'] ?? '');
$price       = $_POST['price'] ?? '';
$quantity    = $_POST['quantity'] ?? '';

$description = trim($_POST['description'] ?? '');
$in_stock    = isset($_POST['in_stock']) ? (int)$_POST['in_stock'] : 1;

$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

if ($category_id <= 0) {
    echo "ERROR: Invalid category selected";
    exit;
}
 $newId=0;

if ($item_name === '' || $price === '' || $quantity === '' || $category_id === '') {
    echo "ERROR: Missing required fields";
    exit;
}
if (!is_numeric($price) || !is_numeric($quantity)) {
    echo "ERROR: Price and quantity must be numeric";
    exit;
}

// handle image upload (optional)
$image_path = null;
if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "ERROR: Image upload failed";
        exit;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowed, true)) {
        echo "ERROR: Invalid image type";
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo "ERROR: Image too large";
        exit;
    }
    try {
        $image_path = uploadImageToCloudinary($file);

        if ($image_path === '') {
            echo "ERROR: Failed to get Cloudinary image URL";
            exit;
        }
    } catch (Exception $e) {
        echo "ERROR: Cloudinary upload failed: " . $e->getMessage();
        exit;
    }


}

// insert into menu_items
$sql = "INSERT INTO menu_items (category_id, name, description, price, image_url, availability, stock)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "ERROR: Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param(
    'issdsii',
    $category_id,
    $item_name,
    $description,
    $price,
    $image_path,
    $quantity,
    $in_stock
);

if ($stmt->execute()) {
     $newId = $conn->insert_id;
     $code = 'item' . $newId;
      $upd = $conn->prepare("UPDATE menu_items SET code = ? WHERE item_id = ?");
    $upd->bind_param('si', $code, $newId);
    $upd->execute();
    $upd->close();

    echo "OK";
} else {
    echo "ERROR: Insert failed: " . $conn->error;
}
$stmt->close();

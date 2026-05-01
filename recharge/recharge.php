<?php
session_start();

// 🔐 Proper headers FIRST
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

// DB connection
require_once __DIR__ . '/../db.php';

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB Connection failed"]);
    exit;
}

// Session check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    echo json_encode(["success" => false, "error" => "Invalid User"]);
    exit;
}

// Get data
$data = json_decode(file_get_contents("php://input"), true);

$cardNumber = $data['cardNumber'] ?? null;
$expiry = $data['expiry'] ?? null;
$cvv = $data['cvv'] ?? null;
$amount = $data['amount'] ?? 0;
$mode = $data['mode'] ?? 'unknown';
$cardType = $data['cardType'] ?? null;

// Validate amount
if ($amount <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid amount"]);
    exit;
}

// Normalize mode
$mode = strtolower($mode);

if ($mode === "upi" || $mode === "qr") {
    $cardType = NULL;
    $mode = "UPI";
}

// Other values
$type = "Credit";
$userId = $_SESSION['user_id'];

// Generate UTR
$utr = generateUTR($conn);

// 🔥 START TRANSACTION
$conn->begin_transaction();

try {

    // ---- Insert into payments ----
    $stmt1 = $conn->prepare("
        INSERT INTO payments (user_id, amount, type, through, cardType, utr)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt1->bind_param("idssss", $userId, $amount, $type, $mode, $cardType, $utr);

    if (!$stmt1->execute()) {
        throw new Exception("Payment insert failed");
    }

    // ---- Update wallet ----
    $stmt2 = $conn->prepare("
        UPDATE users 
        SET wallet = wallet + ? 
        WHERE user_id = ?
    ");

    $stmt2->bind_param("di", $amount, $userId);

    if (!$stmt2->execute()) {
        throw new Exception("Wallet update failed");
    }

    if ($stmt2->affected_rows === 0) {
        throw new Exception("User not found");
    }

    // ✅ Commit
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Payment successful",
        "utr" => $utr
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

// Close
$stmt1->close();
$stmt2->close();
$conn->close();


// 🔢 UTR Generator
function generateUTR($conn) {

    do {
        $utr = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("SELECT payment_id FROM payments WHERE utr = ?");
        $stmt->bind_param("s", $utr);
        $stmt->execute();
        $stmt->store_result();

    } while ($stmt->num_rows > 0);

    $stmt->close();
    return $utr;
}
?>
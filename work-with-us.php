<?php
session_start();
include 'db.php'; // Make sure db.php connects to your MySQL database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Collect form data safely ---
    $role = $_POST['role'] ?? '';
    $name = ucwords(trim($_POST['name'] ?? ''));
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $vehicle_type = $_POST['vehicle_type'] ?? NULL;
    $vehicle_number = $_POST['vehicle_number'] ?? NULL;
    $experience = $_POST['experience'] ?? NULL;
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- Validate required fields ---
    if (empty($role) || empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        die("⚠️ Please fill in all required fields.");
    }

    if ($password !== $confirm_password) {
        die("❌ Passwords do not match!");
    }



if($role == "admin" || $role == "chief"){
    $vehicle_number = NULL;
    $vehicle_type = NULL;
}
if($vehicle_type == "Cycle"){
    $vehicle_number= NULL;
}

// echo "<script>alert('$role');</script>";
// echo "<script>alert('$name');</script>";
// echo "<script>alert('$email');</script>";
// echo "<script>alert('$phone ');</script>";
// echo "<script>alert('$vehicle_type');</script>";
// echo "<script>alert('$vehicle_number');</script>";
// echo "<script>alert('$experience');</script>";
// echo "<script>alert('$password');</script>";
 
   $check = $conn->prepare("SELECT email FROM users WHERE email = ? AND role = ? ");
    $check->bind_param("ss", $email, $role);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0 ) {
        echo "<script>alert('Email already exists. Please sign in.'); window.location.href='index.php';</script>";
        exit;
    }
   $check = $conn->prepare("SELECT phone FROM users WHERE phone = ? AND role = ? ");
    $check->bind_param("ss", $phone, $role);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0 ) {
        echo "<script>alert('Phone number already exists. Please sign in.'); window.location.href='index.php';</script>";
        exit;
    }

    // --- Prepare the insert query ---
    $stmt = $conn->prepare("
        INSERT INTO users  (name, email, password, phone, role, vehicle_type, vehicle_number)  VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("SQL Prepare failed: " . $conn->error);
    }

    // --- Bind parameters safely ---
    $stmt->bind_param(
        "sssssss", $name, $email,$password, $phone, $role, $vehicle_type, $vehicle_number);

    // --- Execute and check result ---
  
        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully! You can now sign in.');
             window.location.href='index.php';</script>";
             exit;
            } 
        else {
          echo "❌ Registration failed: " . $stmt->error;
          exit;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid Request Method!";
}
?>

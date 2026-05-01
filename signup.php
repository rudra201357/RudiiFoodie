<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['password'], $_POST['phone'],$_POST['password'])){
    $name = ucwords(trim($_POST['name']));
    $email = strtolower($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    }
    else exit;
    // Check for duplicate email
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0 ) {
        echo "<script>alert('Email already exists. Please sign in.'); window.location.href='register.html';</script>";
    }
     $check = $conn->prepare("SELECT email FROM users WHERE phone = ?");
    $check->bind_param("s", $phone);
    $check->execute();
    $check->store_result();
     if ($check->num_rows > 0 ) {
        echo "<script>alert('Phone number already exists. Please sign in.'); window.location.href='register.html';</script>";
    }
     else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, address, role, wallet) VALUES (?, ?, ?, ?, NULL,'customer',0.0)");
        $stmt->bind_param("ssss", $name, $email, $password, $phone);

        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully! You can now sign in.'); window.location.href='register.html';</script>";
        } else {
            echo "<script>alert('Error creating account. Try again.'); window.location.href='register.html';</script>";
        }
        $stmt->close();
    }
    $check->close();
}
?>

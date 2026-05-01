<?php
session_start();
include 'db.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if (isset($_POST['email'], $_POST['password'])){
    $emailPhone = strtolower($_POST['email']);
    $password1 = trim($_POST['password']);
    }
    else
        exit;
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ? and role = 'customer' ");
    $stmt->bind_param("s", $emailPhone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0  ){
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE phone = ? and role = 'customer'");
    $stmt->bind_param("s", $emailPhone);
    $stmt->execute();
    $stmt->store_result();
    }

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $name, $password);
        $stmt->fetch();
    
        if ($password === $password1) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['loggedin'] = true;
           $parts = explode(" ", trim($name));
            $initials = "";
            foreach ($parts as $p) {
                if ($p !== "") {
                    $initials .= strtoupper($p[0]);
                }
            }
            $_SESSION['user_initial'] ="Hi, ". $initials;

                if (isset($_SESSION['redirect_source']) && $_SESSION['redirect_source'] == 'checkout'){
                    header("Location: checkout.php");
                    exit;
                }
            
             
            header("Location: index.php");
            exit;
        } else {
            echo "<script>alert('Incorrect password!');window.location.href='register.html';</script>";
             
        }
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='register.html';</script>";
     
    }

    $stmt->close();
} else {
    // Direct access or missing fields
    header("Location: register.html");
    exit;
}
?>

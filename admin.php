<?php
session_start();
include "db.php";
$err= "";
// Check if form submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if(empty($email) || empty($password)){
        die("Please fill all fields");
    }

    // Prepare statement (security)
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){

        $user = $result->fetch_assoc();

        // Verify password (IMPORTANT)
        if($password == $user['password']){

            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to dashboard / home
            header("Location: admin-portal.html");
            exit;

        } else {
            $err=  "❌ Invalid Password";
        }

    } else {
        $err= "❌ User not found";
    }

} else {
    $err= "Invalid Request";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="icon" type="image/png" href="images/logo.png">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* Glass Card */
.login-box{
    width:350px;
    padding:40px;
    border-radius:15px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    color:white;
    text-align:center;
}

.login-box h2{
    margin-bottom:25px;
    font-weight:600;
}

/* Inputs */
.input-group{
    margin-bottom:20px;
    text-align:left;
}

.input-group label{
    font-size:14px;
}

.input-group input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border:none;
    outline:none;
    border-radius:8px;
    background: rgba(255,255,255,0.2);
    color:white;
}

.input-group input::placeholder{
    color:#ddd;
}

/* Button */
.login-btn{
    width:100%;
    padding:10px;
    border:none;
    border-radius:8px;
    background:#ff6b81;
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.login-btn:hover{
    background:#ff4757;
    transform:scale(1.05);
}

/* Footer */
.footer{
    margin-top:15px;
    font-size:12px;
    color:#ddd;
}

.footer a{
    color:white;
    text-decoration:none;
    font-weight:500;
}
</style>

</head>

<body>

<div class="login-box">

<h2>🔐 Welcome Back</h2>

<form action="" method="POST">

<div class="input-group">
<label>Email or Mobile</label>
<input type="email" name="email" placeholder="Enter your email" required>
</div>

<div class="input-group">
<label>Password</label>
<input type="password" name="password" placeholder="Enter your password" required>
</div>

<button type="submit" class="login-btn">Login</button>

</form>

<div class="footer">
    <?php  if($err != "" ): ?>
<span><?php echo htmlspecialchars($err); ?> </span>
<?php  endif; ?>
</div>

</div>

</body>
</html>

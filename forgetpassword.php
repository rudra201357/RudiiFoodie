<?php
include 'db.php';

$message = "";
$messageType = "";
$foundUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'find';

    if ($action === 'find') {
        $emailPhone = strtolower(trim($_POST['email_phone'] ?? ''));

        if ($emailPhone === '') {
            $message = "Please enter your email or phone number.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare("SELECT user_id, name, email, phone FROM users WHERE (email = ? OR phone = ?) AND role = 'customer' LIMIT 1");
            $stmt->bind_param("ss", $emailPhone, $emailPhone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $foundUser = $result->fetch_assoc();
                $message = "Account found. Set a new password below.";
                $messageType = "success";
            } else {
                $message = "No customer account found with those details.";
                $messageType = "error";
            }

            $stmt->close();
        }
    }

    if ($action === 'reset') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($userId <= 0 || $newPassword === '' || $confirmPassword === '') {
            $message = "Please fill all password fields.";
            $messageType = "error";
        } elseif ($newPassword !== $confirmPassword) {
            $message = "Passwords do not match.";
            $messageType = "error";
        } elseif (strlen($newPassword) < 4) {
            $message = "Password must be at least 4 characters.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ? AND role = 'customer'");
            $stmt->bind_param("si", $newPassword, $userId);

            if ($stmt->execute()) {
                $stmt->close();
                echo "<script>alert('Password updated successfully. Please sign in.'); window.location.href='register.html';</script>";
                exit;
            }

            $message = "Could not update password. Please search for your account again.";
            $messageType = "error";
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | RudiiFoodie</title>
    <link rel="stylesheet" href="css/register.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            height: auto;
            padding: 30px 16px;
        }

        .forgot-container {
            width: 430px;
            max-width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25),
                0 10px 10px rgba(0, 0, 0, 0.22);
            overflow: hidden;
        }

        .forgot-header {
            background: linear-gradient(to right, #FF4B2B, #F2BD12);
            color: #fff;
            padding: 30px;
            text-align: center;
        }

        .forgot-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .forgot-header p {
            margin: 0;
            font-weight: 400;
        }

        .forgot-form {
            height: auto;
            padding: 30px;
            gap: 8px;
        }

        .field-group {
            width: 100%;
            text-align: left;
        }

        .field-group label {
            color: #333;
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin: 4px 0 2px;
        }

        .field-group input {
            margin-top: 4px;
        }

        .notice {
            width: 100%;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.4;
            margin: 0 0 10px;
            padding: 12px 14px;
        }

        .notice.success {
            background: #ecfdf5;
            color: #166534;
        }

        .notice.error {
            background: #fef2f2;
            color: #991b1b;
        }

        .account-box {
            width: 100%;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            color: #7c2d12;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 10px;
            padding: 12px 14px;
            text-align: left;
        }

        .account-title {
            color: #431407;
            display: block;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .account-row {
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 10px;
            padding: 5px 0;
        }

        .account-label {
            color: #9a3412;
            font-weight: 700;
        }

        .account-value {
            color: #431407;
            overflow-wrap: anywhere;
        }

        .back-link {
            color: #333;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .forgot-container {
                min-height: 0;
            }

            .forgot-form {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <h2>RudiiFoodie</h2>

    <div class="forgot-container">
        <div class="forgot-header">
            <h1>Forgot Password</h1>
            <p>Find your account and create a new password.</p>
        </div>

        <form class="forgot-form" method="POST" action="forgetpassword.php">
            <?php if ($message !== ""): ?>
                <div class="notice <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($foundUser === null): ?>
                <input type="hidden" name="action" value="find">
                <div class="field-group">
                    <label for="email_phone">Email or Phone</label>
                    <input type="text" id="email_phone" name="email_phone" placeholder="Enter email or phone" required>
                </div>
                <button type="submit">Find Account</button>
            <?php else: ?>
                <div class="account-box">
                    <strong class="account-title">Account Details</strong>
                    <div class="account-row">
                        <span class="account-label">Name</span>
                        <span class="account-value"><?php echo htmlspecialchars($foundUser['name']); ?></span>
                    </div>
                    <div class="account-row">
                        <span class="account-label">Email</span>
                        <span class="account-value"><?php echo htmlspecialchars($foundUser['email']); ?></span>
                    </div>
                    <div class="account-row">
                        <span class="account-label">Phone</span>
                        <span class="account-value"><?php echo htmlspecialchars($foundUser['phone']); ?></span>
                    </div>
                </div>
                <input type="hidden" name="action" value="reset">
                <input type="hidden" name="user_id" value="<?php echo (int)$foundUser['user_id']; ?>">
                <div class="field-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="field-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <button type="submit">Reset Password</button>
            <?php endif; ?>

            <a class="back-link" href="register.html">Back to sign in</a>
        </form>
    </div>
</body>
</html>

<?php
session_start();
include "db.php";
if(!isset($_SESSION['loggedin'])){
    die("Invalid User");
}
$user_id = $_SESSION['user_id'];

// --- DELETE ADDRESS LOGIC ---
if(isset($_GET['delete_id'])){
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    // Ensure the user can only delete their OWN address
    $conn->query("DELETE FROM user_addresses WHERE address_id='$delete_id' AND user_id='$user_id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// INSERT ADDRESS
if(isset($_POST['save_address'])){
    $label = mysqli_real_escape_string($conn, $_POST['address_label']);
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $street = mysqli_real_escape_string($conn, $_POST['street_address']);
    $apt = mysqli_real_escape_string($conn, $_POST['apartment']);
    $locality = mysqli_real_escape_string($conn, $_POST['locality']);

    if(isset($_POST['is_default'])){
        $conn->query("UPDATE user_addresses SET is_default=0 WHERE user_id='$user_id'");
        $is_default = 1;
    } else {
        $is_default = 0;
    }

    $sql = "INSERT INTO user_addresses (user_id,address_label,full_name,phone_number,street_address,apartment,locality,is_default)
            VALUES ('$user_id','$label','$name','$phone','$street','$apt','$locality','$is_default')";
    $conn->query($sql);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// SET DEFAULT ADDRESS
if(isset($_GET['set_default'])){
    $aid = mysqli_real_escape_string($conn, $_GET['set_default']);
    $conn->query("UPDATE user_addresses SET is_default=0 WHERE user_id='$user_id'");
    $conn->query("UPDATE user_addresses SET is_default=1 WHERE address_id='$aid' AND user_id='$user_id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query("SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY is_default DESC, address_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Address Dashboard</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-sub: #64748b;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #22c55e;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text-main); margin: 0; padding: 40px 20px; }
        
        .dashboard { max-width: 1100px; margin: auto; display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; align-items: start; }

        /* LEFT SIDE */
        .address-list-section h2 { font-size: 1.5rem; margin-bottom: 20px; }
        .address-grid { display: grid; grid-template-columns: 1fr; gap: 15px; }

        .address-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 20px;
            border-radius: 12px;
            position: relative;
            transition: all 0.2s;
        }

        .address-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .address-card.default { border: 2px solid var(--primary); background: #f5f3ff; }

        /* Delete Button Style */
        .delete-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #fee2e2;
            color: var(--danger);
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            opacity: 0; /* Hidden by default */
            transition: opacity 0.2s;
        }

        .address-card:hover .delete-btn { opacity: 1; }
        .delete-btn:hover { background: var(--danger); color: white; }

        .badge { display: inline-block; padding: 2px 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; border-radius: 4px; margin-bottom: 8px; background: #e2e8f0; }
        .default .badge { background: var(--primary); color: white; }

        .address-card h4 { margin: 0 0 5px 0; font-size: 1.1rem; }
        .address-card p { margin: 2px 0; color: var(--text-sub); font-size: 0.95rem; line-height: 1.5; }

        .actions { margin-top: 15px; display: flex; gap: 15px; align-items: center; }
        .set-btn { text-decoration: none; color: var(--primary); font-size: 13px; font-weight: 600; }

        /* RIGHT SIDE FORM */
        .form-section { background: var(--card-bg); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); position: sticky; top: 20px; }
        input, textarea { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid var(--border); border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .save-btn { width: 100%; background: var(--primary); color: white; border: none; padding: 14px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        
        @media (max-width: 850px) { .dashboard { grid-template-columns: 1fr; } .form-section { order: -1; position: static; } }
    </style>
</head>
<body>

<div class="dashboard">
    
    <div class="address-list-section">
        <h2>📦 Your Addresses</h2>
        <div class="address-grid">
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="address-card <?php echo $row['is_default'] ? 'default' : ''; ?>">
                        
                        <a href="?delete_id=<?php echo $row['address_id']; ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Are you sure you want to delete this address?')">
                           Delete
                        </a>

                        <span class="badge"><?php echo htmlspecialchars($row['address_label']); ?></span>
                        <h4><?php echo htmlspecialchars($row['full_name']); ?></h4>
                        <p>📞 <?php echo htmlspecialchars($row['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($row['street_address']); ?>, <?php echo htmlspecialchars($row['apartment']); ?></p>
                        <p><?php echo htmlspecialchars($row['locality']); ?></p>
                        
                        <div class="actions">
                            <?php if(!$row['is_default']): ?>
                                <a href="?set_default=<?php echo $row['address_id']; ?>" class="set-btn">Set as Primary</a>
                            <?php else: ?>
                                <span style="color: var(--success); font-weight: 600; font-size: 12px;">✓ PRIMARY</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:var(--text-sub)">Your address book is empty.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-section">
        <h2>📍 New Address</h2>
        <form method="POST">
            <input type="text" name="address_label" placeholder="Label (Home, Office, etc.)" required>
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="phone_number" placeholder="Phone Number">
            <textarea name="street_address" rows="3" placeholder="Street Address" required></textarea>
            <input type="text" name="apartment" placeholder="Apt / Suite (optional)">
            <input type="text" name="locality" placeholder="City / Locality" required>
            <div style="margin: 10px 0; font-size: 0.9rem; color: var(--text-sub);">
                <input type="checkbox" name="is_default" id="def" style="width:auto;"> <label for="def">Make this my primary address</label>
            </div>
            <button type="submit" name="save_address" class="save-btn">Save Address</button>
        </form>
    </div>

</div>

</body>
</html>

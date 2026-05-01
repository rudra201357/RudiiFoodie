<?php
include "db.php";
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $email = $conn->real_escape_string($email);


    $stmt = $conn->prepare("SELECT sno, submit FROM subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      
        $row = $result->fetch_assoc();
        $new_submit = $row['submit'] + 1;

        $update = $conn->prepare("UPDATE subscribers SET submit = ? WHERE email = ?");
        $update->bind_param("is", $new_submit, $email);

        if ($update->execute()) {
            echo "Thanks for subscribing! ";
        } else {
            echo "Error updating: " . $conn->error;
        }

        $update->close();
    } else {
       
        $insert = $conn->prepare("INSERT INTO subscribers (email, submit, points) VALUES (?, 1, 10)");
        $insert->bind_param("s", $email);

        if ($insert->execute()) {
            echo "Thanks for subscribing! ";
        } else {
            echo "Error inserting: " . $conn->error;
        }

        $insert->close();
    }

    $stmt->close();
} else {
    echo "No email received.";
}

$conn->close();
?>

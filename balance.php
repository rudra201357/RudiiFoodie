<?php
include 'db.php';

function balance($user_id){
    global $conn;
     $stmt = $conn->prepare("SELECT wallet FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()){
        return $row['wallet'] ?? 0;
    }
}

?>
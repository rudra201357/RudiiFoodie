<?php
include "db.php";

if(isset($_POST['order_id'])){

    $orderId = (int)$_POST['order_id'];
     $action = $_POST['action'];
     if($action=="accept"){
    $stmt = $conn->prepare("UPDATE orders SET status='prepared' WHERE order_id=?");
     }
     else $stmt = $conn->prepare("UPDATE orders SET status='cancelled' WHERE order_id=?");
    $stmt->bind_param("i", $orderId);

    if($stmt->execute()){
        echo "success";
    } else {
        echo "error";
    }

} else {
    echo "invalid";
}
?>
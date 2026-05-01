<?php
// get_categories.php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

// adjust table/column names to match your schema: catagory? category?
$sql = "SELECT category_id, name FROM categories ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
$rows=[];
if ($result && mysqli_num_rows($result) > 0){
      while ($row = mysqli_fetch_assoc($result)){
        $rows[]= $row;
      }
    echo json_encode($rows);
   mysqli_close($conn);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Query preparation failed']);
    exit;
}

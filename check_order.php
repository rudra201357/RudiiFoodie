<?php
include "db.php";

$query = "
SELECT 
o.order_id,
o.order_date,
u.name,
u.phone,
u.address,
o.total_amount,
o.order_mode,
GROUP_CONCAT(CONCAT(m.name,' x',oi.quantity) SEPARATOR ', ') AS items

FROM orders o
JOIN users u ON o.user_id = u.user_id
JOIN order_items oi ON o.order_id = oi.order_id
JOIN menu_items m ON oi.item_id = m.item_id

WHERE o.status='pending'

GROUP BY o.order_id
ORDER BY o.order_date ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Pending Orders</title>

<style>
body{
    font-family:Arial;
    background:#f4f6f9;
    margin:0;
    padding:30px;
}

.container{
    max-width:1500px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    text-align:left;
    border-bottom:1px solid #eee;
}

th{
    background:#2f3542;
    color:white;
}

tr:hover{
    background:#f1f2f6;
}

.items{
    font-size:14px;
    color:#444;
}

.btn-status{
    background:#ffa502;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:5px;
    cursor:pointer;
    font-size:13px;
}

.btn-status:hover{
    background:#ff7f00;
}

.done{
    background:#2ed573 !important;
}

</style>

</head>

<body>

<div class="container">

<h2>📦 Pending Orders</h2>

<table>

<tr>
<th>Order ID</th>
<th>Mode</th>
<th>Date</th>
<th>Customer</th>
<th>Phone</th>
<th>Address</th>
<th>Items</th>
<th>Total</th>
<th>Action</th>
</tr>

<?php
if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

echo "<tr id='row_".$row['order_id']."'>";

echo "<td>#".$row['order_id']."</td>";
echo "<td>".$row['order_mode']."</td>";
echo "<td>".$row['order_date']."</td>";
echo "<td>".$row['name']."</td>";
echo "<td>".$row['phone']."</td>";
echo "<td>".$row['address']."</td>";
echo "<td class='items'>". ucwords($row['items'])."</td>";
echo "<td>₹".$row['total_amount']."</td>";

echo "<td>
<button class='btn-status' onclick='acceptOrder(\"".$row['order_id']."\", this)'>
Accept Order
</button>
<button class='btn-status' onclick='cancelOrder(\"".$row['order_id']."\", this)'>
Cancel Order
</button>
</td>";
echo "</tr>";
}

}else{
echo "<tr><td colspan='9'>No pending orders</td></tr>";
}
?>

</table>

</div>




</body>
</html>
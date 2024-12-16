<?php
// order_status.php
include('db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the logged-in user
$query = "SELECT products.name, orders.quantity, orders.status 
          FROM orders
          JOIN products ON orders.product_id = products.id
          WHERE orders.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
</head>
<body>
    <h2>Your Orders</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

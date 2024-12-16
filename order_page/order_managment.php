<?php
// Include database connection
include('db_connect.php');
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "You must be logged in as an admin.";
    exit();
}

// Fetch all orders with itemized details, including customer info
$query = "SELECT orders.id AS order_id, users.username, users.email, products.name AS product_name, 
                 orders.quantity, orders.status, orders.created_at
          FROM orders
          JOIN products ON orders.product_id = products.id
          JOIN users ON orders.user_id = users.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
</head>
<body>
    <h2>Order Management</h2>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['order_id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['product_name']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
                <form action="update_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <select name="status">
                        <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Confirmed" <?php echo ($row['status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="Shipped" <?php echo ($row['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                        <option value="Delivered" <?php echo ($row['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        <option value="Paid" <?php echo ($row['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                    </select>
                    <button type="submit">Update Status</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

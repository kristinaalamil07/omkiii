<?php
// Include the database connection
include('db_connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Safely fetch the logged-in user's ID
$user_id = intval($_SESSION['user_id']); // Ensure the user_id is an integer

// Fetch orders grouped by status
$statuses = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];
$orders_by_status = [];

foreach ($statuses as $status) {
    // Use a prepared statement to prevent SQL injection
    $query = "SELECT orders.id AS order_id, orders.total_price, orders.created_at, 
                     GROUP_CONCAT(CONCAT(products.name, ' (x', order_details.quantity, ')') SEPARATOR ', ') AS items
              FROM orders
              JOIN order_details ON orders.id = order_details.order_id
              JOIN products ON order_details.product_id = products.id
              WHERE orders.user_id = ? AND orders.status = ?
              GROUP BY orders.id";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $status); // Bind the parameters (user_id as integer, status as string)
    $stmt->execute();
    $result = $stmt->get_result();
    $orders_by_status[$status] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .order-status {
            margin-bottom: 20px;
        }
        .order-status h2 {
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Tracking</h1>

        <?php foreach ($statuses as $status): ?>
            <div class="order-status">
                <h2>Status: <?= htmlspecialchars($status) ?></h2>
                <?php if (!empty($orders_by_status[$status])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items</th>
                                <th>Total Price</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders_by_status[$status] as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                                    <td><?= htmlspecialchars($order['items']) ?></td>
                                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                                    <td><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders with this status.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

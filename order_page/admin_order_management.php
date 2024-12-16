<?php
// order_management.php
include('db_connect.php');

// Fetch all orders
$query = "SELECT orders.id, products.name, orders.quantity, orders.status, users.username 
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            margin: 20px 0;
            color: #4CAF50;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 18px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        button {
            padding: 8px 12px;
            font-size: 14px;
            margin: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Order Management</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Update Status</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <form action="update_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="status" value="Preparing to be shipped">Your parcel is preparing to be shipped</button><br>
                    <button type="submit" name="status" value="On its way">Your parcel is on its way</button><br>
                    <button type="submit" name="status" value="Delivered">Item delivered</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

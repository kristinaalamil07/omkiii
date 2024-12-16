<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

require_once 'db_connect.php'; // Your database connection file

// Handle form submission for placing an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    
    // Check if quantity is valid
    $query = "SELECT quantity FROM items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && $quantity > 0 && $quantity <= $item['quantity']) {
        // Insert into orders table
        $insertQuery = "INSERT INTO orders (user_id, item_id, quantity) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iii", $user_id, $item_id, $quantity);
        $insertStmt->execute();

        // Update stock in items table
        $updateQuery = "UPDATE items SET quantity = quantity - ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $quantity, $item_id);
        $updateStmt->execute();

        echo "<div class='alert alert-success'>Order placed successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Invalid quantity or insufficient quantity.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Order Items</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Order</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch items from the database
                $query = "SELECT * FROM items";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['quantity']}</td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='item_id' value='{$row['id']}'>
                                <input type='number' name='quantity' min='1' max='{$row['quantity']}' class='form-control mb-2' required>
                                <button type='submit' class='btn btn-primary'>Order</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
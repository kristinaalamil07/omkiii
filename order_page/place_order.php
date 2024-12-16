<?php
session_start();
include('db_connect.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product ID and quantity from the form
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    // Fetch the product details from the database
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    $product = $product_result->fetch_assoc();

    if ($product) {
        // Calculate the total price
        $total_price = $product['price'] * $quantity;

        // Insert the order into the orders table
        $order_query = "INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("id", $user_id, $total_price);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id; // Get the ID of the inserted order

            // Insert the order details (product, quantity) into the order_details table
            $order_details_query = "INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($order_details_query);
            $stmt->bind_param("iii", $order_id, $product_id, $quantity);
            $stmt->execute();

            // Redirect the user to the order confirmation page or order tracking
            header('Location: order_tracking.php'); // Change to appropriate page
            exit();
        } else {
            echo "Error: Could not place order.";
        }
    } else {
        echo "Product not found.";
    }
}
?>

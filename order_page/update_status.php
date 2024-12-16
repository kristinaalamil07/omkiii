<?php
// update_status.php
include('db_connect.php');

// Check if order_id and status are set in the POST request
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update the order status in the database
    $query = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "Order status updated to: $status";
        header('Location: order_management.php'); // Redirect back to order management
        exit;
    } else {
        echo "Error updating order status.";
    }
} else {
    echo "Invalid request.";
}
?>

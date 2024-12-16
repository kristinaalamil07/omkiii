<?php
session_start();
include('db_connect.php'); // Database connection script

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    $item_id = $_POST['item_id'];
    $price = $_POST['price'];

    if ($item_id && $quantity > 0) {
        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, item_id, price) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $item_id, $quantity);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Order placed successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error placing order.</p>";
        }
        $stmt->close();
    }
}

// Fetch items from the database
$result = $conn->query("SELECT * FROM items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        .category-links {
            margin-top: 10px;
            font-size: 18px;
            color: #555;
        }
        .category-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
            transition: color 0.3s;
        }
        .category-links a:hover {
            color: #218838;
        }
        .product-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px;
            text-align: center;
            transition: transform 0.3s ease;
            width: 30%;
            box-sizing: border-box;
        }
        .product-card:hover {
            transform: translateY(-10px);
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .product-info {
            margin-top: 15px;
        }
        .product-info h3 {
            font-size: 24px;
            margin: 10px 0;
        }
        .product-info p {
            font-size: 16px;
            color: #777;
        }
        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        /* Media Query for smaller screens */
        @media (max-width: 768px) {
            .product-card {
                width: 48%; /* 2 items per row on smaller screens */
            }
        }
        @media (max-width: 480px) {
            .product-card {
                width: 100%; /* 1 item per row on very small screens */
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Order Your Favorite Shoes</h1>
        <div class="category-links">
            <a href="#">MEN</a>
            <a href="#">WOMEN</a>
        </div>
    </header>

    <div class="container">
        <div class="product-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($row['image_url']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="product-image">
                    <div class="product-info">
                        <h3><?= htmlspecialchars($row['name']); ?></h3>
                        <p class="product-price">$<?= $row['price']; ?></p>
                        <form method="POST" action="order.php">
                            <input type="hidden" name="item_id" value="<?= $row['id']; ?>">
                            <input type="number" name="price" min="1" max="<?= $row['price']; ?>" required>
                            <button type="submit" name="order">Add To Cart</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>

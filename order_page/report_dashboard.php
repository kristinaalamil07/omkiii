<?php
// Include database connection
include('db_connect.php');
session_start();

// Fetch report data
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$thisYear = date('Y');
$lastYear = date('Y', strtotime('-1 year'));

// Sales Today
$salesToday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * price) AS total_sales FROM orders JOIN products ON orders.product_id = products.id WHERE DATE(orders.created_at) = '$today'"));

// Sales Yesterday
$salesYesterday = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * price) AS total_sales FROM orders JOIN products ON orders.product_id = products.id WHERE DATE(orders.created_at) = '$yesterday'"));

// Sales This Year
$salesThisYear = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * price) AS total_sales FROM orders JOIN products ON orders.product_id = products.id WHERE YEAR(orders.created_at) = '$thisYear'"));

// Sales Last Year
$salesLastYear = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(quantity * price) AS total_sales FROM orders JOIN products ON orders.product_id = products.id WHERE YEAR(orders.created_at) = '$lastYear'"));

// Top 10 Items
$topItems = mysqli_query($conn, "SELECT products.name, SUM(orders.quantity) AS total_quantity 
    FROM orders 
    JOIN products ON orders.product_id = products.id 
    GROUP BY products.id 
    ORDER BY total_quantity DESC 
    LIMIT 10");

// Inventory
$inventory = mysqli_query($conn, "SELECT name, description, price FROM products");

// User Activity
$userRegistrations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total_users FROM users WHERE role = 'customer'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports Dashboard</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .report-section {
            margin-bottom: 30px;
        }
        .report-section h2 {
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            padding: 20px;
            background:rgb(47, 50, 53);
            color: white;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-card h3 {
            margin: 0;
            font-size: 24px;
        }
        .stat-card p {
            margin: 5px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background: #f8f9fa;
            color: #333;
        }
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Reports Dashboard</h1>

        <!-- Sales Report -->
        <div class="report-section">
            <h2>Sales Overview</h2>
            <div class="stats">
                <div class="stat-card">
                    <h3>$<?= $salesToday['total_sales'] ?? 0 ?></h3>
                    <p>Sales Today</p>
                </div>
                <div class="stat-card">
                    <h3>$<?= $salesYesterday['total_sales'] ?? 0 ?></h3>
                    <p>Sales Yesterday</p>
                </div>
                <div class="stat-card">
                    <h3>$<?= $salesThisYear['total_sales'] ?? 0 ?></h3>
                    <p>Sales This Year</p>
                </div>
                <div class="stat-card">
                    <h3>$<?= $salesLastYear['total_sales'] ?? 0 ?></h3>
                    <p>Sales Last Year</p>
                </div>
            </div>
        </div>

        <!-- Inventory Report -->
        <div class="report-section">
            <h2>Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($inventory)): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['description'] ?></td>
                            <td>$<?= $row['price'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Top 10 Items -->
        <div class="report-section">
            <h2>Top 10 Best-Selling Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($topItems)): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['total_quantity'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- User Activity -->
        <div class="report-section">
            <h2>User Activity</h2>
            <p>Total Registered Customers: <?= $userRegistrations['total_users'] ?></p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> E-Commerce Dashboard. All rights reserved.</p>
    </div>
</body>
</html>

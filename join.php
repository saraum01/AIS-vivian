<?php
// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "ms"; // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$joinType = '';
$result = null;

// Check if a button has been clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['left_join'])) {
        $joinType = 'LEFT JOIN';
        $query = "SELECT fp.id AS food_product_id, fp.name, fp.description
                  FROM food_products fp LEFT JOIN sales s ON fp.id = s.food_product_id";
    } elseif (isset($_POST['right_join'])) {
        $joinType = 'RIGHT JOIN';
        $query = "SELECT s.id AS sale_id, s.quantity, s.date_sold, fp.price
                  FROM food_products fp RIGHT JOIN sales s ON fp.id = s.food_product_id";
    } elseif (isset($_POST['union'])) {
        $joinType = 'UNION';
        $query = "(SELECT fp.id AS food_product_id, fp.name, fp.description, NULL AS price, s.id AS sale_id, s.quantity, s.date_sold
                   FROM food_products fp LEFT JOIN sales s ON fp.id = s.food_product_id)
                  UNION ALL
                  (SELECT fp.id AS food_product_id, fp.name, fp.description, fp.price, s.id AS sale_id, s.quantity, s.date_sold
                   FROM food_products fp RIGHT JOIN sales s ON fp.id = s.food_product_id)";
    }

    if (isset($query)) {
        $result = $conn->query($query);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Example</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Body Styling */
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background-color: #2e0249;
            padding: 15px 30px;
            color: #ffffff;
        }

        .navbar-logo {
            font-size: 28px;
            font-weight: bold;
            color: #e0e0e0;
        }

        .navbar a {
            color: #e0e0e0;
            text-decoration: none;
            margin-left: 20px;
            padding: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #bb86fc;
            color: #1a1a1a;
            border-radius: 5px;
        }

        /* Header and Form Styling */
        h1 {
            margin-top: 20px;
            color: #bb86fc;
            font-size: 2em;
        }

        form button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            background-color: #bb86fc;
            color: #1a1a1a;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        form button:hover {
            background-color: #4a148c;
            color: #ffffff;
        }

        /* Table Styling */
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            color: #333333;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }

        th {
            background-color: #bb86fc;
            color: #ffffff;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            .navbar-logo {
                font-size: 22px;
            }

            form button {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-logo">MyApp Logo</div>
    <div>
        <a href="dashboard.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<h1>Join Example</h1>

<form method="post">
    <button type="submit" name="left_join">LEFT JOIN (Food Product Details)</button>
    <button type="submit" name="right_join">RIGHT JOIN (Sales Details)</button>
    <button type="submit" name="union">UNION (Combined Data)</button>
</form>

<?php if ($joinType): ?>
    <h2><?php echo $joinType; ?> Result</h2>
    <table>
        <tr>
            <?php if ($joinType === 'LEFT JOIN'): ?>
                <th>Food Product ID</th>
                <th>Name</th>
                <th>Description</th>
            <?php elseif ($joinType === 'RIGHT JOIN'): ?>
                <th>Sale ID</th>
                <th>Quantity</th>
                <th>Date Sold</th>
                <th>Price</th>
            <?php elseif ($joinType === 'UNION'): ?>
                <th>Food Product ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Sale ID</th>
                <th>Quantity</th>
                <th>Date Sold</th>
            <?php endif; ?>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <?php if ($joinType === 'LEFT JOIN'): ?>
                        <td><?php echo $row['food_product_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                    <?php elseif ($joinType === 'RIGHT JOIN'): ?>
                        <td><?php echo $row['sale_id']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['date_sold']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                    <?php elseif ($joinType === 'UNION'): ?>
                        <td><?php echo $row['food_product_id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price'] ?? ''; ?></td>
                        <td><?php echo $row['sale_id']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['date_sold']; ?></td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No results found</td>
            </tr>
        <?php endif; ?>
    </table>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>

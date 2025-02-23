<?php
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php"; // Include your database connection file

// Fetch food products from the database
$sql = "SELECT * FROM food_products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle the sale of food products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Insert sale into sales table
    $insert_sql = "INSERT INTO sales (food_product_id, quantity) VALUES (:product_id, :quantity)";
    $insert_stmt = $pdo->prepare($insert_sql);
    $insert_stmt->bindParam(':product_id', $product_id);
    $insert_stmt->bindParam(':quantity', $quantity);
    $insert_stmt->execute();
}

// Handle adding a new food product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image_url = $_POST['image_url'];

    // Insert new product into the database
    $insert_product_sql = "INSERT INTO food_products (name, description, price, image_url) VALUES (:name, :description, :price, :image_url)";
    $insert_product_stmt = $pdo->prepare($insert_product_sql);
    $insert_product_stmt->bindParam(':name', $name);
    $insert_product_stmt->bindParam(':description', $description);
    $insert_product_stmt->bindParam(':price', $price);
    $insert_product_stmt->bindParam(':image_url', $image_url);
    $insert_product_stmt->execute();

    // Redirect to the same page to avoid resubmission
    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        background-color: #121212;
        color: #e0e0e0;
        font-family: Arial, sans-serif;
    }
    .navbar {
        background-color: #1f1b24;
    }
    .navbar-brand, .navbar-nav .nav-link {
        color: #bb86fc;
        transition: color 0.3s, background-color 0.3s;
    }
    .navbar-nav .nav-link:hover {
        background-color: #3c2f41;
        color: #e0e0e0;
        border-radius: 5px;
    }
    .container {
        margin-top: 20px;
    }
    .product-card {
        margin: 15px;
        border: 1px solid #444;
        border-radius: 8px;
        padding: 10px;
        background-color: #1f1b24;
        color: #e0e0e0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s, transform 0.3s;
    }
    .product-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        transform: translateY(-5px);
    }
    .product-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .product-card h5 {
        color: #bb86fc;
    }
    .product-card p {
        color: #b3b3b3;
    }
    .btn-success {
        background-color: #3700b3;
        border-color: #3700b3;
        transition: background-color 0.3s, color 0.3s;
    }
    .btn-success:hover {
        background-color: #6200ea;
        color: white;
    }
    .add-product-card {
        border: 1px solid #444;
        border-radius: 8px;
        padding: 10px;
        background-color: #1f1b24;
        margin: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s, transform 0.3s;
    }
    .add-product-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        transform: translateY(-5px);
    }
    .btn-primary {
        background-color: #bb86fc;
        border-color: #bb86fc;
        transition: background-color 0.3s, color 0.3s;
    }
    .btn-primary:hover {
        background-color: #6200ea;
        color: #e0e0e0;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Food Sales</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <a class="nav-link" href="warning.php">Notificatuion</a>
                    <a class="nav-link" href="join.php">Join Data</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>Food Products</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="product-card text-center">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                        <form method="post" action="">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" min="1" value="1" required>
                            <button type="submit" class="btn btn-success">Sell</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Add New Food Product</h2>
        <div class="add-product-card">
            <form method="post" action="">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" name="image_url" class="form-control" required>
                </div>
                <input type="hidden" name="new_product" value="1">
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Close the database connection
unset($stmt);
unset($pdo);
?>

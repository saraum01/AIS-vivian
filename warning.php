<?php
require_once "config.php"; // Database connection

// Fetch all intruder attempts
$sql = "SELECT id, username, attempt_time, attempt_count FROM intruders ORDER BY attempt_time DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$intruders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intruder Log</title>
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
        <h2 class="text-center">Intruder Attempt Logs</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Attempt Count</th>
                    <th>Last Attempt Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($intruders) > 0): ?>
                    <?php foreach ($intruders as $intruder): ?>
                        <tr>
                            <td><?= htmlspecialchars($intruder['id']); ?></td>
                            <td><?= htmlspecialchars($intruder['username']); ?></td>
                            <td><?= htmlspecialchars($intruder['attempt_count']); ?></td>
                            <td><?= htmlspecialchars($intruder['attempt_time']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No intruder attempts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

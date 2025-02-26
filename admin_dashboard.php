<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

$admin_name = "Administrator"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            background-color: #1a1a2e;
            color: #c084fc;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #22223b;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            color: white;
            margin: 0;
        }
        .btn-logout {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-logout:hover {
            background-color: darkred;
        }
        .container {
            padding: 20px;
            text-align: center;
        }
        .card {
            background: #22223b;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(192, 132, 252, 0.8);
            margin: 20px auto;
            width: 50%;
        }
        .card h2 {
            color: white;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background: #7b2cbf;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background: #5a189a;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <form action="logout.php" method="post">
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>

    <div class="container">
        <div class="card">
            <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h2>
            <p>You have successfully logged into the admin dashboard.</p>
            <a href="intruder_logs.php" class="btn">malicios attacks</a>
            
        </div>
    </div>

</body>
</html>

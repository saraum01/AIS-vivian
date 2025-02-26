<?php
// Include database configuration
require_once "config.php"; // Ensure config.php contains your database connection

// Fetch all records from failed_attempts table
$sql_failed = "SELECT id, ip_address, FROM_UNIXTIME(attempt_time) as attempt_time FROM failed_attempts ORDER BY attempt_time DESC";
$stmt_failed = $pdo->prepare($sql_failed);
$stmt_failed->execute();
$failed_attempts = $stmt_failed->fetchAll(PDO::FETCH_ASSOC);

// Fetch all records from intruders table
$sql_intruders = "SELECT id, username, attempt_time, attempt_count FROM intruders ORDER BY attempt_time DESC";
$stmt_intruders = $pdo->prepare($sql_intruders);
$stmt_intruders->execute();
$intruders = $stmt_intruders->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request securely
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"]) && isset($_POST["delete_table"])) {
    $delete_id = intval($_POST["delete_id"]); // Ensure it's an integer
    $delete_table = $_POST["delete_table"];

    if ($delete_table === "failed_attempts" || $delete_table === "intruders") {
        $sql_delete = "DELETE FROM $delete_table WHERE id = :id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $stmt_delete->execute();
        header("Location: intruder_logs.php"); // Refresh page after deletion
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intruder Attempt Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #1f1b24;
            padding: 10px;
            text-align: center;
        }
        .navbar h1 {
            color: #bb86fc;
            margin: 0;
        }
        .container {
            margin-top: 20px;
        }
        .table {
            background-color: #1f1b24;
            color: #e0e0e0;
        }
        .thead-dark th {
            background-color: #3700b3 !important;
        }
        .btn-logout, .btn-delete {
            background-color: #bb86fc;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-logout:hover, .btn-delete:hover {
            background-color: #6200ea;
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm("Are you sure you want to delete this record?")) {
                form.submit();
            }
        }
    </script>
</head>
<body>

<div class="navbar">
    <h1>Intruder Logs</h1>
    <form action="logout.php" method="post">
        <a class="btn btn-primary" href="admin_dashboard.php">Back to Dashboard</a>
        <button type="submit" class="btn-logout">Logout</button>
    </form>
</div>

<div class="container">
    <h2 class="text-center">Admin Intruder</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>Attempt Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($failed_attempts) > 0): ?>
                <?php foreach ($failed_attempts as $attempt): ?>
                    <tr>
                        <td><?= htmlspecialchars($attempt['id']); ?></td>
                        <td><?= htmlspecialchars($attempt['ip_address']); ?></td>
                        <td><?= htmlspecialchars($attempt['attempt_time']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="delete_id" value="<?= $attempt['id']; ?>">
                                <input type="hidden" name="delete_table" value="failed_attempts">
                                <button type="button" class="btn-delete" onclick="confirmDelete(this.form)">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No failed login attempts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2 class="text-center">User Intruder</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Attempt Time</th>
                <th>Attempt Count</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($intruders) > 0): ?>
                <?php foreach ($intruders as $intruder): ?>
                    <tr>
                        <td><?= htmlspecialchars($intruder['id']); ?></td>
                        <td><?= htmlspecialchars($intruder['username']); ?></td>
                        <td><?= htmlspecialchars($intruder['attempt_time']); ?></td>
                        <td><?= htmlspecialchars($intruder['attempt_count']); ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="delete_id" value="<?= $intruder['id']; ?>">
                                <input type="hidden" name="delete_table" value="intruders">
                                <button type="button" class="btn-delete" onclick="confirmDelete(this.form)">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No intruder attempts found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

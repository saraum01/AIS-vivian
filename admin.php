<?php
session_start();
require 'config.php';

$max_attempts = 3;
$lock_duration = 10; // Lock time in seconds

// Get user IP address
$user_ip = $_SERVER['REMOTE_ADDR'];

// Fetch secret code from database
$query = $pdo->query("SELECT code FROM admin_codes ORDER BY id DESC LIMIT 1");
$secret_code = $query->fetchColumn() ?: 'DEFAULT_CODE';

// Initialize session variables if not set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['locked_time'] = 0;
}

$current_time = time();
$lock_remaining = max(0, $_SESSION['locked_time'] - $current_time);
$lock_active = $_SESSION['attempts'] >= $max_attempts && $lock_remaining > 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$lock_active) {
    $entered_code = strtoupper(trim($_POST['secret_code'] ?? ''));

    if ($entered_code === $secret_code) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['attempts'] = 0;
        $_SESSION['locked_time'] = 0; // Reset lock on success
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['attempts']++;

        // Log the failed attempt into the database
        $stmt = $pdo->prepare("INSERT INTO failed_attempts (ip_address) VALUES (?)");
        $stmt->execute([$user_ip]);

        $error_message = "Incorrect code. Attempt " . $_SESSION['attempts'] . " of $max_attempts.";

        if ($_SESSION['attempts'] >= $max_attempts) {
            $_SESSION['locked_time'] = $current_time + $lock_duration;
            $lock_remaining = $lock_duration;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            background-color: #1a1a2e;
            color: #c084fc;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .wrapper {
            width: 360px;
            padding: 20px;
            background: #22223b;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(192, 132, 252, 0.8);
            text-align: center;
            transition: opacity 0.5s ease-in-out;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: #1a1a2e;
            color: #c084fc;
            text-align: center;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-primary {
            background-color: #7b2cbf;
            color: white;
        }
        .btn-primary:hover {
            background-color: #5a189a;
        }
        .btn-secondary {
            background-color: #444;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #666;
        }
        .error {
            color: red;
            font-weight: bold;
            font-size: 14px;
        }
        .warning {
            color: orange;
            font-weight: bold;
            font-size: 14px;
        }
        @media (max-width: 400px) {
            .wrapper {
                width: 90%;
            }
        }
    </style>
    <script>
        function startCountdown(seconds) {
            let countdownElement = document.getElementById("countdown");
            let inputForm = document.getElementById("input-form");

            if (seconds > 0) {
                inputForm.style.opacity = "0"; 
                setTimeout(() => inputForm.style.display = "none", 500); 
                
                countdownElement.textContent = "Too many failed attempts. Try again in " + seconds + " seconds.";
                
                let interval = setInterval(() => {
                    seconds--;
                    countdownElement.textContent = "Too many failed attempts. Try again in " + seconds + " seconds.";
                    
                    if (seconds <= 0) {
                        clearInterval(interval);
                        countdownElement.textContent = "";
                        inputForm.style.display = "block"; 
                        setTimeout(() => inputForm.style.opacity = "1", 100); 
                    }
                }, 1000);
            }
        }
        
        window.onload = function() {
            let remainingTime = <?php echo $lock_active ? $lock_remaining : 0; ?>;
            if (remainingTime > 0) {
                startCountdown(remainingTime);
            }
        };
    </script>
</head>
<body>
    <div class="wrapper">
        <h2>Enter Secret Code</h2>
        <?php if ($lock_active) { ?>
            <p class="error" id="countdown"></p>
        <?php } elseif (!empty($error_message)) { ?>
            <p class="warning"><?php echo $error_message; ?></p>
        <?php } ?>
        
        <form id="input-form" method="post" style="<?php echo $lock_active ? 'display: none; opacity: 0;' : ''; ?>">
            <input type="password" name="secret_code" placeholder="Enter secret code" required>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
    </div>
</body>
</html>

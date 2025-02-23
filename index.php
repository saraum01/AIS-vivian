<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            resetAttempts($pdo, $username);

                            header("location: dashboard.php");
                            exit;
                        } else {
                            $login_err = "Invalid username or password.";
                            logIntruder($pdo, $username);
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                    logIntruder($pdo, $username);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}

function logIntruder($pdo, $username) {
    
    $sql = "SELECT attempt_count FROM intruders WHERE username = :username";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        unset($stmt);

        if ($result) {
        
            $new_count = $result["attempt_count"] + 1;
            $sql = "UPDATE intruders SET attempt_count = :new_count WHERE username = :username";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":new_count", $new_count, PDO::PARAM_INT);
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->execute();
                unset($stmt);
            }
        } else {
           
            $sql = "INSERT INTO intruders (username, attempt_count) VALUES (:username, 1)";
            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->execute();
                unset($stmt);
            }
        }
    }
}

function resetAttempts($pdo, $username) {
    $sql = "DELETE FROM intruders WHERE username = :username";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        unset($stmt);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
       body {
            font: 14px sans-serif;
            background-color: #000; /* Black background */
            color: #dcdcdc; /* Light gray text */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height for centering */
            margin: 0;
        }
        .wrapper {
            width: 100%;
            max-width: 400px; /* Set a max-width for the form */
            padding: 30px; /* Increased padding for better spacing */
            background-color: #1a1a1a; /* Dark gray background for the form */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5); /* Soft shadow for depth */
        }
        .form-control {
            background-color: #121212; /* Darker input field */
            color: #dcdcdc; /* Light text */
            border: 1px solid #8e24aa; /* Purple border */
        }
        .form-control:focus {
            background-color: #121212; /* Dark background on focus */
            border-color: #ba68c8; /* Bright purple border on focus */
            box-shadow: 0 0 5px rgba(186, 104, 200, 0.5); /* Purple glow effect on focus */
        }
        .btn-primary {
            background-color: #6a1b9a; /* Dark purple button */
            border: none;
            transition: background-color 0.3s; /* Smooth transition */
        }
        .btn-primary:hover {
            background-color: #8e24aa; /* Brighter purple on hover */
        }
        .alert-danger {
            background-color: #e57373; /* Softer red for error messages */
            color: #fff; /* White text */
            border-radius: 5px; /* Slight rounding of corners */
        }
        a {
            color: #ce93d8; /* Light purple link */
        }
        a:hover {
            color: #ba68c8; /* Brighter purple on hover */
        }
    </style> 
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php" style="color: #ffccbc;">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>

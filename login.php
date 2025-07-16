<?php
    // Include database connection
    include("connection.php");
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $employee_id = $_POST['user'];
        $password = $_POST['pass'];

        $sql = "SELECT * FROM Employee WHERE Employee_ID_Number = ?";  
        
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();  
            $row = $result->fetch_assoc();  
            $count = $result->num_rows;  
            
            if ($count == 1 && password_verify($password, $row["Password"])) {  
                // Start a new session
                session_start();
                
                // Store data in session variables
                $_SESSION["employee_id"] = $employee_id;
                $_SESSION["first_name"] = $row["First_Name"];
                $_SESSION["last_name"] = $row["Last_Name"];
                
                // Redirect user to dashboard page
                header("Location: dashboard.php");
                exit();
            } else {  
                // Invalid credentials, redirect to login with error
                header("Location: login.php?login_failed=true");
                exit();
            }
        } else {
            // Error with prepared statement
            echo "Oops! Something went wrong. Please try again later.";
        }
        
        // Close statement
        $stmt->close();
        
        // Close connection
        $mysqli->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-group a {
            text-decoration: none;
            color: #007bff;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="user">Employee ID:</label>
                <input type="text" id="user" name="user" required>
            </div>
            <div class="form-group">
                <label for="pass">Password:</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login" name="submit">
            </div>
        </form>
        <?php if(isset($_GET['login_failed']) && $_GET['login_failed'] == true): ?>
            <p style="color: red;">Invalid Employee ID or Password</p>
        <?php endif; ?>
        <p>Don't have an account?<a href="registration.php">Register here</a></p>
    </div>
</body>
</html>

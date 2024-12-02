<?php
session_start();
include 'koneksi.php'; // Ganti dengan path ke koneksi database Anda

if (isset($_SESSION['username'])) {
    header("Location: MenuUtama.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    // Query untuk mengambil data Admin
    $query_admin = "SELECT * FROM admin WHERE Username = '$username' AND Password = '$password'";
    $result_admin = mysqli_query($conn, $query_admin);
    $user_admin = mysqli_fetch_assoc($result_admin);

    if ($user_admin) {
        $_SESSION['username'] = $user_admin['Username'];
        $_SESSION['level'] = $user_admin['Level'];
        $_SESSION['ID'] = $user_admin['ID'];
        header("Location: MenuUtama.php");
        exit;
    } else {
        $error_message = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
         /* Styling page background with sky-blue gradient */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #87CEEB, #4682B4); /* Sky-blue gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Semi-transparent, blurred overlay for the login box */
        .login-container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.15); /* Light semi-transparent background */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            backdrop-filter: blur(10px); /* Blur effect */
        }

        h1 {
            margin-bottom: 20px;
            color: #000;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        label {
            font-size: 16px;
            color: #000;
            text-align: left;
            width: 100%;
            display: block;
            margin-bottom: -25px;
        }

        .show-password-container {
            display: flex;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .show-password-label {
            font-size: 14px;
            color: #000;
            align-items: center;
            margin-top: 2px;
            margin-left: 4px; /* Add some space between checkbox and text */
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
        }

        /* Hover effect on the checkbox */
        input[type="checkbox"]:hover {
            transform: scale(1.2);
        }

        .register-link {
            margin-top: 15px;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }

        .register-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="post">
            <label for="username">Username</label><br>
            <input type="text" id="username" name="Username" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="Password" required><br><br>

            <div class="show-password-container">
                <input type="checkbox" id="showPasswordCheckbox" onclick="togglePasswordVisibility()">
                <label class="show-password-label" for="showPasswordCheckbox">Show Password</label>
            </div>

            <button type="submit" name="login">Login</button>
        </form>

        <!-- Register link -->
        <a href="register2.php" class="register-link">Don't have an account? Register here</a>
    </div>

    <script>
        // Function to toggle password visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("password");
            const checkBox = document.getElementById("showPasswordCheckbox");
            passwordField.type = checkBox.checked ? "text" : "password";
        }
    </script>
</body>
</html>

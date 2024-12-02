<?php
session_start();
include 'koneksi.php'; // Make sure to include your database connection

// Handle form submission for user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);

    // Get the last user ID from the database to calculate the next ID
    $query_last_id = "SELECT MAX(ID) AS LastID FROM admin";
    $result_last_id = mysqli_query($conn, $query_last_id);

    if ($result_last_id) {
        $row = mysqli_fetch_assoc($result_last_id);
        $last_id = $row['LastID'] ? $row['LastID'] : 0; // If NULL, start from 0
        $next_id = $last_id + 1; // Add 1 for the next ID
    } else {
        die("Failed to retrieve last user ID: " . mysqli_error($conn));
    }

    // Insert the new user into the users table
    $query_insert = "INSERT INTO admin (ID, Username, Password, Level) 
                     VALUES ('$next_id', '$username', '$password', '$level')";
    if (mysqli_query($conn, $query_insert)) {
        header("Location: MenuUtama.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <style>
        /* Styling for the form */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            height: 100%;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        select {
            background-color: #fff;
            color: #555;
        }

        .form-footer {
            text-align: center;
            margin-top: 10px;
        }

        .form-footer a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .form-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="form-container">
            <h2>Register Account</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="level">Level:</label>
                    <select id="level" name="level" required>
                        <option value="Admin">Admin</option>
                        <option value="Pelanggan">Pelanggan</option>
                        <option value="Pemasok">Pemasok</option>
                        <option value="Manajer">Manajer</option>
                    </select>
                </div>

                <input type="submit" value="Register">
            </form>
        </div>
    </div>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>

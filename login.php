<?php
// Include the necessary files for database connection and session management
include("header.php");

require_once 'db_connection.php'; 
require_once 'session.php'; 

// Check if the user is already logged in, redirect to the admin if true
if (isLoggedIn()) {
    header('Location: admin.php'); 
    exit();
}

// Handle the login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Store the user's login session information
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        header('Location: admin.php');
        exit();
    } else {
        $loginError = 'Invalid username or password'; // Display an error message on the login form
    }

    // Send the login status as JSON response
    $response = array('success' => false, 'message' => $loginError);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management System - Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"> 
    <script src="js/jquery-3.6.0.min.js"></script> 
    <script src="js/bootstrap.min.js"></script> 
    <style>
        .container {
            max-width: 50%;
            margin: 0 auto;
            margin-top: 100px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('form').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                var username = $('#username').val();
                var password = $('#password').val();

                $.ajax({
                    type: 'POST',
                    url: 'login.php',
                    data: {
                        username: username,
                        password: password
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Redirect to admin page on successful login
                            window.location.href = 'admin.php';
                        } else {
                            // Display the error message
                            $('.alert').text(response.message).show();
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Login</h1>
        <div class="alert alert-danger" style="display: none;"></div>
        <form>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <div class="form-group text-center">
                <a href="signup.php" class="btn btn-link">Don't have an account? Signup</a>
            </div>
        </form>
    </div>
    <footer>
        <?php include("footer.php");?>
    </footer>
</body>
</html>

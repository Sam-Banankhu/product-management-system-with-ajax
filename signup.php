<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files for database connection and session management
require_once 'header.php';
require_once 'db_connection.php';
require_once 'session.php';

// Check if the user is already logged in, redirect to the dashboard if true
if (isLoggedIn()) {
    header('Location: admin.php');
    exit();
}

// Handle the signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate form data
    $errors = array();

    // Check if username is empty
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Check if password is empty
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check if there are no validation errors
    if (empty($errors)) {
        // Check if the username already exists
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Username already exists, return JSON response with error message
            $response = array(
                'success' => false,
                'message' => 'Username already exists. Please choose a different username.'
            );
            echo json_encode($response);
            exit();
        }

        // Hash the password before saving it to the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user data into the users table
        $query = "INSERT INTO users (username, password, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $username, $hashedPassword);

        if ($stmt->execute()) {
            // Return JSON response with success message and redirect URL
$response = array(
    'success' => true,
    'message' => 'User registered successfully.',
    'redirect' => 'login.php'
);
echo json_encode($response);
exit();
        } else {
            // Return JSON response with error message
            $response = array(
                'success' => false,
                'message' => 'Error occurred while registering. Please try again later.'
            );
            echo json_encode($response);
            exit();
        }
    } else {
        // Return JSON response with validation errors
        $response = array(
            'success' => false,
            'errors' => $errors
        );
        echo json_encode($response);
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management System - Signup</title>
    <!-- Include necessary CSS and JavaScript files -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        .container {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 100px;
        }
    </style>
    <script>
    $(document).ready(function () {
        $('form').submit(function (e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Show success message and redirect to login page
                        $('.alert-success').html(response.message).show();
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 3000);
                    } else if (response.errors) {
                        var errorHtml = '';
                        $.each(response.errors, function (index, error) {
                            errorHtml += '<p>' + error + '</p>';
                        });
                        $('.alert-danger').html(errorHtml).show();
                    } else {
                        $('.alert-danger').html(response.message).show();
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    $('.alert-danger').html('An error occurred. Please try again later.').show();
                }
            });
        });
    });
</script>

</head>
<body>
    <div class="container">
        <h1>Signup</h1>
        <div class="alert alert-success" style="display: none;"></div>
        <div class="alert alert-danger" style="display: none;"></div>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Signup</button>
            </div>
            <div class="form-group text-center">
                <a href="login.php" class="btn btn-link">Already have an account? Login</a>
            </div>
        </form>
    </div>
    <footer>
        <?php include("footer.php"); ?>
    </footer>
</body>
</html>

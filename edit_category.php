<?php
// Include the necessary files for database connection and session management
include("header.php");
require_once 'db_connection.php';
require_once 'session.php';

// Check if the user is already logged in, redirect to the index if true
if (!isLoggedIn()) {
    header('Location: index.php'); // Redirect to index.php if user is not logged in
    exit();
}

// Check if the category ID is provided in the URL
if (!isset($_GET['id'])) {
    header('Location: categories.php'); // Redirect to categories.php if category ID is not provided
    exit();
}

$category_id = $_GET['id'];

// Retrieve the category details from the database
$query = "SELECT * FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

// Handle the category update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];

    // Update the category name in the database
    $query = "UPDATE categories SET name = ? WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $category_name, $category_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the categories.php page
    header('Location: categories.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management System - Edit Category</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Edit Category</h1>
        <form method="POST">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="category_name">Category Name:</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo $category['name']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_category">Update Category</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

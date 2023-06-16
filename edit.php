<!DOCTYPE html>
<html>
<head>
    <title>Product Management System - Edit Item</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<?php
// Include the necessary files for database connection and session management
include("header.php");
require_once 'db_connection.php';
require_once 'session.php';

// Check if the user is already logged in, redirect to the index if not
if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Check if the item_id is provided in the URL
if (!isset($_GET['item_id'])) {
    header('Location: admin.php');
    exit();
}

$item_id = $_GET['item_id'];

// Retrieve the item details from the items table
$query = "SELECT items.item_id, items.name, items.description, items.quantity, items.price, categories.name AS category_name
          FROM items
          INNER JOIN categories ON items.category_id = categories.category_id
          WHERE items.item_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

// Handle the item update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $item_name = $_POST['item_name'];
    $item_description = $_POST['item_description'];
    $item_quantity = $_POST['item_quantity'];
    $item_price = $_POST['item_price'];
    $item_category = $_POST['item_category'];

    // Server-side validation for price and quantity fields
    if ($item_quantity < 0 || $item_price < 0) {
        $error_message = "Quantity and price cannot be negative.";
    } else {
        // Update the item in the items table
        $query = "UPDATE items SET name = ?, description = ?, quantity = ?, price = ?, category_id = ? WHERE item_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssidii', $item_name, $item_description, $item_quantity, $item_price, $item_category, $item_id);
        $stmt->execute();
        $stmt->close();

        header('Location: admin.php');
        exit();
    }
}

// Retrieve the list of categories from the categories table
$query = "SELECT * FROM categories";
$result = $conn->query($query);

// Check if there are categories available
if ($result && $result->num_rows > 0) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
} else {
    // Handle the case when no categories are found
    $error_message = "No categories found. Please create categories first.";
}
?>

<div class="container">
    <h1>Edit Item</h1>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php else: ?>
        <form method="POST">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="item_name">Name:</label>
                        <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo $item['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="item_description">Description:</label>
                        <textarea class="form-control" id="item_description" name="item_description" required><?php echo $item['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="item_quantity">Quantity:</label>
                        <input type="number" class="form-control" id="item_quantity" name="item_quantity" value="<?php echo $item['quantity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="item_price">Price:</label>
                        <input type="number" class="form-control" id="item_price" name="item_price" value="<?php echo $item['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="item_category">Category:</label>
                        <select class="form-control" id="item_category" name="item_category" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo isset($item['category_id']) && $category['category_id'] == $item['category_id'] ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>

                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_item">Update Item</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

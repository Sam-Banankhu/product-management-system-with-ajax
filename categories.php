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

// Handle the category form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    // Insert the new category into the categories table
    $query = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $category_name);
    $stmt->execute();
    $stmt->close();

    // Retrieve the newly added category
    $last_category_id = $conn->insert_id;
    $query = "SELECT * FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $last_category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_category = $result->fetch_assoc();
    $stmt->close();

    // Send the newly added category as JSON response
    echo json_encode(['success' => true, 'category' => $new_category]);
    exit();
}

// Handle the category deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];

    // Delete the category and related items from the tables
    $query = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $stmt->close();

    // Send response back to the client-side
    echo json_encode(['success' => true]);
    exit();
}

// Pagination variables
$items_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Retrieve the total number of categories
$query = "SELECT COUNT(*) AS total_categories FROM categories";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_categories = $row['total_categories'];

// Calculate the total number of pages
$total_pages = ceil($total_categories / $items_per_page);

// Retrieve the list of categories with pagination
$query = "SELECT * FROM categories LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $offset, $items_per_page);
$stmt->execute();
$result = $stmt->get_result();
$categories = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Management System - Categories</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Categories</h1>
        <h2>Add Category</h2>
        <form id="add-category-form" method="POST">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="category_name">Category Name:</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                </div>
            </div>
        </form>
        <hr>
        <h2>Category List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="category-list">
                <?php foreach ($categories as $category) { ?>
                    <tr>
                        <td><?php echo $category['category_id']; ?></td>
                        <td><?php echo $category['name']; ?></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-primary">Edit</a>
                            <button class="btn btn-danger delete-category" data-category-id="<?php echo $category['category_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="<?php if ($i == $current_page) echo 'active'; ?>">
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
  
        <hr>
        <a href="admin.php" class="btn btn-primary">Go Back to Admin</a>
    </div>

    <script>
    $(document).ready(function() {
        // Delete category
        $('.delete-category').click(function() {
            var category_id = $(this).data('category-id');
            var delete_button = $(this);

            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: 'categories.php',
                    method: 'POST',
                    data: {
                        delete_category: true,
                        category_id: category_id
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            delete_button.closest('tr').remove(); // Remove the deleted category row from the table
                        } else {
                            alert('Failed to delete category. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the category. Please try again.');
                    }
                });
            }
        });

        // Add category
        $('#add-category-form').submit(function(e) {
            e.preventDefault(); // Prevent the form from submitting

            var category_name = $('#category_name').val();

            $.ajax({
                url: 'categories.php',
                method: 'POST',
                data: {
                    add_category: true,
                    category_name: category_name
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        var category = data.category;
                        var categoryRow = '<tr><td>' + category.category_id + '</td><td>' + category.name + '</td><td><a href="edit_category.php?id=' + category.category_id + '" class="btn btn-primary">Edit</a><button class="btn btn-danger delete-category" data-category-id="' + category.category_id + '">Delete</button></td></tr>';

                        $('#category-list').append(categoryRow); // Append the new category row to the table
                        $('#category_name').val(''); // Clear the category name input
                        alert('Category added successfully.');
                    } else {
                        alert('Failed to add category. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred while adding the category. Please try again.');
                }
            });
        });
    });
</script>


</body>
</html>

<?php
// Include the necessary files for database connection and session management
require_once 'db_connection.php';
require_once 'session.php';

// Check if the user is logged in
$isLoggedIn = isLoggedIn();

// Check if search term and page number are provided
if (isset($_POST['search']) && isset($_POST['page'])) {
    $search = $_POST['search'];
    $page = $_POST['page'];

    // Prepare the SQL query to retrieve products
    $perPage = 10;
    $start = ($page - 1) * $perPage;
    $query = "SELECT items.*, categories.name AS category_name FROM items JOIN categories ON items.category_id = categories.category_id";

    // If search term is provided, add a WHERE clause to the query
    if (!empty($search)) {
        $query .= " WHERE items.name LIKE '%$search%' OR categories.name LIKE '%$search%'";
    }

    // Add LIMIT and OFFSET for pagination
    $query .= " LIMIT $perPage OFFSET $start";

    // Execute the query
    $result = $conn->query($query);
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();

    // Generate the product table HTML
    $table = '<table id="product-table">';
    $table .= '<tr>';
    $table .= '<th>Name</th>';
    $table .= '<th>Category</th>';
    $table .= '<th>Description</th>';
    $table .= '<th>Quantity</th>';
    $table .= '<th>Price</th>';
    $table .= '</tr>';

    foreach ($items as $item) {
        $table .= '<tr>';
        $table .= "<td>{$item['name']}</td>";
        $table .= "<td>{$item['category_name']}</td>";
        $table .= "<td>{$item['description']}</td>";
        $table .= "<td>{$item['quantity']}</td>";
        $table .= "<td>{$item['price']}</td>";
        $table .= '</tr>';
    }

    $table .= '</table>';

    // Generate the pagination links HTML
    $pagination = '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';

    $totalItems = countItems($search);
    $totalPages = ceil($totalItems / $perPage);

    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $page) ? ' active' : '';
        $pagination .= '<li class="page-item' . $activeClass . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }

    $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';

    // Prepare the response data
    $response = array(
        'table' => $table,
        'pagination' => $pagination
    );

    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Function to count the total number of items based on the search term
function countItems($search) {
    global $conn;

    $query = "SELECT COUNT(*) AS total FROM items JOIN categories ON items.category_id = categories.category_id";

    // If search term is provided, add a WHERE clause to the query
    if (!empty($search)) {
        $query .= " WHERE items.name LIKE '%$search%' OR categories.name LIKE '%$search%'";
    }

    $result = $conn->query($query);
    $data = $result->fetch_assoc();
    $result->free_result();

    return $data['total'];
}

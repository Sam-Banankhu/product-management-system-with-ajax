<?php
require_once 'db_connection.php';

if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];

    // Delete the item from the items table
    $query = "DELETE FROM items WHERE item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $stmt->close();

    $response = [
        'success' => true,
        'message' => 'Item deleted successfully.'
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid item ID.'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

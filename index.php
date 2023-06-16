<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .table-wrapper {
            padding: 0 15px; /* Add spacing on the left and right */
        }
        .search-form {
            margin-right: 15px; /* Add spacing on the right */
        }
        .table-heading {
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php
    // Include the necessary files for database connection and session management
    include("header.php");

    require_once 'db_connection.php'; 
    require_once 'session.php'; 
    ?>

    <div class="container">
        <div class="search-form">
            <h2>Search Products</h2>
            <form id="search-form">
                <input type="text" name="search" placeholder="Enter search term" required>
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>

        <div class="table-wrapper">
            <h2 class="table-heading">Product List</h2>
            <div id="product-table-container"></div>
            <nav id="pagination-container" aria-label="Pagination">
                <ul class="pagination"></ul>
            </nav>
        </div>
    </div>

    <footer>
        <?php include("footer.php");?>
    </footer>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load initial product list
            loadProductList();

            // Handle search form submission
            $('#search-form').submit(function(e) {
                e.preventDefault();
                loadProductList();
            });

            // Handle pagination links
            $(document).on('click', '.pagination .page-link', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                loadProductList(page);
            });
        });

        // Function to load the product list with Ajax
        function loadProductList(page = 1) {
            var search = $('input[name="search"]').val();
            $.ajax({
                url: 'product_list.php',
                type: 'POST',
                data: { search: search, page: page },
                success: function(response) {
                    $('#product-table-container').html(response.table);
                    $('#pagination-container ul').html(response.pagination);
                }
            });
        }
    </script>
</body>
</html>

$(document).ready(function() {
    // AJAX request to delete a category
    $('.delete-category').click(function() {
      var categoryId = $(this).data('category-id');
      if (confirm("Are you sure you want to delete this category?")) {
        $.ajax({
          url: 'categories.php',
          type: 'POST',
          data: { delete_category: true, category_id: categoryId },
          success: function(response) {
            window.location.reload(); // Reload the page after successful deletion
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
  
<?php
session_start();
include('db.php');

// Function to delete a post and its associated comments
function deletePost($post_id) {
    global $conn;
    // Start a transaction
    $conn->begin_transaction();
    try {
        // Step 1: Delete associated comments
        $stmt_delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt_delete_comments->bind_param("i", $post_id);
        $stmt_delete_comments->execute();
        $stmt_delete_comments->close();
        
        // Step 2: Delete the post
        $stmt_delete_post = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt_delete_post->bind_param("i", $post_id);
        $stmt_delete_post->execute();
        $stmt_delete_post->close();

        // Commit the transaction if all queries executed successfully
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        throw $e; // Rethrow the exception to be caught by the calling code
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])) {
    $post_id = intval($_POST["post_id"]);
    
    try {
        deletePost($post_id);
        // Redirect to a success page or any other appropriate action after deleting the post
        header("Location: success.php");
        exit();
    } catch (Exception $e) {
        // Handle the exception, such as displaying an error message
        echo "Error deleting post: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>

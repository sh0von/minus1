<?php
session_start();
include('db.php');

// Function to delete a file
function deleteFile($file_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM markdown_files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file_id"])) {
    $file_id = intval($_POST["file_id"]);
    deleteFile($file_id);
    // Redirect to a success page or any other appropriate action after deleting the file
    header("Location: success.php");
    exit();
} else {
    echo "Invalid request.";
}
?>

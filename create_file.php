<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_id"])) {
    $post_id = $_POST["post_id"];

   
    $file_name = trim($_POST["file_name"]);

   
    $temp_content = "This is a temporary file content. You can edit it later.";

   
    $temp_filename = uniqid('') . '_' . $file_name . '.md';

   
    $upload_dir = "uploads/";

   
    $file_path = $upload_dir . $temp_filename;
    if (file_put_contents($file_path, $temp_content) !== false) {
       
        $sql = "INSERT INTO markdown_files (post_id, filename) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("is", $post_id, $temp_filename);
            if ($stmt->execute()) {
                echo "File created successfully.";
            } else {
                echo "Error: Failed to insert file details into database.";
            }
            $stmt->close();
        } else {
            echo "Error: Database error.";
        }
    } else {
        echo "Error: Failed to create the file.";
    }
} else {
    echo "Error: Invalid request.";
}
?>

<?php
session_start();
include('db.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
       
        $filename = $_FILES["file"]["name"];
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_size = $_FILES["file"]["size"];
        
       
        $file_type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($file_type != "md") {
            echo "Error: Only Markdown files are allowed.";
            exit;
        }
        
       
        if ($file_size > 5000000) {
            echo "Error: File size exceeds the limit.";
            exit;
        }
        
       
        $new_filename = uniqid() . '_' . $filename;
        
       
        $upload_dir = "uploads/";
        if (!move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
            echo "Error: Failed to move uploaded file.";
            exit;
        }
        
       
        $post_id = $_POST["post_id"];
        $sql = "INSERT INTO markdown_files (post_id, filename) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("is", $post_id, $new_filename);
            if ($stmt->execute()) {
                echo "File uploaded successfully.";
            } else {
                echo "Error: Failed to insert file details into database.";
            }
            $stmt->close();
        } else {
            echo "Error: Database error.";
        }
    } else {
        echo "Error: Invalid file upload.";
    }
} else {
    echo "Error: Invalid request.";
}
?>

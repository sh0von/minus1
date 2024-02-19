<?php

session_start();


include('db.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
   
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

   
    $sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    
   
    if ($stmt = $conn->prepare($sql)) {
       
       
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
       
       
        if ($stmt->execute()) {
           
           
            header("Location: devbin.php?slug=" . $slug);
            exit();
        } else {
           
           
            echo "Oops! Something went wrong. Please try again later.";
        }
       
       
        $stmt->close();
    } else {
       
        echo "Oops! Something went wrong. Please try again later.";
    }
}
?>

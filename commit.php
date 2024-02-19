<?php
session_start();
include('db.php');

// Function to retrieve commit details by commit key
function getCommitDetailsByKey($commit_key) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, commit_timestamp, commit_message, file_content FROM commits WHERE commit_key = ?");
    $stmt->bind_param("s", $commit_key);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($commit_id, $commit_timestamp, $commit_message, $file_content);
        $stmt->fetch();
        return array(
            "commit_id" => $commit_id,
            "commit_timestamp" => $commit_timestamp,
            "commit_message" => $commit_message,
            "file_content" => $file_content
        );
    } else {
        return false;
    }
}

// Check if the request is a GET request and the commit key is provided
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["key"])) {
    $commit_key = $_GET["key"];
    // Retrieve commit details for the specified commit key
    $commit_details = getCommitDetailsByKey($commit_key);
    if ($commit_details) {
        // Display the commit details
        $commit_id = $commit_details["commit_id"];
        $commit_timestamp = $commit_details["commit_timestamp"];
        $commit_message = $commit_details["commit_message"];
        $file_content = $commit_details["file_content"];

        // Include header
        include 'header.php';
?>

<section class="section">
    <div class="container">
        <div class="content">
            <h1>Commit Details</h1>
            <p><strong>Commit Key:</strong> <?php echo $commit_key; ?></p>
            <p><strong>Timestamp:</strong> <?php echo $commit_timestamp; ?></p>
            <p><strong>Message:</strong> <?php echo $commit_message; ?></p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content">
            <h2>File Content</h2>
            <pre><?php echo htmlspecialchars($file_content); ?></pre>
        </div>
    </div>
</section>

<?php
        // Include footer
        include 'footer.php';
    } else {
        echo "Commit not found.";
    }
} else {
    echo "Invalid request.";
}
?>

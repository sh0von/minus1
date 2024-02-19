<?php
session_start();
include('db.php');
require_once 'Parsedown.php';

// Function to retrieve commit history for a file
function getCommitHistory($file_id) {
    global $conn;
    $commits = array();
    $stmt = $conn->prepare("SELECT commit_key, commit_timestamp, commit_message FROM commits WHERE file_id = ? ORDER BY commit_timestamp DESC");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $commits[] = $row;
    }
    $stmt->close();
    return $commits;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
   
    $file_id = intval($_GET["id"]);

   
    $sql = "SELECT m.filename, p.id AS post_id, p.title, p.content, p.created_at, p.user_id, p.collaborators
            FROM markdown_files AS m 
            INNER JOIN posts AS p ON m.post_id = p.id 
            WHERE m.id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $file_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($filename, $post_id, $post_title, $post_content, $created_at, $user_id, $collaborators);
                $stmt->fetch();

                $upload_dir = "uploads/";

                if (file_exists($upload_dir . $filename)) {
                   
                    $file_content = file_get_contents($upload_dir . $filename);

                   
                    $commit_history = getCommitHistory($file_id);

                    ?>
                    <?php include 'header.php'; ?>

<section class="section">
    <div class="container">
        <div class="columns">
            <div class="column is-one-quarter">
                <div class="content">
                    <p><strong><?php echo $post_title; ?>/<?php echo substr($filename, 14); ?>
</strong></p>
                    <p><strong>Description:</strong><?php echo $post_content; ?></p>
                    <p><strong>Created at:</strong> <?php echo $created_at; ?></p>
                    <p><strong>Commit history</strong></p>
                    <ul>
<?php foreach ($commit_history as $commit) : ?>
    <li><?php echo $commit['commit_message']; ?> -<a href="commit.php?key=<?php echo $commit['commit_key']; ?>"> <?php echo $commit['commit_key']; ?></a></li>
<?php endforeach; ?>
</ul>
                    <!-- Add delete button -->
                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user_id || strpos($collaborators, $_SESSION['user_id']) !== false)) : ?>
                        <form method="POST" action="delete_file.php">
                            <input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
                            <button type="submit" class="button is-danger" onclick="return confirm('Are you sure you want to delete this file?')">Delete File</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="column">
                <div class="content"><?php 
                            $parsedown = new Parsedown();
                            echo $parsedown->text($file_content);
                        ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<?php
                } else {
                    echo "File not found.";
                }
            } else {
                echo "File not found.";
            }
        } else {
            echo "Error: Failed to execute database query.";
        }
    } else {
        echo "Error: Database error.";
    }
} else {
    echo "Invalid request.";
}
?>

<?php
session_start();
include('db.php');


if (!isset($_SESSION['user_id'])) {
   
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
// Function to create a new commit

function createCommit($file_id, $file_content, $commit_message, $commit_key, $committed_by) {
    global $conn;
    $timestamp = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO commits (commit_key, file_id, commit_timestamp, file_content, commit_message, committed_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssi", $commit_key, $file_id, $timestamp, $file_content, $commit_message, $committed_by);
    $stmt->execute();
    $stmt->close();
}
// Function to fetch a random word from the API
function getRandomWord() {
    $letters = 'abcdefghijklmnopqrstuvwxyz';
    $word = '';
    for ($i = 0; $i < 8; $i++) {
        $word .= $letters[rand(0, strlen($letters) - 1)];
    }
    return $word;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["file_id"], $_POST["file_content"])) {
  
     // After successfully updating the file content, create a new commit
     $file_id = intval($_POST["file_id"]);
     $file_content = $_POST["file_content"];
     $commit_message = isset($_POST["commit_message"]) && !empty($_POST["commit_message"]) ? $_POST["commit_message"] : "Update Code"; // Custom commit message or default "Update Code"
     $commit_key = getRandomWord(); // Get a random word as the commit key
     createCommit($file_id, $file_content, $commit_message, $commit_key, $user_id);
    
    $sql = "SELECT filename, post_id FROM markdown_files WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $file_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($filename, $post_id);
                $stmt->fetch();

               
                $user_id = $_SESSION['user_id'];
                $sql_check_access = "SELECT id FROM posts WHERE id = ? AND (user_id = ? OR FIND_IN_SET(?, collaborators))";
                if ($stmt_check_access = $conn->prepare($sql_check_access)) {
                    $stmt_check_access->bind_param("iii", $post_id, $user_id, $user_id);
                    if ($stmt_check_access->execute()) {
                        $stmt_check_access->store_result();
                        if ($stmt_check_access->num_rows == 1) {
                           
                            $upload_dir = "uploads/";
                            $filename = $upload_dir . $_POST["filename"];
                            if (file_put_contents($filename, $file_content) !== false) {
                               
                                header("Location: deventry.php?id=$file_id");
                                exit();
                            } else {
                                echo "Error: Failed to update file content.";
                            }
                        } else {
                           
                            header("Location: access_denied.php");
                            exit();
                        }
                    } else {
                        echo "Error: Failed to execute database query.";
                        exit();
                    }
                    $stmt_check_access->close();
                }
            } else {
                echo "File not found.";
                exit();
            }
        } else {
            echo "Error: Failed to execute database query.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error: Database error.";
        exit();
    }
}
function getCommitHistory($file_id) {
    global $conn;
    $commits = array();
    $stmt = $conn->prepare("SELECT commit_key,commit_message, commit_timestamp FROM commits WHERE file_id = ? ORDER BY commit_timestamp DESC");
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
    $commit_history = getCommitHistory($file_id);
   
    $sql = "SELECT filename, post_id FROM markdown_files WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $file_id);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($filename, $post_id);
                $stmt->fetch();
               
                $sql_post = "SELECT title, content,created_at FROM posts WHERE id = ?";
                if ($stmt_post = $conn->prepare($sql_post)) {
                    $stmt_post->bind_param("i", $post_id);
                    if ($stmt_post->execute()) {
                        $stmt_post->store_result();
                        if ($stmt_post->num_rows == 1) {
                            $stmt_post->bind_result($project_name, $description,$created_at);
                            $stmt_post->fetch();
                        }
                    }
                    $stmt_post->close();
                }
               
                $user_id = $_SESSION['user_id'];
                $sql_check_access = "SELECT id FROM posts WHERE id = ? AND (user_id = ? OR FIND_IN_SET(?, collaborators))";
                if ($stmt_check_access = $conn->prepare($sql_check_access)) {
                    $stmt_check_access->bind_param("iii", $post_id, $user_id, $user_id);
                    if ($stmt_check_access->execute()) {
                        $stmt_check_access->store_result();
                        if ($stmt_check_access->num_rows == 1) {
                           
                            $upload_dir = "uploads/";
                            $file_content = file_get_contents($upload_dir . $filename);
                        } else {
                           
                            header("Location: unauthorized.php");
                            exit();
                        }
                    } else {
                        echo "Error: Failed to execute database query.";
                        exit();
                    }
                    $stmt_check_access->close();
                }
            } else {
                echo "File not found.";
                exit();
            }
        } else {
            echo "Error: Failed to execute database query.";
            exit();
        }
        $stmt->close();
    } else {
        echo "Error: Database error.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<?php

include 'header.php'; 
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

<section class="section">
    <div class="container">
        <div class="columns">
            <div class="column is-one-quarter">
                <div class="content">
                    <h2><strong><?php echo $project_name; ?></strong></h2>
                    <p><strong>Description:</strong> <?php echo $description; ?></p>
                    <p><strong>Created at:</strong>
<?php
$formatted_date = date("jS F", strtotime($created_at));


echo $formatted_date;
?></p>     <ul>
<?php foreach ($commit_history as $commit) : ?>
    <li><?php echo $commit['commit_message']; ?> -<a href="commit.php?key=<?php echo $commit['commit_key']; ?>"> <?php echo $commit['commit_key']; ?></a></li>
<?php endforeach; ?>
</ul>
                </div>
            </div>
            <div class="column">
                <section class="section">
                    <div class="container">
                        <div class="columns">
                            <div class="column is-three-quarters">
                                <h1>Filename: <?php echo $filename; ?></h1><br>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                
        <input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
        <input type="hidden" name="filename" value="<?php echo $filename; ?>">
                                <textarea id="markdown-editor"name="file_content" style="min-height: 500px;"><?php echo $file_content; ?></textarea>
                                <input type="text" name="commit_message" class="input is-primary" placeholder="Commit Message"> <br><br>
        <input type="submit" value="Save Changes" class="button is-primary">
                                
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
 <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
  <script>
   
    var simplemde = new SimpleMDE({
      element: document.getElementById("markdown-editor"),
      spellChecker: false,
      placeholder: "Type here...",
      autofocus: true,
      forceSync: true,
      tabSize: 4,
      indentWithTabs: false,
      renderingConfig: {
        singleLineBreaks: false
      },
      toolbar: [
        "bold", "italic", "heading", "|",
        "quote", "unordered-list", "ordered-list", "|",
        "link", "image", "table","code", "|",
        "preview", "side-by-side", "|",
        "guide"
      ]
    });

   
    document.getElementById("save-button").addEventListener("click", function() {
     
      var markdownContent = simplemde.value();
     
      console.log("Markdown content:", markdownContent);
    });
  </script>

<?php include 'footer.php'; ?>

<?php
session_start();
include('db.php');



if(isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
   
    $sql = "SELECT id, title, content, created_at, user_id, collaborators FROM posts WHERE slug = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $slug);
        
       
        if ($stmt->execute()) {
            $stmt->store_result();
            
           
            if ($stmt->num_rows == 1) {
               
                $stmt->bind_result($post_id, $post_title, $post_content, $created_at, $user_id, $collaborators);
                $stmt->fetch();
                
               
                if (isset($_SESSION['user_id']) && ($user_id == $_SESSION['user_id'] || strpos($collaborators, $_SESSION['user_id']) !== false)) {
                    $can_edit = true;
                    $show_collaboration_form = true;
                    $can_upload_create = true;
                } else {
                    $can_edit = false;
                    $show_collaboration_form = false;
                    $can_upload_create = false;
                }
            } else {
               
                header("Location: devbox.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        
       
        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
} else {
   
    
header("Location: devbin.php?slug=" . $slug);
    exit();
}






if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $collaborator_email = trim($_POST["email"]);
    if (!filter_var($collaborator_email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format";
    } else {
       
        $sql_check_user = "SELECT id FROM users WHERE email = ?";
        if ($stmt_check_user = $conn->prepare($sql_check_user)) {
           
            $stmt_check_user->bind_param("s", $param_email);
           
            $param_email = $collaborator_email;
           
            if ($stmt_check_user->execute()) {
               
                $stmt_check_user->store_result();
                if ($stmt_check_user->num_rows == 1) {
                   
                    $stmt_check_user->bind_result($collaborator_id);
                    $stmt_check_user->fetch();
                   
                    if (strpos($collaborators, $collaborator_id) !== false) {
                        $collaboration_err = "This user is already a collaborator for this post";
                    } else {
                       
                        $sql_update_collaborators = "UPDATE posts SET collaborators = CONCAT_WS(',', collaborators, ?) WHERE slug = ?";
                        if ($stmt_update_collaborators = $conn->prepare($sql_update_collaborators)) {
                           
                            $stmt_update_collaborators->bind_param("ss", $param_collaborator_id, $param_slug);
                           
                            $param_collaborator_id = $collaborator_id;
                            $param_slug = $slug;
                           
                            if ($stmt_update_collaborators->execute()) {
                                $success_message = "Collaboration request sent successfully!";
                            } else {
                                $collaboration_err = "Oops! Something went wrong. Please try again later.";
                            }
                           
                            $stmt_update_collaborators->close();
                        }
                    }
                } else {
                   
                    $collaboration_err = "User with provided email does not exist";
                }
            } else {
                $collaboration_err = "Oops! Something went wrong. Please try again later.";
            }
           
            $stmt_check_user->close();
        }
    }
}


if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
   
   
}
$comments = array();
$sql_comments = "SELECT user_id, comment, created_at FROM comments WHERE post_id = ?";
if ($stmt_comments = $conn->prepare($sql_comments)) {
   
    $stmt_comments->bind_param("i", $post_id);
   
    if ($stmt_comments->execute()) {
       
        $result_comments = $stmt_comments->get_result();
       
        if ($result_comments->num_rows > 0) {
           
            while ($row = $result_comments->fetch_assoc()) {
                $comments[] = $row;
            }
        }
    }
   
    $stmt_comments->close();
}

?>
<?php include 'header.php'; ?>
<style>
    
    .repo-list {
        list-style-type: none;
    }

    .repo-item {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    .repo-name {
        font-weight: bold;
    }

    .repo-description {
        color: #666;
    }
</style>

<section class="section">
    <div class="container">
        <div class="columns">
            <div class="column is-one-quarter">
                
                <div class="content">
                    <h2><strong><?php echo $post_title; ?></strong></h2>
                    <p><strong>Description:</strong><?php echo $post_content; ?></p>
                    <p><strong>Created at:</strong> <?php echo $created_at; ?></p>
                </div>

                <?php if ($can_upload_create): ?>
                <div class="field">
    <label class="label">Upload or Create File</label>
    <div class="field">
      <div class="control">
        <label class="radio">
          <input type="radio" name="fileOption" value="upload" onclick="openUploadModal()">
          Upload File
        </label>
        <label class="radio">
          <input type="radio" name="fileOption" value="create" onclick="openCreateModal()">
          Create New File
        </label>
      </div>
    </div>
  </div>
    <?php endif; ?>
            </div>
        <div class="column">
         

 <section class="section">
    <div class="container">
    
      <h2 class="subtitle">Files</h2>
      <table class="table is-striped is-fullwidth">
        <thead>
          <tr>
            <th>File Name</th>
          </tr>
        </thead>
        <tbody>


        <?php
$sql_files = "SELECT id, filename FROM markdown_files WHERE post_id = ?";
if ($stmt_files = $conn->prepare($sql_files)) {
    $stmt_files->bind_param("i", $post_id);
    $stmt_files->execute();
    $result_files = $stmt_files->get_result();

    if ($result_files->num_rows > 0) {
        while ($row = $result_files->fetch_assoc()) {
            $filename = substr(pathinfo($row["filename"], PATHINFO_FILENAME), 14);
            echo '<tr><td><a href="deventry.php?id=' . $row["id"] . '">' . $filename . '</a></td></tr>';
        }
    } else {
        // No files found
        echo '<tr><td>No Dev Entry found</td></tr>';
    }

    $stmt_files->close();
}
?>

        
        </tbody>
      </table>

      <?php if ($can_upload_create): ?>
<div id="createModal" class="modal">
  <div class="modal-background"></div>
  <div class="modal-content">
        <form action="create_file.php" method="post">
<div class="box">
      <h2 class="title">Create File</h2>
      <div class="field">
        <label class="label">Enter file name</label>
        <div class="control">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
          <input name="file_name"" class="input" type="text" placeholder="File Name" required>
        </div>
      </div>
      <button class="button is-primary" type="submit">Create</button>
      
    </div></form>
  </div>
  <button class="modal-close is-large" aria-label="close" onclick="closeCreateModal()"></button>
</div>

<script>
  let myFile = Bulma('.file-name').data('file');
myFile.on('changed', function() {
    let filename = myFile.getFilename();
});</script>
<div id="uploadModal" class="modal">
  <div class="modal-background"></div>
  <div class="modal-content">
    
  <form action="upload.php" method="post" enctype="multipart/form-data">
    <div class="box">
      <h2 class="title">Upload File</h2>
      <div class="field">
        <div class="file is-primary has-name">
          <label class="file-label">
          <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
       
            <input class="file-input" required type="file" name="file" required>
            <span class="file-cta">
              <span class="file-icon">
                <i class="fas fa-upload"></i>
              </span>
              <span class="file-label">
                Choose a file…
              </span>
            </span><span class="file-name">Please select a file.</span>
          </label>
        </div>
      </div>
      <input type="submit" class="button is-primary" value="Upload">
    </div>
      </form>
  </div>
  <button class="modal-close is-large" aria-label="close" onclick="closeUploadModal()"></button>
</div>
<?php endif; ?>


<?php if ($show_collaboration_form): ?>
  <form action="" method="post">
<div class="field">
      <label class="label">Add Collaborator</label>
      <div class="control">
        <input class="input" type="email" name="email" placeholder="Enter collaborator's email">
      </div>
    </div>
    <div class="field">
      <div class="control">
        <button class="button is-primary" type="submit">Send Collaboration Request</button>
      </div><br>
      <?php if (isset($collaboration_err)): ?>
        <article class="message is-danger">
  <div class="message-body">
  <?php echo $collaboration_err; ?>
</div>
</article>
            <?php elseif (isset($success_message)): ?>  
              <article class="message is-success">
  <div class="message-body">
  <?php echo $success_message; ?>
</div>
</article>
            <?php endif; ?>
    </div><br>
    
    </form>
<?php endif; ?>
    
<?php if (isset($_SESSION['user_id'])): ?>
    <form action="add_comment.php" method="post">
        <article class="media">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <div class="media-content">
                <div class="field">
                    <label class="label">Add Comment</label>
                    <div class="control">
                        <textarea name="comment" class="textarea" placeholder="Add your comment here"></textarea>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <input type="submit" class="button is-primary" value="Submit Comment">
                    </div>
                </div>
            </div>
        </article>
    </form>
    <br>
<?php else: ?>
    <div class="notification is-warning">
        You need to <a href="login.php">sign in</a> or <a href="register.php">register</a> to comment.
    </div>
<?php endif; ?>

<div class="container">
    <h2 class="subtitle">Comments</h2>
    <div class="box">
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <article class="media">
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <strong><?php 
                                   
                                    $user_id = $comment['user_id'];
                                    $sql_username = "SELECT username FROM users WHERE id = ?";
                                    if ($stmt_username = $conn->prepare($sql_username)) {
                                        $stmt_username->bind_param("i", $user_id);
                                        $stmt_username->execute();
                                        $result_username = $stmt_username->get_result();
                                        if ($result_username->num_rows > 0) {
                                            $username = $result_username->fetch_assoc()['username'];
                                            echo $username;
                                        } else {
                                            echo "Unknown User";
                                        }
                                        $stmt_username->close();
                                    } else {
                                        echo "Unknown User";
                                    }
                                ?></strong> Commented :
                                <br>
                                <p class="is-size-4"><?php echo $comment['comment']; ?><br></p>
                            </p>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>
</div>
<br>


                        
<script>
  function openCreateModal() {
    document.getElementById('createModal').classList.add('is-active');
  }

  function closeCreateModal() {
    document.getElementById('createModal').classList.remove('is-active');
  }

  function openUploadModal() {
    document.getElementById('uploadModal').classList.add('is-active');
  }

  function closeUploadModal() {
    document.getElementById('uploadModal').classList.remove('is-active');
  }

  function createFile() {
    var fileName = document.getElementById('fileNameInput').value;
   
    console.log('Creating file:', fileName);
   
    closeCreateModal();
  }
</script>
<?php if ($can_edit): ?>
    <a class="button is-info" href="edit_post.php?slug=<?php echo $slug; ?>">Edit</a><br><br>
    <form method="post" action="delete_file.php">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <button type="submit" class="button is-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
    </form>
<?php endif; ?>
    </div>
  </section>
        </div>
      </div>
    </div>
  </section>
  <?php include 'footer.php';?>

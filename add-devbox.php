<?php
session_start();
include('db.php');


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


$title = $content = $slug = $skills = $category = "";
$title_err = $content_err = $slug_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

   
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter content for the post.";
    } else {
        $content = trim($_POST["content"]);
    }

   
    if (empty(trim($_POST["slug"]))) {
        $slug_err = "Please enter a slug.";
    } else {
        $slug = trim($_POST["slug"]);
    }

   
    if (empty($title_err) && empty($content_err) && empty($slug_err)) {
       
        $sql = "INSERT INTO posts (title, content, slug, skills, category, user_id) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
           
            $stmt->bind_param("sssssi", $param_title, $param_content, $param_slug, $param_skills, $param_category, $param_user_id);

           
            $param_title = $title;
            $param_content = $content;
            $param_slug = $slug;
            $param_skills = $_POST["skills"];
            $param_category = $_POST["category"];
            $param_user_id = $_SESSION['user_id'];

           
            if ($stmt->execute()) {
               
                header("Location: devbox.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

           
            $stmt->close();
        }
    }

   
    $conn->close();
}
?><?php include 'header.php'; ?>
<section class="section">
    <div class="container">
        <h1 class="title">Create Post</h1>
        <h2 class="subtitle">Please fill in the details of your new post.</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" name="title" type="text" placeholder="Enter title" required>
                    <?php if (!empty($title_err)) echo '<p class="help is-danger">' . $title_err . '</p>'; ?>
                </div>
            </div>
            <div class="field">
                <label class="label">Slug</label>
                <div class="control">
                    <?php
                   
                    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
                    $slug = '';
                    for ($i = 0; $i < 16; $i++) {
                        $index = rand(0, strlen($chars) - 1);
                        $slug .= $chars[$index];
                    }
                    echo '<input class="input" name="slug" type="text" value="' . $slug . '" readonly>';
                    ?>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea name="content" class="textarea" placeholder="Enter content" required></textarea>
                    <?php if (!empty($content_err)) echo '<p class="help is-danger">' . $content_err . '</p>'; ?>
                </div>
            </div>
            <div class="field">
                <label class="label">Skills (separated by comma)</label>
                <div class="control">
                    <input class="input" name="skills" type="text" placeholder="Enter skills" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Category</label>
                <div class="control">
                    <div class="select">
                        <select name="category" required>
                            <option value="">Select a category</option>
                            <option value="Science">Science</option>
                            <option value="Education">Education</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Coding">Coding</option>
                        </select>
                        <?php if (!empty($category_err)) echo '<p class="help is-danger">' . $category_err . '</p>'; ?>
                    </div>
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <button class="button is-link">Create</button>
                </div>
                <div class="control">
                    <button class="button is-text">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</section>
<?php include 'footer.php'; ?>

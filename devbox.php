<?php
session_start();
include('db.php');

$postsPerPage = 10;
$user_id = $_SESSION['user_id'];
$sql_user_skills = "SELECT skills FROM posts WHERE user_id = ?";
$stmt_user_skills = $conn->prepare($sql_user_skills);
$stmt_user_skills->bind_param("i", $user_id);
$stmt_user_skills->execute();
$result_user_skills = $stmt_user_skills->get_result();

$user_skills = array();

while ($row = $result_user_skills->fetch_assoc()) {
    $user_skills = array_merge($user_skills, explode(',', $row['skills']));
}

$user_skills = array_unique($user_skills);


$sql_count_posts = "SELECT COUNT(*) AS total FROM posts";
$stmt_count_posts = $conn->prepare($sql_count_posts);
$stmt_count_posts->execute();
$result_count_posts = $stmt_count_posts->get_result();
$totalPosts = $result_count_posts->fetch_assoc()['total'];
$stmt_count_posts->close();


$totalPages = ceil($totalPosts / $postsPerPage);


$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;


$offset = ($page - 1) * $postsPerPage;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $search_category = $_POST['search_category'];

   
    $sql = "SELECT id, title, content, created_at, skills FROM posts WHERE ";
    $sql .= "(title LIKE '%$search_query%' OR content LIKE '%$search_query%') ";
    if (!empty($search_category)) {
        $sql .= "AND category = '$search_category'";
    }

   
    $stmt_all_posts = $conn->prepare($sql);
    $stmt_all_posts->execute();
    $result_all_posts = $stmt_all_posts->get_result();

    $posts = array();

    while ($row = $result_all_posts->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt_all_posts->close();
} else {
   
    $sql_all_posts = "SELECT id, title, content, slug, created_at, skills FROM posts ORDER BY created_at DESC LIMIT ?, ?";
    $stmt_all_posts = $conn->prepare($sql_all_posts);
    $stmt_all_posts->bind_param("ii", $offset, $postsPerPage);
    $stmt_all_posts->execute();
    $result_all_posts = $stmt_all_posts->get_result();

    $posts = array();

    while ($row = $result_all_posts->fetch_assoc()) {
        $posts[] = $row;
    }
    $stmt_all_posts->close();
}


usort($posts, function ($a, $b) use ($user_skills) {
    $matches_a = array_intersect($user_skills, explode(',', $a['skills']));
    $matches_b = array_intersect($user_skills, explode(',', $b['skills']));

    return count($matches_b) - count($matches_a);
});
?>

    <?php include 'header.php'; ?>

    <style>
    
    .repo-list {
        list-style-type: none;
    }
    .pagination-link {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    justify-content: center;
    align-items: center;
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
            <h2 class="title">All Posts</h2>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="field is-grouped">
                    <div class="control">
                        <input class="input" type="text" name="search_query" placeholder="Search by keywords">
                    </div>
                    <div class="control">
                        <div class="select">
                            <select name="search_category">
                                <option value="">All Categories</option>
                                <option value="Science">Science</option>
                                <option value="Education">Education</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Coding">Coding</option>
                            </select>
                        </div>
                    </div><hr>
                    <div class="control">
                        <button class="button is-primary" type="submit" name="search">Search</button>
                    </div>
                </div>
            </form>

            <ul class="repo-list">
                <?php foreach ($posts as $post) : ?>
                    <li class="repo-item">
                        <div>
                            <a href="devbin.php?slug=<?php echo $post['slug']; ?>" class="repo-name"><?php echo $post['title']; ?></a>
                            <p class="repo-description"><?php echo $post['content']; ?></p>
                        </div>
                        <div class="tags" style="padding-top:5px">
                            <span class="tag"><?php echo $post['created_at']; ?></span>
                            <?php 
                           
                            if (isset($post['skills'])) {
                                $skills = explode(',', $post['skills']);
                                foreach ($skills as $skill) {
                                    echo '<span class="tag">' . trim($skill) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
<br>
        <div class="pagination is-centered" role="navigation" aria-label="pagination">
            <ul class="pagination-list">
                <?php if ($page > 1) : ?>
                    <li><a class="pagination-link" href="?page=<?php echo ($page - 1); ?>"><<</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li><a class="pagination-link <?php echo ($i === $page) ? 'is-current' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $totalPages) : ?>
                    <li><a class="pagination-link" href="?page=<?php echo ($page + 1); ?>">>></a></li>
                <?php endif; ?>
            </ul>
        </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>

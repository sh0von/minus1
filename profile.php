<?php
session_start();
include('db.php');
if (!isset($_SESSION['user_id'])) {
   
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];


$postsPerPage = 4;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $postsPerPage;


$sql_user = "SELECT username FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();


$filter = isset($_GET['filter']) ? $_GET['filter'] : '';


if ($filter === 'owner') {
    $sql_count_posts = "SELECT COUNT(*) AS total FROM posts WHERE user_id = ?";
    $stmt_count_posts = $conn->prepare($sql_count_posts);
    $stmt_count_posts->bind_param("i", $user_id);
} elseif ($filter === 'collaborator') {
    $sql_count_posts = "SELECT COUNT(*) AS total FROM posts WHERE FIND_IN_SET(?, collaborators)";
    $stmt_count_posts = $conn->prepare($sql_count_posts);
    $stmt_count_posts->bind_param("i", $user_id);
} else {
    $sql_count_posts = "SELECT COUNT(*) AS total FROM posts WHERE user_id = ? OR FIND_IN_SET(?, collaborators)";
    $stmt_count_posts = $conn->prepare($sql_count_posts);
    $stmt_count_posts->bind_param("ii", $user_id, $user_id);
}
$stmt_count_posts->execute();
$result_count_posts = $stmt_count_posts->get_result();
$totalPosts = $result_count_posts->fetch_assoc()['total'];
$stmt_count_posts->close();


if ($filter === 'owner') {
    $sql_posts = "SELECT id, title,slug, content, created_at, skills, 'Owner' AS role FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?";
    $stmt_posts = $conn->prepare($sql_posts);
    $stmt_posts->bind_param("iii", $user_id, $offset, $postsPerPage);
} elseif ($filter === 'collaborator') {
    $sql_posts = "SELECT id, title, content,slug, created_at, skills, 'Collaborator' AS role FROM posts WHERE FIND_IN_SET(?, collaborators) ORDER BY created_at DESC LIMIT ?, ?";
    $stmt_posts = $conn->prepare($sql_posts);
    $stmt_posts->bind_param("iii", $user_id, $offset, $postsPerPage);
} else {
    $sql_posts = "SELECT id, title, content,slug, created_at, skills, CASE WHEN user_id = ? THEN 'Owner' ELSE 'Collaborator' END AS role FROM posts WHERE user_id = ? OR FIND_IN_SET(?, collaborators) ORDER BY created_at DESC LIMIT ?, ?";
    $stmt_posts = $conn->prepare($sql_posts);
    $stmt_posts->bind_param("iiiii", $user_id, $user_id, $user_id, $offset, $postsPerPage);
}

$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();

$posts = array();
while ($row = $result_posts->fetch_assoc()) {
    $posts[] = $row;
}
$stmt_posts->close();


$totalPages = ceil($totalPosts / $postsPerPage);
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
        <div class="columns">
            <div class="column is-one-quarter">
                <figure class="image is-128x128">
                    <img class="is-rounded"
                        src="https://avataaars.io/?avatarStyle=Circle&topType=Hat&accessoriesType=Blank&facialHairType=Blank&clotheType=CollarSweater&clotheColor=Red&eyeType=WinkWacky&eyebrowType=RaisedExcitedNatural&mouthType=Twinkle&skinColor=Brown"
                        alt="Profile Picture">
                </figure><br>
                <h1 class="title"><?php echo $user['username']; ?></h1>
                <p class="subtitle">@<?php echo $user['username']; ?></p>
                <p>Bio: Software Developer</p><br>
                <a href="add-devbox.php" class="button is-info is-fullwidth">Add DevBox</a><br>
<form action="" method="get">
    <div class="field">
        <label class="label">Filter:</label>
        <div class="control">
            <label class="radio">
                <input type="radio" name="filter" value="all" <?php echo ($filter === 'all') ? 'checked' : ''; ?>>
                All
            </label>
            <label class="radio">
                <input type="radio" name="filter" value="owner" <?php echo ($filter === 'owner') ? 'checked' : ''; ?>>
                Owner
            </label>
            <label class="radio">
                <input type="radio" name="filter" value="collaborator" <?php echo ($filter === 'collaborator') ? 'checked' : ''; ?>>
                Collaborator
            </label>
        </div>
    </div>
    <div class="field is-grouped">
        <div class="control">
            <button type="submit" class="button is-link">Apply</button>
        </div>
    </div>
</form>


            </div>
            <div class="column">
                <h2 class="title">Repositories</h2>
                <ul class="repo-list">
                    
                    <?php foreach ($posts as $post): ?>
                    <li class="repo-item">
                        <div>
                            <a href="devbin.php?slug=<?php echo $post['slug']; ?>"
                                class="repo-name"><?php echo $post['title']; ?></a>(<?php echo $post['role']; ?>)
                            <p class="repo-description"><?php echo $post['content']; ?></p>
                        </div>
                        <div class="tags" style="padding-top:5px">
                            <span class="tag"><?php echo $post['created_at']; ?></span>
                            <?php 
                                $skills = explode(',', $post['skills']);
                                foreach($skills as $skill) {
                                    echo '<span class="tag">' . trim($skill) . '</span>';
                                }
                            ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    
                </ul> <?php if (empty($posts)): ?>
            <p>No Dev Box found.</p>
        <?php else: ?><?php endif; ?>
            </div>
        </div>
<div class="pagination is-centered" role="navigation" aria-label="pagination">
    <ul class="pagination-list">
        <?php if ($page > 1): ?>
        <li><a class="pagination-link" href="?page=<?php echo ($page - 1); ?><?php echo $filter ? '&filter=' . $filter : ''; ?>"><<</a></li>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li><a class="pagination-link <?php echo ($i === $page) ? 'is-current' : ''; ?>" href="?page=<?php echo $i; ?><?php echo $filter ? '&filter=' . $filter : ''; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <li><a class="pagination-link" href="?page=<?php echo ($page + 1); ?><?php echo $filter ? '&filter=' . $filter : ''; ?>">>></a></li>
        <?php endif; ?>
    </ul>
</div>
    </div>
</section>
<?php include 'footer.php'; ?>

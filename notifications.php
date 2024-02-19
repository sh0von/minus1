<?php
include('db.php');
$sql = "SELECT n.id, n.post_id, n.additional_info, n.created_at, p.title AS post_name, p.slug AS post_slug 
        FROM notifications n 
        LEFT JOIN posts p ON n.post_id = p.id 
        WHERE n.recipient_id = ? 
        ORDER BY n.created_at DESC";

if ($stmt = $conn->prepare($sql)) {
   
    $stmt->bind_param("i", $user_id);
   
    if ($stmt->execute()) {
       
        $result = $stmt->get_result();
       
        if ($result->num_rows > 0) {
            ?>
            <div class="modal" id="notificationModal">
                <div class="modal-background"></div>
                <div class="modal-content">
                    <div class="box">
                        <h1 class="title is-4 has-text-centered">Notifications</h1>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="notification has-background-primary">
                                <p class="subtitle is-6 has-text-white">
                                    <?php if (!empty($row['post_slug'])): ?>
                                        You've got collaboration requests for <a href="devbin.php?slug=<?php echo $row['post_slug']; ?>">
                                        <strong class="has-text-white"><?php echo $row['post_name']; ?></strong></a><br>
                                    <?php endif; ?>
                                    <strong class="has-text-white">Created at:</strong> <?php echo formatDateTime($row['created_at']); ?><br>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <button class="modal-close is-large" aria-label="close"></button>
            </div>

            <?php
        } else {
        }
    } else {
    }
   
    $stmt->close();
}


function formatDateTime($dateTimeStr) {
    $dateTime = strtotime($dateTimeStr);
    $currentTime = time();
    $diff = $currentTime - $dateTime;
    if ($diff < 604800) {
        return date('l, h:i A', $dateTime);
    } else {
        return date('F j, Y', $dateTime);
    }
}
?>

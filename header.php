<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minus One</title>
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="https://jenil.github.io/bulmaswatch/united/bulmaswatch.min.css">
</head>
<body>
<nav class="navbar is-primary" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="../">
        <strong>Minus 1</strong>
      </a>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a id="notificationIcon" role="button" class="navbar-item" aria-expanded="false">
          <span class="icon has-text-white">
            <i data-feather="bell"></i>
          </span>
        </a>
      <?php endif; ?>
      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>
    <div id="navbarMenu" class="navbar-menu">
      <div class="navbar-end">
        <a class="navbar-item" href="../devbox">Dev Box</a>
        <?php if(isset($_SESSION['user_id'])): ?>
          
        <a class="navbar-item" href="../profile">Profile</a>
          <a class="navbar-item" href="#">Logout</a>
        <?php else: ?>
          <a class="navbar-item" href="../login.php">Login</a>
          <a class="navbar-item" href="../register.php">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

<?php include'notifications.php';?><script>
  document.addEventListener('DOMContentLoaded', function () {
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationModal = document.getElementById('notificationModal');

    notificationIcon.addEventListener('click', function () {
      notificationModal.classList.toggle('is-active');
    });
  });
</script>

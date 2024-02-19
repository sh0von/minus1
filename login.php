<?php
session_start();
include('db.php');

if(isset($_SESSION['username'])) {
    header("Location: profile.php");
    exit();
}

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password_hash FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password_hash"])) {
            $_SESSION['user_id'] = $row["id"];
            $_SESSION['username'] = $row["username"];
            header("Location: profile.php");
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "User not found";
    }
}
?>
<?php include 'header.php';?>
 <section class="section">
    <div class="container">
      <div class="columns is-centered">
        <div class="column is-half">
          <h1 class="title has-text-centered">Login</h1>
          <div class="box">
            <form  method="post" action="">
              <div class="field">
                <label class="label">Email</label>
                <div class="control">
                  <input class="input" type="email" name="email" required placeholder="Enter your email">
                </div>
              </div>
              <div class="field">
                <label class="label">Password</label>
                <div class="control">
                  <input class="input" type="password" name="password" placeholder="Enter your password">
                </div>
              </div>
              <div class="field">
                <div class="control">
                  <button class="button is-primary is-fullwidth" name="login">Login</button>
                </div>
              </div>
            </form>
          </div>
          <?php if(isset($error)) { ?>
            <div class="notification is-danger">
              <?php echo $error; ?>
            </div>
          <?php } ?>
          <p class="has-text-centered">
            Don't have an account? <a href="#">Sign up here</a>
          </p>
        </div>
      </div>
    </div>
  </section>
  <?php include 'footer.php';?>

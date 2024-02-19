<?php
session_start();
include('db.php');

if(isset($_SESSION['username'])) {
    header("Location: profile.php");
    exit();
}


$username = "";
$email = "";

if(isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

   
    if(empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $username = mysqli_real_escape_string($conn, $username);
        $email = mysqli_real_escape_string($conn, $email);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $check_email_query = "SELECT * FROM users WHERE email='$email'";
        $check_email_result = $conn->query($check_email_query);
        if ($check_email_result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            $insert_query = "INSERT INTO users (username, email, password_hash) VALUES ('$username', '$email', '$password_hash')";
            if ($conn->query($insert_query) === TRUE) {
                echo "Registration successful";

                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . $insert_query . "<br>" . $conn->error;
            }
        }
    }
}
?>

<?php include 'header.php';?> 
<section class="section">
    <div class="container">
      <div class="columns is-centered">
        <div class="column is-half">
          <h1 class="title has-text-centered">Register</h1>
          <div class="box">
          <form method="post" action="">
              <div class="field">
                <label class="label">Full Name</label>
                <div class="control">
                  <input class="input" type="text" name="username" placeholder="Enter your full name" value="<?php echo $username; ?>">
                </div>
              </div>
              <div class="field">
                <label class="label">Email</label>
                <div class="control">
                  <input class="input" name="email" type="email" placeholder="Enter your email" value="<?php echo $email; ?>">
                </div>
              </div>
              <div class="field">
                <label class="label">Password</label>
                <div class="control">
                  <input class="input" name="password" type="password" placeholder="Enter your password">
                </div>
              </div>
              <div class="field">
                <label class="label">Confirm Password</label>
                <div class="control">
                  <input class="input" name="confirm_password" type="password" placeholder="Confirm your password">
                </div>
              </div>
              <div class="field">
                <div class="control">
                  <button class="button is-primary is-fullwidth" name="register">Register</button>
                </div>
              </div>
            </form>
          </div>   
          
          
    <?php if(isset($error)) { echo '<div class="notification is-danger">' . $error . '</div>'; } ?>

         
          <p class="has-text-centered">
            Already have an account? <a href="#">Login here</a>
          </p>
        </div>
      </div>
    </div>
  </section>
  <?php include 'footer.php';?>

<?php
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | The Mobile Store</title>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <link rel="stylesheet" href="../assets/css/main.css" />
</head>

<body>
  <div class="form-container">
    <div class="form-wrapper">
      <div class="form-image-column">
        <img src="../assets/images/svg/register.svg" alt="Register Image" class="login-image">
      </div>
      <div class="form-content-column">
        <div class="form-card">
          <div class="form-header">
            <h2 class="form-header">Create Account</h2>
            <p class="form-header">Join The Mobile Store</p>
          </div>
          <div id="alert-container"></div>


          <form action="../includes/auth.php" method="POST" class="auth-form">
            <div class="form-group">
              <label for="fullname" class="form-label">Full Name</label>
              <div class="form-input">
                <span>
                  <ion-icon name="person-outline"></ion-icon>
                </span>
                <input type="text" id="fullname" name="fullname" required placeholder="Your full name" />
              </div>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <div class="form-input">
                <span>
                  <ion-icon name="mail-outline"></ion-icon>
                </span>
                <input type="email" id="email" name="email" placeholder="Enter your email" required />
              </div>
            </div>

            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <div class="form-input">
                <span>
                  <ion-icon name="lock-closed-outline"></ion-icon>
                </span>
                <input type="password" id="password" name="password" placeholder="Enter your password" required />
              </div>
            </div>

            <div class="form-group">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <div class="form-input">
                <span>
                  <ion-icon name="lock-closed-outline"></ion-icon>
                </span>
                <input type="password" id="confirm_password" name="confirm_password" required
                  placeholder="Confirm your password" />
              </div>
            </div>

            <div class="form-terms">
              <label>
                <input type="checkbox" name="terms" required /> I agree to the
                <a href="../t-c.html">Terms</a> and <a href="../t-c.html"> Conditions </a>
              </label>
            </div>

            <div class="form-submit">
              <button type="submit" class="button">Register</button>
            </div>
          </form>

          <div class="form-footer">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/js/auth.js"></script>
</body>

</html>
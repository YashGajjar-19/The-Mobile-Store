<?php
$error = isset($_GET['error']) ? $_GET['error'] : '';
$page_title = 'Register | The Mobile Store';
require_once '../includes/header.php';
?>

<body>
  <div class="form-container">
    <div class="form-wrapper">

      <div class="form-image-column">
        <img src="../assets/images/svg/register.svg" alt="Register Image" class="login-image">
      </div>

      <div class="form-content-column">
        <div class="form-card">
          <!-- Header -->
          <div class="form-header">
            <h2 class="form-header">Create Account</h2>
            <p class="form-header">Join The Mobile Store</p>
          </div>

          <!-- Alert box -->
          <div id="alert-container"></div>

          <!-- Registeration form -->
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
                <a href="../pages/t-c.php">Terms</a> and <a href="../pages/t-c.php"> Conditions </a>
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
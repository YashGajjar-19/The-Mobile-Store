document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const strengthMeter = document.getElementById("strength-meter");
  const strengthBar = document.getElementById("strength-bar-fill");
  const alertContainer = document.getElementById("alert-container");

  // --- Alert Message Display ---
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get("error");
  const success = urlParams.get("success");

  if (error) {
    let message = "";
    // Define messages for different errors
    const errorMessages = {
      "passwords-length": "Password must be at least 8 characters long.",
      "passwords-uppercase": "Password requires an uppercase letter.",
      "passwords-lowercase": "Password requires a lowercase letter.",
      "passwords-number": "Password requires a number.",
      "passwords-special": "Password requires a special character.",
      passwordmismatch: "Passwords do not match.",
      emailtaken: "This email is already registered. Please login.",
      invalidpassword: "The password you entered is incorrect.",
      usernotfound: "No account found with that email address.",
      dberror: "An internal error occurred. Please try again later.",
    };
    message =
      errorMessages[error] || "An unknown error occurred. Please try again.";

    // Use your existing alert classes!
    alertContainer.innerHTML = `
          <div class="alert alert-error" role="alert">
              <ion-icon name="warning-outline" class="alert-icon"></ion-icon>
              <span>${message}</span>
              <button class="alert-close">&times;</button>
          </div>
      `;
  }

  if (success === "registered") {
    alertContainer.innerHTML = `
            <div class="alert alert-success" role="alert">
               <ion-icon name="checkmark-done-circle-outline" class="alert-icon"></ion-icon>
                <span>Registration successful! Please log in.</span>
                <button class="alert-close">&times;</button>
            </div>
        `;
  }

  // --- Close Button Functionality ---
  alertContainer.addEventListener("click", function (e) {
    if (e.target.classList.contains("alert-close")) {
      e.target.parentElement.style.display = "none";
    }
  });
});

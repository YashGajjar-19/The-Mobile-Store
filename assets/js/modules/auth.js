document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const strengthMeter = document.getElementById("strength-meter");
  const strengthBar = document.getElementById("strength-bar-fill");
  const alertContainer = document.getElementById("alert-container");

  // --- Alert Message Display ---
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get("error");

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
    };
    message =
      errorMessages[error] || "An unknown error occurred. Please try again.";

    // Use your existing alert classes!
    alertContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <span class="material-symbols-rounded">error</span>
                    <span>${message}</span>
                </div>
            `;
  }

  // --- Password Visibility Toggle ---
  function setupToggle(toggleId, inputId) {
    const toggleElement = document.getElementById(toggleId);
    const inputElement = document.getElementById(inputId);
    if (!toggleElement || !inputElement) return;

    toggleElement.addEventListener("click", function () {
      const type =
        inputElement.getAttribute("type") === "password" ? "text" : "password";
      inputElement.setAttribute("type", type);
      this.setAttribute(
        "name",
        type === "password" ? "eye-outline" : "eye-off-outline"
      );
    });
  }
  setupToggle("togglePassword", "password");
  setupToggle("toggleConfirmPassword", "confirm_password");

  // --- Password Strength Meter Logic ---
  passwordInput.addEventListener("input", function () {
    const password = passwordInput.value;

    // Show or hide the strength bar based on input
    if (password.length > 0) {
      strengthMeter.classList.remove("hidden");
    } else {
      strengthMeter.classList.add("hidden");
    }

    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^A-Za-z0-9]/)) strength++;

    let effectiveStrength = Math.min(strength, 4);
    if (password.length > 0 && strength < 2) {
      effectiveStrength = 1;
    } else if (password.length === 0) {
      effectiveStrength = 0;
    }

    strengthBar.setAttribute("data-strength", effectiveStrength);
  });
});

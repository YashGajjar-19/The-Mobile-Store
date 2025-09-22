document.addEventListener("DOMContentLoaded", () => {
  // --- Slider Functionality ---
  const slider = document.querySelector(".slider-container");
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  let currentIndex = 0;
  let slideInterval;

  if (slider && slides.length > 0 && dots.length > 0) {
    const showSlide = (index) => {
      slider.style.transform = `translateX(-${index * 100}%)`;
      const activeDot = document.querySelector(".dot.active");
      if (activeDot) activeDot.classList.remove("active");
      dots[index].classList.add("active");
      currentIndex = index;
    };
    const nextSlide = () => showSlide((currentIndex + 1) % slides.length);
    const startSlideShow = () => slideInterval = setInterval(nextSlide, 3000);
    const stopSlideShow = () => clearInterval(slideInterval);

    dots.forEach((dot, i) => dot.addEventListener("click", () => {
      stopSlideShow();
      showSlide(i);
      startSlideShow();
    }));
    showSlide(0);
    startSlideShow();
  }

  // --- Search Bar & Mobile Menu ---
  const searchBtn = document.querySelector(".search-btn");
  const searchInput = document.querySelector(".search-input");
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const mobileMenu = document.querySelector(".mobile-menu");

  if (searchBtn && searchInput) {
    searchBtn.addEventListener("click", (e) => {
      if (!searchInput.classList.contains("active")) {
        e.preventDefault();
        searchInput.classList.add("active");
        searchInput.focus();
      }
    });
  }
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", (e) => e.stopPropagation());
    document.body.addEventListener('click', (e) => mobileMenu.classList.toggle("active", mobileMenuBtn.contains(e.target)));
  }
  document.addEventListener("click", (e) => {
    if (searchInput && !searchBtn.contains(e.target) && !searchInput.contains(e.target)) {
      searchInput.classList.remove("active");
    }
  });
});

// --- START: CORRECTED WISHLIST FUNCTIONALITY ---
document.addEventListener("click", function (e) {
  const wishlistButton = e.target.closest(".wishlist-btn");
  if (wishlistButton) {
    e.stopPropagation(); // Prevents the event from firing on parent elements

    // Prevent double-clicks while a request is in progress
    if (wishlistButton.disabled) {
      return;
    }
    wishlistButton.disabled = true;

    const productId = wishlistButton.dataset.productId;
    const wishlistHandlerPath = '/The-Mobile-Store/handlers/wishlist_handler.php';
    const loginPath = '/The-Mobile-Store/user/login.php';

    fetch(wishlistHandlerPath, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "product_id=" + productId,
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      if (data.status === "added") {
        wishlistButton.classList.add("active");
        showAlert("success", data.message);
      } else if (data.status === "removed") {
        wishlistButton.classList.remove("active");
        showAlert("success", data.message);
      } else {
        if (data.message && data.message.includes("logged in")) {
          window.location.href = loginPath;
        } else {
          showAlert("error", data.message || "An unknown error occurred.");
        }
      }
    })
    .catch(error => {
      console.error("Wishlist Error:", error);
      showAlert("error", "An unexpected error occurred.");
    })
    .finally(() => {
      // Re-enable the button after the action is complete
      wishlistButton.disabled = false;
    });
  }
});

function showAlert(type, message) {
  let alertContainer = document.querySelector(".alert-container");
  if (!alertContainer) {
    alertContainer = document.createElement("div");
    alertContainer.className = "alert-container";
    document.body.appendChild(alertContainer);
  }

  const alert = document.createElement("div");
  const iconName = type === "success" ? "checkmark-done-circle-outline" : "warning-outline";
  alert.className = `alert alert-${type}`;
  alert.innerHTML = `<ion-icon name="${iconName}" class="alert-icon"></ion-icon><span>${message}</span><button class="alert-close">&times;</button>`;
  alertContainer.appendChild(alert);

  setTimeout(() => {
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 300);
  }, 5000);

  alert.querySelector(".alert-close").addEventListener("click", () => alert.remove());
}

// --- Quantity Setter Functionality ---
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('quantity-btn')) {
        const action = e.target.dataset.action;
        const wrapper = e.target.closest('.quantity-input-wrapper');
        const input = wrapper.querySelector('.quantity-input');
        
        let currentValue = parseInt(input.value, 10);
        const min = parseInt(input.min, 10);
        const max = parseInt(input.max, 10);

        if (action === 'increment' && currentValue < max) {
            input.value = currentValue + 1;
        } else if (action === 'decrement' && currentValue > min) {
            input.value = currentValue - 1;
        }
        
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
});
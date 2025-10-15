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

  // --- Initialize Wishlist ---
  initializeWishlist();
});

// =================================================================
// ===== WISHLIST FUNCTIONALITY ====================================
// =================================================================

function initializeWishlist() {
  const wishlistButtons = document.querySelectorAll('.wishlist-btn');

  wishlistButtons.forEach(button => {
    button.addEventListener('click', handleWishlistClick);
  });

  console.log('Wishlist initialized with', wishlistButtons.length, 'buttons');
}

function handleWishlistClick(event) {
  event.preventDefault();
  event.stopPropagation();

  const button = event.currentTarget;
  const productId = button.dataset.productId;

  console.log('Wishlist clicked - Product ID:', productId);

  if (!productId) {
    console.error('Missing product ID');
    return;
  }

  if (button.disabled) return;

  toggleWishlistItem(productId, button);
}

async function toggleWishlistItem(productId, button) {
  // Visual feedback
  button.disabled = true;
  button.style.opacity = '0.6';
  button.style.cursor = 'wait';

  try {
    console.log('Sending wishlist request...');

    // FIXED PATH: Use the correct relative path
    const response = await fetch('./handlers/wishlist_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `product_id=${productId}`
    });

    console.log('Response status:', response.status);

    // Check if response is JSON
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text();
      console.error('Non-JSON response:', text);
      throw new Error('Server returned non-JSON response. Check the file path.');
    }

    const data = await response.json();
    console.log('Server response:', data);

    if (!response.ok) {
      throw new Error(data.message || 'Server error');
    }

    // Handle success
    if (data.status === 'added') {
      button.classList.add('active');
      button.dataset.inWishlist = 'true';
      showAlert('success', '✓ Added to wishlist!');
    } else if (data.status === 'removed') {
      button.classList.remove('active');
      button.dataset.inWishlist = 'false';
      showAlert('success', 'Removed from wishlist');
    } else if (data.status === 'error') {
      if (data.message.includes('logged in')) {
        showAlert('error', 'Please login to use wishlist');
        setTimeout(() => {
          window.location.href = './user/index.php';
        }, 1500);
      } else {
        showAlert('error', data.message);
      }
    }

  } catch (error) {
    console.error('Wishlist error:', error);

    if (error.message.includes('logged in')) {
      showAlert('error', 'Please login to use wishlist');
      setTimeout(() => {
        window.location.href = './user/index.php';
      }, 1500);
    } else if (error.message.includes('404') || error.message.includes('file path')) {
      showAlert('error', 'Wishlist service unavailable. Please try again later.');
    } else {
      showAlert('error', 'Failed to update wishlist');
    }

  } finally {
    // Reset button state
    button.disabled = false;
    button.style.opacity = '';
    button.style.cursor = '';
  }
}

// =================================================================
// ===== ALERT SYSTEM - USING YOUR EXISTING CSS ===================
// =================================================================

function showAlert(type, message) {
  // Use your existing alert container
  let alertContainer = document.querySelector(".alert-container");

  // Create alert element that matches your CSS
  const alert = document.createElement("div");
  alert.className = `alert alert-${type}`;

  // Set icon based on type
  let iconName = "info";
  if (type === "success") iconName = "check_circle";
  if (type === "error") iconName = "error";

  alert.innerHTML = `
    <span class="material-symbols-rounded alert-icon">${iconName}</span>
    <span>${message}</span>
    <button class="alert-close">&times;</button>
  `;

  // Add to container
  alertContainer.appendChild(alert);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (alert.parentElement) {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }
  }, 5000);

  // Close button functionality
  alert.querySelector(".alert-close").addEventListener("click", () => {
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 300);
  });
}

// =================================================================
// ===== QUANTITY SETTER ===========================================
// =================================================================

document.addEventListener('click', function (e) {
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
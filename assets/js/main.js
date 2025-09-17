document.addEventListener("DOMContentLoaded", () => {
  // --- Slider Functionality ---
  const slider = document.querySelector(".slider-container");
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  let currentIndex = 0;
  let slideInterval;

  if (slider && slides.length > 0 && dots.length > 0) {
    const showSlide = (index) => {
      // Move the entire slider container
      slider.style.transform = `translateX(-${index * 100}%)`;

      // Update the active dot
      const activeDot = document.querySelector(".dot.active");
      if (activeDot) activeDot.classList.remove("active");
      dots[index].classList.add("active");

      currentIndex = index;
    };

    const nextSlide = () => {
      const newIndex = (currentIndex + 1) % slides.length;
      showSlide(newIndex);
    };

    const startSlideShow = () => {
      slideInterval = setInterval(nextSlide, 3000);
    };

    const stopSlideShow = () => {
      clearInterval(slideInterval);
    };

    dots.forEach((dot) => {
      dot.addEventListener("click", () => {
        stopSlideShow();
        showSlide(parseInt(dot.dataset.slide));
        startSlideShow();
      });
    });

    showSlide(0);
    startSlideShow();
  }

  // --- Search Bar Functionality ---
  const searchBtn = document.querySelector(".search-btn");
  const searchInput = document.querySelector(".search-input");

  if (searchBtn && searchInput) {
    searchBtn.addEventListener("click", (e) => {
      if (!searchInput.classList.contains("active")) {
        e.preventDefault();
        searchInput.classList.add("active");
        searchInput.focus();
      } else if (searchInput.value.trim() === "") {
        e.preventDefault();
        searchInput.focus();
      }
    });
  }

  // --- Mobile Menu Functionality ---
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const mobileMenu = document.querySelector(".mobile-menu");

  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      mobileMenu.classList.toggle("active");
    });
  }

  // --- Close Menu/Search on Outside Click ---
  document.addEventListener("click", (e) => {
    if (
      searchInput &&
      searchInput.classList.contains("active") &&
      !searchInput.contains(e.target) &&
      !searchBtn.contains(e.target)
    ) {
      searchInput.classList.remove("active");
    }

    if (
      mobileMenu &&
      mobileMenu.classList.contains("active") &&
      !mobileMenu.contains(e.target) &&
      !mobileMenuBtn.contains(e.target)
    ) {
      mobileMenu.classList.remove("active");
    }
  });
});

// --- Wishlist Functionality ---
document.addEventListener("click", function (e) {
  const wishlistButton = e.target.closest(".wishlist-btn");
  if (wishlistButton) {
    const productId = wishlistButton.dataset.productId;

    wishlistButton.classList.toggle("active");

    let pathPrefix = window.location.pathname.includes("/products/")
      ? "../"
      : "./";

    fetch(`${pathPrefix}includes/wishlist_handler.php`, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "product_id=" + productId,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "error") {
          wishlistButton.classList.toggle("active");
          if (data.message.includes("logged in")) {
            window.location.href = `${pathPrefix}user/login.php`;
          } else {
            showAlert("error", data.message);
          }
        } else {
          showAlert("success", data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        wishlistButton.classList.toggle("active");
        showAlert("error", "An unexpected error occurred.");
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
  const icon = type === "success" ? "check_circle" : "error";
  alert.className = `alert alert-${type}`;
  alert.innerHTML = `<span class="material-symbols-rounded alert-icon">${icon}</span><span>${message}</span><button class="alert-close">&times;</button>`;

  alertContainer.appendChild(alert);

  setTimeout(() => {
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 300);
  }, 5000);

  alert
    .querySelector(".alert-close")
    .addEventListener("click", () => alert.remove());
}

// --- Product Page Interactivity ---
document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector(".product-page-container")) {
    const productData = {
      variants: typeof variantsData !== "undefined" ? variantsData : [],
      images: typeof imagesData !== "undefined" ? imagesData : [],
    };

    const mainImageContainer = document.querySelector(".main-image-container");
    const thumbnailStrip = document.querySelector(".thumbnail-strip");
    const priceDisplay = document.getElementById("price-display");

    const colorOptions = document.getElementById("color-options");
    const ramOptions = document.getElementById("ram-options");
    const storageOptions = document.getElementById("storage-options");

    let selectedColor = null;
    let selectedRam = null;
    let selectedStorage = null;

    function updateImages() {
      mainImageContainer.innerHTML = "";
      thumbnailStrip.innerHTML = "";
      if (selectedColor && productData.images[selectedColor]) {
        productData.images[selectedColor].forEach((imgUrl, index) => {
          const fullUrl = `../assets/images/products/${imgUrl}`;
          if (index === 0) {
            mainImageContainer.innerHTML = `<img src="${fullUrl}" alt="Main product image">`;
          }
          const thumb = document.createElement("div");
          thumb.className = "thumbnail-item";
          thumb.innerHTML = `<img src="${fullUrl}" alt="Product thumbnail">`;
          thumb.addEventListener("click", () => {
            mainImageContainer.querySelector("img").src = fullUrl;
            document
              .querySelectorAll(".thumbnail-item.active")
              .forEach((t) => t.classList.remove("active"));
            thumb.classList.add("active");
          });
          if (index === 0) thumb.classList.add("active");
          thumbnailStrip.appendChild(thumb);
        });
      }
    }

    function updateRamOptions() {
      /* ... function to update ram options ... */
    }
    function updateStorageOptions() {
      /* ... function to update storage options ... */
    }
    function updatePrice() {
      /* ... function to update price ... */
    }

    colorOptions.addEventListener("click", (e) => {
      if (e.target.classList.contains("color-btn")) {
        // ... logic to handle color selection
      }
    });

    // ... (event listeners for RAM and Storage)

    // Initial setup
    if (colorOptions.querySelector(".color-btn")) {
      colorOptions.querySelector(".color-btn").click();
    }
  }
});

document.addEventListener("DOMContentLoaded", () => {
  // --- Slider Functionality ---
  const slider = document.querySelector(".slider-container");
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  let currentIndex = 0;
  let slideInterval;

  if (slider && slides.length > 0 && dots.length > 0) {
    const showSlide = (index) => {
      const activeSlide = document.querySelector(".slide.active");
      if (activeSlide) activeSlide.classList.remove("active");

      const activeDot = document.querySelector(".dot.active");
      if (activeDot) activeDot.classList.remove("active");

      slides[index].classList.add("active");
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
      e.stopPropagation();
      searchInput.classList.toggle("active");
      if (searchInput.classList.contains("active")) {
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

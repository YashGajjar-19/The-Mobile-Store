// Auto-sliding functionality
document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".dot");
  let currentSlide = 0;
  const slideCount = slides.length;
  const slideInterval = 4000; // 4     seconds

  // Function to show a specific slide
  function showSlide(index) {
    // Hide all slides
    slides.forEach((slide) => {
      slide.style.transform = `translateX(-${index * 100}%)`;
    });

    // Update active dot
    dots.forEach((dot) => dot.classList.remove("active"));
    dots[index].classList.add("active");

    currentSlide = index;
  }

  // Function to move to next slide
  function nextSlide() {
    currentSlide = (currentSlide + 1) % slideCount;
    showSlide(currentSlide);
  }

  // Initialize the slider
  function initSlider() {
    // Position all slides horizontally
    slides.forEach((slide, index) => {
      slide.style.transform = `translateX(${index * 100}%)`;
    });

    // Set up click events for dots
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        clearInterval(autoSlide);
        showSlide(index);
        autoSlide = setInterval(nextSlide, slideInterval);
      });
    });

    // Start auto-sliding
    let autoSlide = setInterval(nextSlide, slideInterval);

    // Pause on hover
    const sliderWrapper = document.querySelector(".slider-wrapper");
    sliderWrapper.addEventListener("mouseenter", () => {
      clearInterval(autoSlide);
    });

    sliderWrapper.addEventListener("mouseleave", () => {
      autoSlide = setInterval(nextSlide, slideInterval);
    });
  }

  initSlider();
});
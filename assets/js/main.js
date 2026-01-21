/**
 * Main JavaScript file
 */
document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const menuToggle = document.getElementById("menu-toggle")
  const mobileMenu = document.getElementById("mobile-menu")
  const header = document.getElementById("header")

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener("click", function () {
      mobileMenu.classList.toggle("active")
      this.querySelector("i").classList.toggle("fa-bars")
      this.querySelector("i").classList.toggle("fa-times")
    })
  }

  // Header scroll effect
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled")
    } else {
      header.classList.remove("scrolled")
    }
  })

  // Initialize sliders if they exist
  initializeSliders()

  // Add animation classes to elements when they come into view
  animateOnScroll()
})

/**
 * Initialize content sliders
 */
function initializeSliders() {
  const sliders = document.querySelectorAll(".slider")

  sliders.forEach((slider) => {
    // Simple slider navigation (can be enhanced with a proper slider library)
    const cards = slider.querySelectorAll(".content-card")
    const cardWidth = cards.length > 0 ? cards[0].offsetWidth + 16 : 0 // Card width + margin

    let scrollPosition = 0

    // Create navigation buttons if there are enough cards
    if (cards.length > 5) {
      const prevButton = document.createElement("button")
      prevButton.className = "slider-nav prev"
      prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>'

      const nextButton = document.createElement("button")
      nextButton.className = "slider-nav next"
      nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>'

      prevButton.addEventListener("click", () => {
        scrollPosition = Math.max(scrollPosition - cardWidth * 2, 0)
        slider.scrollTo({
          left: scrollPosition,
          behavior: "smooth",
        })
      })

      nextButton.addEventListener("click", () => {
        scrollPosition = Math.min(scrollPosition + cardWidth * 2, slider.scrollWidth - slider.clientWidth)
        slider.scrollTo({
          left: scrollPosition,
          behavior: "smooth",
        })
      })

      slider.parentNode.appendChild(prevButton)
      slider.parentNode.appendChild(nextButton)
    }
  })
}

/**
 * Animate elements when they come into view
 */
function animateOnScroll() {
  const elements = document.querySelectorAll(".content-card, .detail-poster, .detail-info, .video-player-container")

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in")
          observer.unobserve(entry.target)
        }
      })
    },
    {
      threshold: 0.1,
    },
  )

  elements.forEach((element) => {
    observer.observe(element)
  })
}


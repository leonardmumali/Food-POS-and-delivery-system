// Main JavaScript for FoodExpress

// Toast notification function
function showToast(title, message, type = "info") {
  const toastContainer =
    document.querySelector(".toast-container") || createToastContainer();

  const toast = document.createElement("div");
  toast.className = `toast show bg-${
    type === "success" ? "success" : type === "error" ? "danger" : "info"
  } text-white`;
  toast.innerHTML = `
        <div class="toast-header bg-${
          type === "success" ? "success" : type === "error" ? "danger" : "info"
        } text-white">
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;

  toastContainer.appendChild(toast);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (toast.parentNode) {
      toast.remove();
    }
  }, 5000);
}

// Create toast container if it doesn't exist
function createToastContainer() {
  const container = document.createElement("div");
  container.className = "toast-container";
  document.body.appendChild(container);
  return container;
}

// Update cart count
function updateCartCount(count) {
  const cartBadge = document.querySelector("#cart-count");
  if (cartBadge) {
    cartBadge.textContent = count;
  }
}

// Format currency
function formatCurrency(amount) {
  return "KSh " + parseFloat(amount).toFixed(2);
}

// Validate phone number (Kenyan format)
function validatePhoneNumber(phone) {
  const phoneRegex = /^(\+254|254|0)?([17]\d{8})$/;
  return phoneRegex.test(phone);
}

// Format phone number to 254 format
function formatPhoneNumber(phone) {
  return phone.replace(/^(\+254|254|0)?/, "254");
}

// Loading spinner
function showLoading(element) {
  const originalContent = element.innerHTML;
  element.innerHTML = '<span class="loading"></span> Loading...';
  element.disabled = true;
  return originalContent;
}

function hideLoading(element, originalContent) {
  element.innerHTML = originalContent;
  element.disabled = false;
}

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

// Add scroll to top button
document.addEventListener("DOMContentLoaded", function () {
  // Create scroll to top button
  const scrollButton = document.createElement("button");
  scrollButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
  scrollButton.className = "btn btn-primary position-fixed";
  scrollButton.style.cssText =
    "bottom: 20px; right: 20px; z-index: 1000; width: 50px; height: 50px; border-radius: 50%; display: none;";
  scrollButton.onclick = scrollToTop;
  document.body.appendChild(scrollButton);

  // Show/hide scroll button based on scroll position
  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      scrollButton.style.display = "block";
    } else {
      scrollButton.style.display = "none";
    }
  });
});

// Form validation
function validateForm(form) {
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );
  let isValid = true;

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("is-invalid");
      isValid = false;
    } else {
      input.classList.remove("is-invalid");
    }
  });

  return isValid;
}

// Add form validation listeners
document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll("form[data-validate]");
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault();
        showToast("Error", "Please fill in all required fields", "error");
      }
    });
  });
});

// Auto-hide alerts after 5 seconds
document.addEventListener("DOMContentLoaded", function () {
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
  alerts.forEach((alert) => {
    setTimeout(() => {
      if (alert.parentNode) {
        alert.remove();
      }
    }, 5000);
  });
});

// Lazy loading for images
document.addEventListener("DOMContentLoaded", function () {
  const images = document.querySelectorAll("img[data-src]");
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove("lazy");
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
});

// Search functionality
function performSearch(query) {
  if (query.length < 2) return;

  // You can implement AJAX search here
  console.log("Searching for:", query);
}

// Debounce function for search
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Add search functionality
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector("#search-input");
  if (searchInput) {
    const debouncedSearch = debounce(performSearch, 300);
    searchInput.addEventListener("input", function () {
      debouncedSearch(this.value);
    });
  }
});

// Mobile menu toggle
document.addEventListener("DOMContentLoaded", function () {
  const navbarToggler = document.querySelector(".navbar-toggler");
  const navbarCollapse = document.querySelector(".navbar-collapse");

  if (navbarToggler && navbarCollapse) {
    // Close mobile menu when clicking on a link
    const navLinks = navbarCollapse.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 992) {
          navbarCollapse.classList.remove("show");
        }
      });
    });
  }
});

// Add to cart animation
function addToCartAnimation(button) {
  const originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-check"></i> Added';
  button.classList.remove("btn-primary");
  button.classList.add("btn-success");

  setTimeout(() => {
    button.innerHTML = originalText;
    button.classList.remove("btn-success");
    button.classList.add("btn-primary");
  }, 2000);
}

// Export functions for use in other scripts
window.FoodExpress = {
  showToast,
  updateCartCount,
  formatCurrency,
  validatePhoneNumber,
  formatPhoneNumber,
  showLoading,
  hideLoading,
  scrollToTop,
  validateForm,
  performSearch,
  addToCartAnimation,
};

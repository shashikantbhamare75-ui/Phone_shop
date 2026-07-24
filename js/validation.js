// js/validation.js
// Client-side validation for contact, register, login, and checkout forms.
// NOTE: server-side validation (in the PHP files) is what actually protects
// the database — this file only improves the user experience.

document.addEventListener("DOMContentLoaded", function () {
  // ---------- Helper ----------
  function showError(input, message) {
    clearError(input);
    const err = document.createElement("span");
    err.className = "error-text";
    err.textContent = message;
    input.insertAdjacentElement("afterend", err);
    input.style.borderColor = "#cc0000";
  }

  function clearError(input) {
    input.style.borderColor = "";
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error-text")) {
      next.remove();
    }
  }

  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  // ---------- Contact form ----------
  const contactForm = document.querySelector("#contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      let valid = true;

      const name = contactForm.querySelector("#contactName");
      const email = contactForm.querySelector("#contactEmail");
      const message = contactForm.querySelector("#contactMessage");

      if (name.value.trim() === "") {
        showError(name, "Please enter your name");
        valid = false;
      } else {
        clearError(name);
      }

      if (!isValidEmail(email.value.trim())) {
        showError(email, "Please enter a valid email");
        valid = false;
      } else {
        clearError(email);
      }

      if (message.value.trim().length < 10) {
        showError(message, "Message should be at least 10 characters");
        valid = false;
      } else {
        clearError(message);
      }

      if (!valid) e.preventDefault();
    });
  }

  // ---------- Register form ----------
  const registerForm = document.querySelector("#registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      let valid = true;

      const name = registerForm.querySelector("#regName");
      const email = registerForm.querySelector("#regEmail");
      const mobile = registerForm.querySelector("#regMobile");
      const password = registerForm.querySelector("#regPassword");
      const confirmPassword = registerForm.querySelector("#regConfirmPassword");

      if (name.value.trim() === "") {
        showError(name, "Please enter your name");
        valid = false;
      } else {
        clearError(name);
      }

      if (!isValidEmail(email.value.trim())) {
        showError(email, "Please enter a valid email");
        valid = false;
      } else {
        clearError(email);
      }

      if (!/^\d{10}$/.test(mobile.value.trim())) {
        showError(mobile, "Mobile number must be exactly 10 digits");
        valid = false;
      } else {
        clearError(mobile);
      }

      if (password.value.length < 6) {
        showError(password, "Password must be at least 6 characters");
        valid = false;
      } else {
        clearError(password);
      }

      if (confirmPassword.value !== password.value) {
        showError(confirmPassword, "Passwords do not match");
        valid = false;
      } else {
        clearError(confirmPassword);
      }

      if (!valid) e.preventDefault();
    });

    // Live digit-only limiting for mobile field, kept from your uploaded contact.js
    const mobileInput = registerForm.querySelector("#regMobile");
    if (mobileInput) {
      mobileInput.addEventListener("input", function () {
        mobileInput.value = mobileInput.value.replace(/\D/g, "").slice(0, 10);
      });
    }
  }

  // ---------- Login form ----------
  const loginForm = document.querySelector("#loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      let valid = true;

      const email = loginForm.querySelector("#loginEmail");
      const password = loginForm.querySelector("#loginPassword");

      if (!isValidEmail(email.value.trim())) {
        showError(email, "Please enter a valid email");
        valid = false;
      } else {
        clearError(email);
      }

      if (password.value.trim() === "") {
        showError(password, "Please enter your password");
        valid = false;
      } else {
        clearError(password);
      }

      if (!valid) e.preventDefault();
    });
  }

  // ---------- Checkout form ----------
  const checkoutForm = document.querySelector("#checkoutForm");
  if (checkoutForm) {
    checkoutForm.addEventListener("submit", function (e) {
      let valid = true;

      const address = checkoutForm.querySelector("#checkoutAddress");
      const city = checkoutForm.querySelector("#checkoutCity");
      const pincode = checkoutForm.querySelector("#checkoutPincode");
      const payment = checkoutForm.querySelectorAll('input[name="payment"]');

      if (address.value.trim() === "") {
        showError(address, "Please enter your address");
        valid = false;
      } else {
        clearError(address);
      }

      if (city.value.trim() === "") {
        showError(city, "Please enter your city");
        valid = false;
      } else {
        clearError(city);
      }

      if (!/^\d{5,6}$/.test(pincode.value.trim())) {
        showError(pincode, "Please enter a valid pincode");
        valid = false;
      } else {
        clearError(pincode);
      }

      const paymentSelected = Array.from(payment).some((r) => r.checked);
      if (!paymentSelected) {
        alert("Please select a payment method");
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  }
});
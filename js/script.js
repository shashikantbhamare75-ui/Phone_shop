// js/script.js
// General UI behavior shared by every page: mobile menu toggle + dark mode toggle

document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu toggle
  const menuToggle = document.querySelector("#menuToggle");
  const navMenu = document.querySelector("#navMenu");

  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", function () {
      navMenu.classList.toggle("active");
    });
  }

  // Dark mode toggle (button must have id="dark" on pages that include one)
  const darkBtn = document.querySelector("#dark");

  if (darkBtn) {
    // Restore saved preference on page load
    if (localStorage.getItem("darkMode") === "on") {
      document.body.classList.add("dark");
    }

    darkBtn.addEventListener("click", function () {
      document.body.classList.toggle("dark");
      localStorage.setItem(
        "darkMode",
        document.body.classList.contains("dark") ? "on" : "off"
      );
    });
  }

  // Product card hover tracking (kept from your uploaded script.js,
  // useful later for "recently viewed" features)
  const cards = document.querySelectorAll(".card");
  cards.forEach(function (card) {
    card.addEventListener("mouseenter", function () {
      console.log("Product viewed");
    });
  });
});


document.querySelectorAll(".toggle-review").forEach(function(button){

    button.addEventListener("click", function(){

        let parent = this.parentElement;

        let shortReview = parent.querySelector(".short-review");
        let fullReview = parent.querySelector(".full-review");

        if(fullReview.style.display === "none"){

            shortReview.style.display = "none";
            fullReview.style.display = "inline";

            this.textContent = "Less";

        }else{

            shortReview.style.display = "inline";
            fullReview.style.display = "none";

            this.textContent = "More";

        }

    });

});


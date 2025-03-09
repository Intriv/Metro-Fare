// Function to handle search
function searchFunction(event) {
  event.preventDefault(); // Prevent form submission
  const searchInput = document.getElementById("search-input").value.trim().toLowerCase();
  const resultsContainer = document.querySelector(".main__content");
  const resultsList = document.querySelector(".excel__container");

  resultsList.innerHTML = "";

  if (searchInput === "") {
      return;
  }

  resultsContainer.style.display = "flex";

  // Fetch data from PHP using AJAX
  fetch(`location_fare.php?search_query=${encodeURIComponent(searchInput)}`)
      .then(response => response.text())
      .then(data => {
          resultsList.innerHTML = data;
          
          setTimeout(() => {
              resultsContainer.scrollIntoView({ behavior: "smooth" });
          }, 100);
      })
      .catch(error => {
          console.error("Error fetching data:", error);
          resultsList.innerHTML = '<div class="no-results">Error fetching data. Please try again.</div>';
      });
}

// Attach event listener to form
document.getElementById("search-form").addEventListener("submit", searchFunction);

// Mobile menu functionality
const menu = document.querySelector("#mobile-menu");
const menuLinks = document.querySelector(".navbar__menu");

menu.addEventListener("click", function () {
  menu.classList.toggle("is-active");
  menuLinks.classList.toggle("active");
});
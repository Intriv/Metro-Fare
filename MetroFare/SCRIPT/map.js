const menu = document.querySelector("#mobile-menu");
const menuLinks = document.querySelector(".navbar__menu");

menu.addEventListener("click", function () {
  menu.classList.toggle("is-active");
  menuLinks.classList.toggle("active");
});

// Initialize the map - centered on Imus, Philippines
const map = L.map("map").setView([14.4318, 120.9367], 13);

// Add the OpenStreetMap tiles
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution:
    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(map);

// Add a marker for Imus initially
let currentMarker = L.marker([14.4318, 120.9367])
  .addTo(map)
  .bindPopup("Imus, Philippines")
  .openPopup();

// Function to update the map to a new location
function updateMap(lat, lng, title) {
  map.setView([lat, lng], 13);

  // Remove the current marker
  if (currentMarker) {
    map.removeLayer(currentMarker);
  }

  // Add a new marker
  currentMarker = L.marker([lat, lng]).addTo(map).bindPopup(title).openPopup();
}

// Handle location card clicks
document.querySelectorAll(".location-card").forEach((card) => {
  card.addEventListener("click", () => {
    const lat = parseFloat(card.getAttribute("data-lat"));
    const lng = parseFloat(card.getAttribute("data-lng"));
    const title = card.querySelector("h3").textContent;

    updateMap(lat, lng, title);
  });
});

// Handle search
document.getElementById("search-button").addEventListener("click", () => {
  const searchQuery = document.getElementById("search-input").value.trim();

  if (searchQuery) {
    // In a real application, you'd use a geocoding API here
    // For demo purposes, we'll just show a location near Imus for any search

    // Generate a "random" location near Imus for demonstration
    const lat = 14.4318 + (Math.random() - 0.5) * 0.05;
    const lng = 120.9367 + (Math.random() - 0.5) * 0.05;

    updateMap(lat, lng, `Results for: ${searchQuery}`);
  }
});

// Add enter key support for search
document
  .getElementById("search-input")
  .addEventListener("keypress", (event) => {
    if (event.key === "Enter") {
      document.getElementById("search-button").click();
    }
  });

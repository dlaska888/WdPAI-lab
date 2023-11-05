// SPA navigation
function showPage(pageId) {
	// Hide all sections
	const sections = document.querySelectorAll("main section");
	sections.forEach((section) => {
		section.classList.add("hidden");
	});

	// Show the selected section
	const selectedSection = document.getElementById(pageId);
	selectedSection.classList.remove("hidden");
}

// SPA navigation buttons
const homeBtns = document.querySelectorAll(".btn-home");
const sharedBtns = document.querySelectorAll(".btn-shared");
const accBtns = document.querySelectorAll(".btn-account");
const settBtns = document.querySelectorAll(".btn-settings");
const logoutBtns = document.querySelectorAll(".btn-logout");

homeBtns.forEach((btn) =>
	btn.addEventListener("click", () => showPage("home"))
);

sharedBtns.forEach((btn) => {
	btn.addEventListener("click", () => showPage("shared"));
});

accBtns.forEach((btn) =>
	btn.addEventListener("click", () => showPage("account"))
);
settBtns.forEach((btn) =>
	btn.addEventListener("click", () => showPage("settings"))
);
logoutBtns.forEach((btn) => btn.addEventListener("click", () => logout()));

// Session handling
function logout() {
	window.location.href = "index.html";
}

// Navbar mobile
const navButtons = document.querySelectorAll(".btn-mobile");
const menuBtn = document.querySelector("#btn-mobile-menu");
const nav = document.querySelector("#nav-mobile");

navButtons.forEach((btn) =>
	btn.addEventListener("click", () => {
		menuBtn.classList.toggle("open");
		nav.classList.toggle("expand");
	})
);

const searchBtn = document.querySelector(".search-container");

searchBtn.addEventListener("click", () => {
	menuBtn.classList.remove("open");
	nav.classList.remove("expand");
});

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

function activatePageButtons(pageClass) {
	spaBtns.forEach((btn) => {
		if (btn.classList.contains(pageClass)) {
			btn.classList.add("active");
		}
	});
}

function findPageClass(element) {
	return Array.from(element.classList).find((className) =>
		className.startsWith("page-")
	);
}

// SPA navigation buttons

const spaBtns = document.querySelectorAll(".btn-page");

spaBtns.forEach((btn) => {
	const pageClass = findPageClass(btn);
	btn.addEventListener("click", () => showPage(pageClass));
});

spaBtns.forEach((spaBtn) => {
	spaBtn.addEventListener("click", () => {
		spaBtns.forEach((btn) => {
			btn.classList.remove("active");
		});

		//Link same SPA buttons on desktop and mobile
		const pageClass = findPageClass(spaBtn);
		activatePageButtons(pageClass);
	});
});

// Session handling
const logoutBtns = document.querySelectorAll(".btn-logout");
logoutBtns.forEach((btn) => btn.addEventListener("click", () => logout()));
function logout() {
	window.location.href = "login.html";
}

// Navbar mobile toggle
const navButtons = document.querySelectorAll(".btn-nav");
const menuBtn = document.querySelector("#btn-mobile-menu");
const nav = document.querySelector("#nav-mobile");

navButtons.forEach((btn) =>
	btn.addEventListener("click", () => {
		menuBtn.classList.toggle("open");
		nav.classList.toggle("expand");
	})
);

//Navbar mobile collapse
const searchBtn = document.querySelector(".search-container");

searchBtn.addEventListener("click", () => {
	menuBtn.classList.remove("open");
	nav.classList.remove("expand");
});

//Footer mobile

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

// Navbar mobile toggle on click
const navButtons = document.querySelectorAll(".btn-nav");
const menuBtn = document.querySelector("#btn-mobile-menu");
const nav = document.querySelector("#nav-mobile");

navButtons.forEach((btn) =>
	btn.addEventListener("click", () => {
		menuBtn.classList.toggle("open");
		nav.classList.toggle("expand");
	})
);

//Navbar mobile collapse on click
const collapseButtons = document.querySelectorAll(".btn-nav-collapse");

collapseButtons.forEach((btn) =>
	btn.addEventListener("click", () => {
		menuBtn.classList.remove("open");
		nav.classList.remove("expand");
	})
);

//Navbar and footer mobile collapse on scroll
let lastScrollTop = 0;
const scrollHideEls = document.querySelectorAll(".hide-on-scroll");

window.addEventListener("scroll", function () {
	let currentScroll =
		window.pageYOffset || document.documentElement.scrollTop;

	scrollHideEls.forEach((el) => {
		if (currentScroll > lastScrollTop) {
			// Scrolling down
			el.classList.add("scroll-hidden");
		} else {
			// Scrolling up
			el.classList.remove("scroll-hidden");
		}
	});

	lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});

// Link group collapsing
const btnLinks = document.querySelectorAll(".btn-links");

btnLinks.forEach((btn) => {
	btn.addEventListener("click", () => {
		const links = btn.closest(".group").querySelector(".links");
		links.classList.toggle("collapse");
		btn.classList.toggle("active");
	});
});

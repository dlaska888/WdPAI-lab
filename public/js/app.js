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

// Navbar mobile
const navBtn = document.querySelector("#menu-btn");
const searchBtn = document.querySelector(".search-container");
const navMobile = document.querySelector("#nav-mobile");

navBtn.addEventListener("click", () => {
	navBtn.classList.toggle("open");
	navMobile.classList.toggle("expand");
});

searchBtn.addEventListener("click", () =>{
	navBtn.classList.remove("open");
	navMobile.classList.remove("expand");
})

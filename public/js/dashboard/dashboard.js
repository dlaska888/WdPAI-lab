import {addGroupButtons, fetchAllGroups} from "./home.js";

// --- SPA navigation ---

function showPage(pageId) {
    // hide all sections
    const sections = document.querySelectorAll("main section");
    sections.forEach((section) => {
        section.classList.add("hidden");
    });

    // show the selected section
    const selectedSection = document.getElementById(pageId);
    selectedSection.classList.remove("hidden");
}

function activatePageBtn(pageClass) {
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

// display page on click
const spaBtns = document.querySelectorAll(".btn-page");

spaBtns.forEach((btn) => {
    const pageClass = findPageClass(btn);
    btn.addEventListener("click", () => showPage(pageClass));
});

// highlight button while displaying page
spaBtns.forEach((spaBtn) => {
    spaBtn.addEventListener("click", () => {
        spaBtns.forEach((btn) => {
            btn.classList.remove("active");
        });

        // link same SPA buttons on desktop and mobile
        const pageClass = findPageClass(spaBtn);
        activatePageBtn(pageClass);
    });
});

// --- Navbar mobile ---

// toggle on click
const navButtons = document.querySelectorAll(".btn-nav");
const menuBtn = document.querySelector("#btn-mobile-menu");
const nav = document.querySelector("#nav-mobile");

navButtons.forEach((btn) =>
    btn.addEventListener("click", () => {
        menuBtn.classList.toggle("open");
        nav.classList.toggle("expand");
    })
);

// collapse on click
const collapseButtons = document.querySelectorAll(".btn-nav-collapse");

collapseButtons.forEach((btn) =>
    btn.addEventListener("click", () => {
        menuBtn.classList.remove("open");
        nav.classList.remove("expand");
    })
);

// navbar and footer collapse on scroll
let lastScrollTop = 0;
const scrollHideEls = document.querySelectorAll(".hide-on-scroll");

window.addEventListener("scroll", () => {
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


// collapse on click


// --- Fetching Data ---
addGroupButtons();
fetchAllGroups();



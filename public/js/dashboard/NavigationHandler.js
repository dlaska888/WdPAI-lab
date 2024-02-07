import LinksPage from "./modules/pages/LinksPage.js";
import SettingsPage from "./modules/pages/SettingsPage.js";

const NavigationHandler = (function () {
    let loading = false;

    const pages = {
        "page-home": () => LinksPage.render("page-home", "link-groups"),
        "page-shared": () => LinksPage.render("page-shared", "link-groups/shared", true),
        "page-settings": () => SettingsPage.render("page-settings"),
    };

    async function initNavigation() {
        document.querySelectorAll(".btn-page").forEach(btn => {
            btn.addEventListener("click", async () => {
                await navigateToPage(findPageClass(btn));
            });
        });

        // Initial page load
        await navigateToPage("page-home");
    }

    async function navigateToPage(pageId) {
        if (loading) return;

        loading = true;
        toggleSpinner(true);

        clearPage();
        await renderPage(pageId);
        activatePageBtn(pageId);

        toggleSpinner(false);
        loading = false;
    }

    async function renderPage(pageId) {
        const page = document.querySelector(".page");
        const pageRenderer = pages[pageId];

        if (pageRenderer) {
            const content = await pageRenderer();
            page.replaceWith(content);
        } else {
            console.error(`Renderer not found for page: ${pageId}`);
        }
    }

    function clearPage() {
        document.querySelector(".page").innerHTML = '';
    }

    function toggleSpinner(show) {
        const spinner = document.querySelector("#page-spinner");
        if (show) {
            spinner.classList.remove("hidden");
        } else {
            spinner.classList.add("hidden");
        }
    }

    function activatePageBtn(pageClass) {
        const spaBtns = document.querySelectorAll(".btn-page");

        spaBtns.forEach((btn) => {
            if (btn.classList.contains(pageClass)) {
                btn.classList.add("active");
            } else {
                btn.classList.remove("active");
            }
        });
    }

    function findPageClass(element) {
        return Array.from(element.classList).find((className) =>
            className.startsWith("page-")
        );
    }

    return {
        initNavigation: initNavigation,
    };
}());

export default NavigationHandler;

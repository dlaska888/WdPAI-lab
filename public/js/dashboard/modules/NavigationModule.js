import LinksPage from "./pages/LinksPage.js";
import SettingsPage from "./pages/SettingsPage.js";
import NotificationService from "../NotificationService.js";

const NavigationModule = (function () {

    const pages = {
        "page-home": {module: LinksPage, args: ["link-groups", true]},
        "page-shared": {module: LinksPage, args: ["link-groups/shared"]},
        "page-settings": {module: SettingsPage, args: []},
    };

    async function initNavigation() {
        initButtonsHighlight();

        document.querySelectorAll(".btn-page").forEach(btn => {
            btn.addEventListener("click", async () => await navigateToPage(findPageClass(btn)));
        });

        // Initial page load
        await navigateToPage("page-home");
    }

    async function navigateToPage(pageId) {
        toggleSpinner(true);
        clearPage();
        await renderPage(pageId);
        toggleSpinner(false);
    }

    async function renderPage(pageId) {
        const page = document.querySelector(".page");
        const module = pages[pageId].module;
        const args = pages[pageId].args;
        
        const content = await module.render(pageId, ...args);
        
        page.replaceWith(content);
        NotificationService.notify("Page loaded!");
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

    function initButtonsHighlight() {
        const spaBtns = document.querySelectorAll(".btn-page");

        spaBtns.forEach((spaBtn) => {
            spaBtn.addEventListener("click", () => {
                spaBtns.forEach((btn) => {
                    btn.classList.remove("active");
                });

                const pageClass = findPageClass(spaBtn);
                activatePageBtn(pageClass, spaBtns);
            });
        });
    }

    function activatePageBtn(pageClass, spaBtns) {
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

    return {
        initNavigation: initNavigation,
    };
}());

export default NavigationModule;

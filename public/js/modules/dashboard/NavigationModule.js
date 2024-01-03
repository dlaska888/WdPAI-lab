import LinkPageModule from "./pages/LinkPageModule.js";
import SettingsPageModule from "./pages/SettingsPageModule.js";

const NavigationModule = (function () {
    async function initNavigation() {
        initButtonsHighlight();

        document.querySelectorAll(".page-home").forEach(btn => {
            btn.addEventListener("click", async () => 
                await renderPage(await LinkPageModule.render("page-home", "link-groups", true)));
        });
        
        document.querySelectorAll(".page-shared").forEach(btn => {
            btn.addEventListener("click", async () =>
                await renderPage(await LinkPageModule.render("page-shared", "link-groups/shared")));
        });
        
        document.querySelectorAll(".page-settings").forEach(btn => {
            btn.addEventListener("click", async () =>
                await renderPage(await SettingsPageModule.render()));
        });

        // Initial page load
        await renderPage(await LinkPageModule.render("page-home", "link-groups", true));
    }
    
    async function renderPage(content){
        const page = document.createElement("main");
        page.appendChild(content);
        document.querySelector("main").replaceWith(page);
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
        initNavigation: initNavigation
    };
}());

export default NavigationModule;

import LinkPageModule from "./pages/LinkPageModule.js";
import SettingsPageModule from "./pages/SettingsPageModule.js";

// TODO fix loading multiple pages at once (cancel async tasks?)

const NavigationModule = (function () {
    async function initNavigation() {
        initButtonsHighlight();

        document.querySelectorAll(".page-home").forEach(btn => {
            btn.addEventListener("click", async () => 
                await renderPage(LinkPageModule.render("page-home", "link-groups", true)));
        });
        
        document.querySelectorAll(".page-shared").forEach(btn => {
            btn.addEventListener("click", async () =>
                await renderPage(LinkPageModule.render("page-shared", "link-groups/shared")));
        });
        
        document.querySelectorAll(".page-settings").forEach(btn => {
            btn.addEventListener("click", async () =>
                await renderPage(SettingsPageModule.render()));
        });

        // Initial page load
        await renderPage(LinkPageModule.render("page-home", "link-groups", true));
    }
    
    async function renderPage(contentPromise){
        toggleSpinner();
        const page = document.querySelector("#page-container");
        while (page.lastElementChild) {
            page.removeChild(page.lastElementChild);
        }
        page.appendChild(await contentPromise);
        toggleSpinner();
    }
    
    function toggleSpinner(){
        document.querySelector("#page-spinner").classList.toggle("hidden");
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

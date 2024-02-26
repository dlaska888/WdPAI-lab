import ButtonModule from "../ButtonModule.js";
import GroupModule from "../group/GroupModule.js";
import AddGroupForm from "../forms/AddGroupForm.js";
import ModalModule from "../ModalModule.js";
import GroupSearchModule from "../group/GroupSearchModule.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";

const LinksPage = (function () {
    async function render(pageId, groupsEndpoint, shared = false) {
        const groups = (await ApiClient.fetchData(groupsEndpoint)).data;

        let page = document.createElement("div");
        page.innerHTML = `
            <section id="${pageId}" class="page page-links flex-column">
                <div class="groups-container">
                    ${!groups.length && shared === false ? `<p class="link-placeholder text-center">Add groups by clicking on add button</p>` : ''}
                </div>
            </section>`;
        page = page.firstElementChild;

        page.prepend(await GroupSearchModule.render(pageId, groupsEndpoint, "hide-mobile"));

        if (!shared) {
            page.querySelector(".search-container")
                .appendChild(await ButtonModule.render("add", addGroupForm, "btn-menu"));
        }

        if (!Array.isArray(groups)) {
            NotificationService.notify("Could not load groups", "error");
            console.error("Invalid groups type for render", groups);
            return page;
        }

        const groupsContainer = page.querySelector('.groups-container');
        for (const group of groups) {
            groupsContainer.appendChild(await GroupModule.render(group, shared));
        }
        
        if (!groups.length){
            groupsContainer.style.margin = 'auto';
        }

        await renderButtonsMobileNav(pageId, groupsEndpoint, shared);

        return page;
    }

    async function addGroup(group, pageId) {
        const groupsContainer = document.querySelector(`[id="${pageId}"]`)
            .querySelector('.groups-container');
        groupsContainer.appendChild(await GroupModule.render(group));
        
        groupsContainer.querySelector(".link-placeholder").remove();
        groupsContainer.style.margin = '';
    }

    async function addGroupForm() {
        document.body.appendChild(await ModalModule.render(await AddGroupForm.render()));
    }

    async function renderButtonsMobileNav(pageId, groupsEndpoint, shared) {
        const navMobile = document.querySelector("#nav-mobile");
        const groupsButtons = document.createElement("div");

        groupsButtons.classList = "groups-buttons flex flex-center";
        groupsButtons.appendChild(await GroupSearchModule.render(pageId, groupsEndpoint, "btn-nav-collapse"));

        if (!shared) {
            groupsButtons.appendChild(await ButtonModule.render("add", addGroupForm, "btn-nav-collapse"));
        }

        groupsButtons.addEventListener("click", () => {
            navMobile.classList.remove("expand");
            navMobile.querySelector("#btn-mobile-menu").classList.remove("open");
        })

        navMobile.querySelector(".groups-buttons").replaceWith(groupsButtons);
    }

    return {
        render: render,
        addGroup: addGroup
    };
}());

export default LinksPage;

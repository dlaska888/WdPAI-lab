import ButtonModule from "../ButtonModule.js";
import GroupModule from "../group/GroupModule.js";
import AddGroupForm from "../forms/AddGroupForm.js";
import ModalModule from "../ModalModule.js";
import GroupSearchModule from "../group/GroupSearchModule.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";

const LinksPage = (function () {
    async function render(pageId, groupsEndpoint, shared = false) {
        let page = document.createElement("div");
        page.innerHTML = `
            <section id="${pageId}" class="page page-links flex-column">
                <div class="groups-container"></div>
            </section>`;
        page = page.firstElementChild;

        page.prepend(await GroupSearchModule.render(pageId, groupsEndpoint));

        if (!shared) {
            page.querySelector(".search-container")
                .appendChild(await ButtonModule.render("add", addGroupForm, "btn-menu"));
        }

        const groupsContainer = page.querySelector('.groups-container');
        const groups = (await ApiClient.fetchData(groupsEndpoint)).data;

        if (!Array.isArray(groups)) {
            NotificationService.notify("Could not load groups", "error");
            console.error("Invalid groups type for render", groups);
            return page;
        }

        for (const group of groups) {
            groupsContainer.appendChild(await GroupModule.render(group, shared));
        }

        return page;
    }

    async function addGroup(group, pageId) {
        const groupsContainer = document.querySelector(`[id="${pageId}"]`)
            .querySelector('.groups-container');
        groupsContainer.appendChild(await GroupModule.render(group));
    }

    async function addGroupForm() {
        document.body.appendChild(await ModalModule.render(await AddGroupForm.render()));
    }

    return {
        render: render,
        addGroup: addGroup
    };
}());

export default LinksPage;

import LinkModule from "./LinkModule.js";
import ButtonModule from "./ButtonModule.js";
import AddLinkForm from "./forms/AddLinkForm.js";
import ModalModule from "./ModalModule.js";
import DeleteGroupForm from "./forms/DeleteGroupForm.js";
import EditGroupForm from "./forms/EditGroupForm.js";
import ShareGroupForm from "./forms/ShareGroupForm.js";
import ApiClient from "../ApiClient.js";

const GroupModule = (function () {
    async function render(group) {
        if (!validateGroup(group)) {
            console.error("Invalid group for render")
            return "";
        }

        const {id, name, editable, links} = group;

        let groupElement = document.createElement("div");
        groupElement.innerHTML = `
            <div id=${id} class="group flex-column">
                <div class="group-menu flex"> 
                    <div class="group-name flex flex-center">
                        <p class="text-tertiary text-ellipsis">${name}</p>
                    </div>
                    <div class="group-buttons flex flex-center">
                    </div>
                </div>
                <div class="group-links flex-column">
                </div>
            </div>`
        groupElement = groupElement.firstElementChild;

        groupElement.querySelector(".group-name")
            .prepend(await ButtonModule.render("collapse", collapseGroup, "btn-group-collapse"));

        if (editable) {
            const groupButtons = groupElement.querySelector(".group-buttons");
            groupButtons.appendChild(await ButtonModule.render("add", () => addLinkForm(group)));
            groupButtons.appendChild(await ButtonModule.render("edit", () => editGroupForm(group)));
            groupButtons.appendChild(await ButtonModule.render("share", () => shareGroupForm(group)));
            groupButtons.appendChild(await ButtonModule.render("delete", () => deleteGroupForm(group)));
        }

        for (const link of links) {
            groupElement.querySelector(".group-links").appendChild(await LinkModule.render(link, editable));
        }

        return groupElement;
    }

    function validateGroup(group) {
        if (!group || typeof group !== 'object') {
            console.error('Invalid group data provided for rendering.');
            return false;
        }

        const {id, name, editable, links} = group;

        if (!id || !name || editable === undefined || !Array.isArray(links)) {
            console.error('Missing required fields in group data for rendering.');
            return false;
        }

        return true;
    }

    function updateState(groupId) {
        ApiClient.fetchData(`http://localhost:8080/link-group/${groupId}`)
            .then(async response => {
                if (response.success) {
                    const groupElement = document.querySelector(`[id="${groupId}"]`);
                    if (groupElement) {
                        groupElement.replaceWith(await GroupModule.render(response.data));
                    } else {
                        console.error(`Group with id ${groupId} not found`);
                    }
                }
            });
    }

    function removeElement(groupId) {
        const groupElement = document.querySelector(`[id="${groupId}"]`); // escaping forbidden id characters
        if (groupElement) {
            groupElement.remove();
        } else {
            console.error(`Group with id ${groupId} to delete not found`);
        }
    }

    function collapseGroup(e) {
        const btn = e.currentTarget;
        const links = btn.closest(".group").querySelector(".group-links");
        links.classList.toggle("collapse");
        btn.classList.toggle("active");
    }

    async function addLinkForm(group) {
        document.body.appendChild(await ModalModule.render(await AddLinkForm.render(group)));
    }

    async function shareGroupForm(group) {
        document.body.appendChild(await ModalModule.render(await ShareGroupForm.render(group)));
    }

    async function editGroupForm(group) {
        document.body.appendChild(await ModalModule.render(await EditGroupForm.render(group)));
    }

    async function deleteGroupForm(group) {
        document.body.appendChild(await ModalModule.render(await DeleteGroupForm.render(group)));
    }

    return {
        render: render,
        updateState: updateState,
        removeElement: removeElement
    }
})();

export default GroupModule;

import LinkModule from "./LinkModule.js";
import ButtonModule from "../ButtonModule.js";
import AddLinkForm from "../forms/AddLinkForm.js";
import ModalModule from "../ModalModule.js";
import DeleteGroupForm from "../forms/DeleteGroupForm.js";
import EditGroupForm from "../forms/EditGroupForm.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";
import IconModule from "../IconModule.js";
import KebabMenuModule from "../KebabMenuModule.js";
import GroupSharesModule from "./GroupSharesModule.js";

const GroupModule = (function () {
    async function render(group) {
        if (!validateGroup(group)) {
            console.error("Invalid group for render")
            return "";
        }

        const {id, name, shared, editable, links, userId} = group;

        let groupElement = document.createElement("div");
        groupElement.innerHTML = `
            <div id=${id} class="group flex-column">
                <div class="group-menu flex"> 
                    <div class="group-name flex flex-center">
                        <p class="text-tertiary text-ellipsis">${name}</p>
                        <p class="dot-separator text-tertiary"></p>
                        <p class="owner-name text-tertiary text-ellipsis"></p>
                        <div class="img-container flex flex-center"></div>
                    </div>
                </div>
                <div class="group-links flex-column">
                </div>
            </div>`
        groupElement = groupElement.firstElementChild;

        groupElement.querySelector(".group-name")
            .prepend(await ButtonModule.render("collapse", collapseGroup, "btn-group-collapse"));

        for (const link of links) {
            groupElement.querySelector(".group-links").appendChild(await LinkModule.render(link, editable));
        }

        // Group buttons

        const groupOptions = [];

        if (editable) {
            groupOptions.push({optionTitle: "Add", optionIcon: "add", callback: () => addLinkForm(group)});
            groupOptions.push({optionTitle: "Edit", optionIcon: "edit", callback: () => editGroupForm(group)});
        }

        groupOptions.push({optionTitle: "Share", optionIcon: "share", callback: () => groupShares(group)});

        if (shared) {
            const ownerData = await fetchUserDataById(userId);
            groupElement.querySelector(".owner-name").textContent = ownerData.userName;
            groupElement.querySelector(".dot-separator").textContent = "•";

            const pictureContainer = groupElement.querySelector(".img-container");
            const ownerImg = document.createElement("img");

            ownerImg.src = `/account/public/${userId}/profile-picture`;
            ownerImg.height = 30;
            ownerImg.width = 30;

            ownerImg.onerror = async () => {
                pictureContainer.innerHTML = await IconModule.render("account");
            }

            pictureContainer.appendChild(ownerImg);

        } else {
            groupOptions.push({optionTitle: "Delete", optionIcon: "delete", callback: () => deleteGroupForm(group)});
        }

        groupElement.querySelector(".group-menu").appendChild(await KebabMenuModule.render(groupOptions));

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
        ApiClient.fetchData(`/link-group/${groupId}`)
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

    function fetchUserDataById(userId) {
        return ApiClient.fetchData(`/account/public/${userId}`)
            .then(result => {
                if (result.success) return result.data;
                NotificationService.notify(result.message || "Could not get user data", "error")
            })
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

    async function groupShares(group) {
        document.body.appendChild(await ModalModule.render(await GroupSharesModule.render(group), false));
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

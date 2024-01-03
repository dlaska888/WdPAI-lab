import LinkModule from "./LinkModule.js";
import ButtonModule from "./ButtonModule.js";
import AddLinkForm from "./forms/AddLinkForm.js";
import ModalModule from "./ModalModule.js";
import DeleteGroupForm from "./forms/DeleteGroupForm.js";
import EditGroupForm from "./forms/EditGroupForm.js";

const GroupModule = (function () {
    async function render(group) {
        let groupElement = document.createElement("div");
        groupElement.innerHTML = `
            <div id=${group.link_group_id} class="group flex-column">
                <div class="group-menu flex"> 
                    <div class="group-name flex flex-center">
                        <p class="text-tertiary text-ellipsis">${group.name}</p>
                    </div>
                    <div class="group-buttons flex flex-center">
                    </div>
                </div>
                <div class="group-links flex-column">
                </div>
            </div>`
        groupElement = groupElement.firstElementChild;

        groupElement.querySelector(".group-name").prepend(await ButtonModule.render("collapse", collapseGroup, "btn-group-collapse"));

        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("add", () => addLinkForm(group)));
        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("edit", () => editGroupForm(group)));
        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("share", null));
        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("delete", () => deleteGroupForm(group)));

        for (const link of group.links) {
            groupElement.querySelector(".group-links").appendChild(await LinkModule.render(link))
        }

        return groupElement;
    }

    function updateState(groupId) {
        fetch(`http://localhost:8080/link-group/${groupId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch group with id ${groupId}: ${response.status}`);
                }
                return response.json();
            })
            .then(async updatedGroup => {
                const groupElement = document.querySelector(`[id="${groupId}" ]`);
                if (groupElement) {
                    groupElement.replaceWith(await GroupModule.render(updatedGroup));
                } else {
                    throw new Error(`Group with id ${groupId} to update not found on the website`);
                }
            })
            .catch(error => {
                console.error(`Error updating group with id ${groupId}: ${error.message}`);
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

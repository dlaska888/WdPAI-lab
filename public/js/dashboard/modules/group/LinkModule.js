import ButtonModule from "../ButtonModule.js";
import ModalModule from "../ModalModule.js";
import EditLinkForm from "../forms/EditLinkForm.js";
import DeleteLinkForm from "../forms/DeleteLinkForm.js";
import ApiClient from "../../ApiClient.js";

const LinkModule = (function () {
    async function render(link, editable = true) {
        if (!validateLink(link)) {
            console.error("Invalid link for render")
            return "";
        }

        const {id, url, title} = link;

        let linkElement = document.createElement("div");
        linkElement.innerHTML = `
        <div id="${id}" class="link-container flex">
            <div class="link-info flex flex-center">
                <img src="https://www.google.com/s2/favicons?domain=${url}&sz=64" width="32" height="32" alt="Icon">
                <div class="link-text flex-column">
                    <p class="link-title text-ellipsis">${title}</p>
                    <p class="link-url text-ellipsis">${url}</p>
                </div>
            </div>
            <div class="link-buttons flex"></div>
        </div>`;
        linkElement = linkElement.firstElementChild;

        const linkButtons = linkElement.querySelector(".link-buttons");
        linkButtons.appendChild(await ButtonModule.render("open-link", () => window.open(url), "btn-link"));

        if (editable) {
            linkElement.querySelector(".link-info")
                .prepend(await ButtonModule.render('drag', null, "btn-drag hidden"));
            linkButtons.appendChild(await ButtonModule.render('edit',
                () => editLinkForm(link), "btn-edit hidden"));
            linkButtons.appendChild(await ButtonModule.render('delete',
                () => deleteLinkForm(link), "btn-delete hidden"));
        }

        return linkElement;
    }

    function validateLink(link) {
        if (!link || typeof link !== 'object') {
            console.error('Invalid link data provided for rendering.');
            return false;
        }

        const {id, url, title} = link;

        if (!id || !url || !title) {
            console.error('Missing required fields in link data for rendering.');
            return false;
        }

        return true;
    }

    function updateState(linkId, groupId) {
        ApiClient.fetchData(`/link-group/${groupId}/link/${linkId}`)
            .then(async response => {
                if (response.success) {
                    const linkElement = document.querySelector(`[id="${linkId}"]`);
                    if (linkElement) {
                        linkElement.replaceWith(await LinkModule.render(response.data));
                        startLinkEdit(linkElement);
                    } else {
                        console.error(`Link with id ${linkId} to update not found`);
                    }
                }
            })
            .catch(error => {
                console.error(`Error updating link with id ${linkId}: ${error.message}`);
            });
    }

    async function removeElement(linkId) {
        const linkElement = document.querySelector(`[id="${linkId}" ]`); // escaping forbidden id characters
        if (linkElement) {
            linkElement.remove();
        } else {
            console.error(`Link with id ${id} to remove not found`);
        }
    }

    function startLinkEdit(link) {
        const linkElement = document.querySelector(`[id="${link.id}"]`);

        linkElement.classList.add("link-edit", "draggable");
        linkElement.draggable = true;
        linkElement.addEventListener("dragstart", () => linkElement.classList.add('dragging'));
        linkElement.addEventListener("dragend", () => linkElement.classList.remove('dragging'));
    }

    function endLinkEdit(link) {
        const linkElement = document.querySelector(`[id="${link.id}"]`);

        linkElement.classList.remove("link-edit", "draggable");
        linkElement.draggable = false;
        linkElement.removeEventListener("dragstart", () => linkElement.classList.add('dragging'));
        linkElement.removeEventListener("dragend", () => linkElement.classList.remove('dragging'));
    }

    async function editLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await EditLinkForm.render(link)));
    }

    async function deleteLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await DeleteLinkForm.render(link)));
    }

    return {
        render: render,
        updateState: updateState,
        removeElement: removeElement,
        startLinkEdit: startLinkEdit,
        endLinkEdit: endLinkEdit,
    };
})();

export default LinkModule;

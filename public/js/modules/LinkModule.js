import ButtonModule from "./ButtonModule.js";
import ModalModule from "./ModalModule.js";
import EditLinkForm from "./forms/EditLinkForm.js";
import DeleteLinkForm from "./forms/DeleteLinkForm.js";

const LinkModule = (function () {
    async function render(link) {
        let linkElement = document.createElement("div");
        linkElement.innerHTML = `
          <div id="${link.link_id}" class="link-container flex">
            <div class="link-info flex flex-center">
              <img src="https://www.google.com/s2/favicons?domain=${link.url}&sz=64" width="32" height="32" alt="Icon">
              <div class="link-text flex-column">
                <p class="link-title text-ellipsis">${link.title}</p>
                <p class="link-url text-ellipsis">${link.url}</p>
              </div>
            </div>
            <div class="link-buttons flex"></div>
          </div>`;

        linkElement.querySelector(".link-buttons").appendChild(await ButtonModule.render('edit', () => editLinkForm(link)))
        linkElement.querySelector(".link-buttons").appendChild(await ButtonModule.render('delete', () => deleteLinkForm(link)))

        return linkElement.firstElementChild;
    }

    function updateState(linkId, groupId) {
        fetch(`http://localhost:8080/link-group/${groupId}/link/${linkId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch link with id ${linkId}: ${response.status}`);
                }
                return response.json();
            })
            .then(async updatedLink => {
                const linkElement = document.querySelector(`[id="${linkId}" ]`);
                if (linkElement) {
                    linkElement.replaceWith(await LinkModule.render(updatedLink));
                } else {
                    throw new Error(`Link with id ${linkId} to update not found on the website`);
                }
            })
            .catch(error => {
                console.error(`Error updating link with id ${linkId}: ${error.message}`);
            });
    }
    
    async function removeElement(linkId){
        const linkElement = document.querySelector(`[id="${linkId}" ]`); // escaping forbidden id characters
        if (linkElement) {
            linkElement.remove();
        } else {
            console.error(`Link with id ${link_id} to remove not found`);
        }
    }

    async function editLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await EditLinkForm.render(link)));
    }

    async function deleteLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await DeleteLinkForm.render(link)));
    }

    return {
        render: render,
        updateState : updateState,
        removeElement : removeElement
    };
})();

export default LinkModule;

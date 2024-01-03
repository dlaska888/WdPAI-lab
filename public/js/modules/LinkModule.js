import ButtonModule from "./ButtonModule.js";
import ModalModule from "./ModalModule.js";
import UpdateLinkForm from "./forms/UpdateLinkForm.js";
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

    async function updateLink(link) {
        const linkElement = document.querySelector(`[id="${link.link_id}" ]`); // escaping forbidden id characters
        if (linkElement) {
            const updatedLinkEl = await LinkModule.render(link);
            linkElement.replaceWith(updatedLinkEl);
        } else {
            console.error(`Link with id ${link.link_id} to update not found`);
        }
    }
    
    // TODO refactor
    async function deleteLink(link){
        const linkElement = document.querySelector(`[id="${link.link_id}" ]`); // escaping forbidden id characters
        if (linkElement) {
            linkElement.remove();
            let links = link.group.links; 
            links.splice(links.findIndex(a => a.link_id === link.link_id) , 1)
        } else {
            console.error(`Link with id ${link.link_id} to remove not found`);
        }
    }

    async function editLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await UpdateLinkForm.render(link)));
    }

    async function deleteLinkForm(link) {
        document.body.appendChild(await ModalModule.render(await DeleteLinkForm.render(link)));
    }

    return {
        render: render,
        updateLink : updateLink,
        deleteLink : deleteLink
    };
})();

export default LinkModule;

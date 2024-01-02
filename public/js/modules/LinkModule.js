import ButtonModule from "./ButtonModule.js";

const LinkModule = (function () {
    async function render(link) {
        let linkElement = document.createElement("div");
        linkElement.innerHTML = `
          <div id="${link.link_id}" class="link-container flex">
            <div class="link-info flex flex-center">
              <img src="${link.url}/favicon.ico" width="32" height="32" alt="Icon">
              <div class="link-text flex-column">
                <p class="text-ellipsis">${link.title}</p>
                <p class="text-ellipsis">${link.url}</p>
              </div>
            </div>
            <div class="link-buttons flex"></div>
          </div>`;

        linkElement.querySelector(".link-buttons").appendChild(await ButtonModule.render('edit', editLink))
        linkElement.querySelector(".link-buttons").appendChild(await ButtonModule.render('delete', deleteLink))

        return linkElement.firstElementChild;
    }

    function editLink() {
        console.log('edit');
    }

    function deleteLink() {
        console.log('delete');
    }

    return {
        render: render,
    };
})();

export default LinkModule;

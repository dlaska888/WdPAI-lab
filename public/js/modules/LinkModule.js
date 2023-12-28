import IconModule from "./IconModule.js";

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

        linkElement.querySelector(".link-info").prepend(imgElement);
        linkElement.querySelector(".link-buttons").appendChild(await getButton('edit', editLink))
        linkElement.querySelector(".link-buttons").appendChild(await getButton('delete', deleteLink))

        return linkElement.firstElementChild;
    }

    async function getButton(icon, callback) {
        const button = document.createElement("button");

        button.className = "flex flex-center";
        button.innerHTML = await IconModule.render(icon);
        button.addEventListener('click', callback);

        return button;
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

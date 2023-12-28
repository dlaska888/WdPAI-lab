import IconModule from "./IconModule.js";
import LinkModule from "./LinkModule.js";

const GroupModule = (function () {
    async function render(group) {
        let groupElement = document.createElement("div");
        groupElement.innerHTML = `
            <div class="group flex-column">
                <div class="group-menu flex">
                    <div class="group-name flex flex-center">
                        <div class="btn-group-collapse">
                            ${await IconModule.render('collapse')}
                        </div>
                        <p class="text-tertiary text-ellipsis">${group.name}</p>
                    </div>
                    <div class="group-buttons flex flex-center">
                        ${await IconModule.render('add')}
                        ${await IconModule.render('share')}
                        ${await IconModule.render('delete')}
                    </div>
                </div>
                <div class="group-links flex-column"></div>
            </div>`

        for (const link of group.links) {
            groupElement.querySelector(".group-links").appendChild(await LinkModule.render(link))
        }

        return groupElement.firstElementChild;
    }
    
    return{
        render: render
    }
})();

export default GroupModule;

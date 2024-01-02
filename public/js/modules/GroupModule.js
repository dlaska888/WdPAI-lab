import LinkModule from "./LinkModule.js";
import ButtonModule from "./ButtonModule.js";
import ModalModule from "./ModalModule.js";
import FormModule from "./FormModule.js";

const GroupModule = (function () {
    async function render(group) {
        let groupElement = document.createElement("div");
        groupElement.innerHTML = `
            <div class="group flex-column">
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

        groupElement.appendChild(await getAddForm(group));
        groupElement.querySelector(".group-name").prepend(await ButtonModule.render("collapse", collapse));

        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("add", null));
        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("share", null));
        groupElement.querySelector(".group-buttons").appendChild(await ButtonModule.render("delete", null));

        for (const link of group.links) {
            groupElement.querySelector(".group-links").appendChild(await LinkModule.render(link))
        }

        return groupElement;
    }

    function collapse(e) {
        const btn = e.target;
        const links = btn.closest(".group").querySelector(".group-links");
        links.classList.toggle("collapse");
        btn.classList.toggle("active");
    }

    async function getAddForm(group) {
        const formFields = [
            {type: "text", name: "title", placeholder: "Title"},
            {type: "text", name: "url", placeholder: "Url"}
        ];

        const submitUrl = `link-group/${group.link_group_id}/link`;
        const method = "POST";

        async function submit(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            try {
                const response = await fetch(submitUrl, {
                    method: method,
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                const responseData = await response.json();
                console.log(responseData);
            } catch (error) {
                console.error('Error submitting form:', error);
            }

        }

        return await FormModule.render(formFields, submit);
    }

    return {
        render: render
    }
})();

export default GroupModule;

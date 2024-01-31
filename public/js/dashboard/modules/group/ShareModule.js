import GroupModule from "./GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import ButtonModule from "../ButtonModule.js";
import GroupSharesModule from "./GroupSharesModule.js";

const ShareModule = (function () {

    async function render(groupShare, user) {
        const share = document.createElement("div");
        share.classList = "flex flex-center text-secondary";

        const emailContainer = document.createElement("p");
        emailContainer.classList = "flex flex-center";
        emailContainer.textContent = user.email;
        share.appendChild(emailContainer);

        const form = document.createElement("form");

        const select = document.createElement("select");
        select.className = "input";
        select.setAttribute("name", "permission");

        const permissionOptions = [{value: "READ", text: "Read"}, {value: "WRITE", text: "Write"}];

        permissionOptions.forEach(permission => {
            const option = createOption(permission);
            if (groupShare.permission === permission.value) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        select.addEventListener("change", async () => {
            await submit(groupShare,
                new FormData(form),
                `link-group/${groupShare.linkGroupId}/shares/${groupShare.id}`,
                "PUT");
            await GroupSharesModule.updateState(groupShare.linkGroupId);
        });

        form.appendChild(select);
        share.appendChild(form);

        const deleteBtn = await ButtonModule.render(
            "delete",
            async () => {
                await submit(groupShare,
                    null,
                    `link-group/${groupShare.linkGroupId}/shares/${groupShare.id}`,
                    "DELETE");
                await GroupSharesModule.updateState(groupShare.linkGroupId);
            }
        );

        share.appendChild(deleteBtn);
        return share;
    }

    async function submit(groupShare, formData, submitUrl, method) {
        try {
            const response = await ApiClient.fetchData(
                submitUrl,
                {
                    method,
                    body: formData ? JSON.stringify(Object.fromEntries(formData)) : undefined,
                }
            );

            if (response.success) {
                await GroupModule.updateState(groupShare.linkGroupId);
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred", "error");
        }
    }

    function createOption(option) {
        const optionEl = document.createElement("option");
        optionEl.value = option.value;
        optionEl.text = option.text;
        return optionEl;
    }

    return {
        render: render,
    };
})();

export default ShareModule;

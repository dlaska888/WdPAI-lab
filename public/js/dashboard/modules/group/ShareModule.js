import GroupModule from "./GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import ButtonModule from "../ButtonModule.js";
import GroupSharesModule from "./GroupSharesModule.js";

const ShareModule = (function () {

    async function render(groupShare, user) {
        let shareElement = document.createElement("div");
        shareElement.innerHTML = `
            <div class="group-share flex flex-center text-secondary">
                <div class="email-container flex flex-center">
                    <p class="flex flex-center">${user.email}</p>
                </div>
                <form>
                    <select name="permission" class="input">
                        <option value="READ">Read</option>
                        <option value="WRITE">Write</option>
                    </select>
                </form>
            </div>`
        shareElement = shareElement.firstElementChild;
        
        const options = shareElement.querySelectorAll("option");
        options.forEach(option => {
            if (groupShare.permission === option.value) {
                option.selected = true;
            }
        });

        const form = shareElement.querySelector("form");
        const select = shareElement.querySelector("select");
        select.addEventListener("change", async () => {
            await submit(groupShare,
                new FormData(form),
                `link-group/${groupShare.linkGroupId}/shares/${groupShare.id}`,
                "PUT");
            await GroupSharesModule.updateState(groupShare.linkGroupId);
        });

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

        shareElement.appendChild(deleteBtn);
        return shareElement;
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

    return {
        render: render,
    };
})();

export default ShareModule;

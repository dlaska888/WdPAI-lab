import GroupModule from "./GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import ButtonModule from "../ButtonModule.js";
import GroupSharesModule from "./GroupSharesModule.js";
import IconModule from "../IconModule.js";

const ShareModule = (function () {

    async function render(groupShare, user) {
        let shareElement = document.createElement("div");
        shareElement.innerHTML = `
            <div class="group-share flex flex-center">
                <div class="share-info flex flex-center">
                    <div class="img-container flex flex-center">
                       <img src="/account/public/${user.id}/profile-picture" 
                    alt="User image" width="30" height="30">
                    </div>
                    <p class="text-ellipsis">${user.email}</p>
                </div>
                <div class="share-buttons flex flex-center">
                    <form>
                        <select name="permission" class="input">
                            <option value="READ">👁️</option>
                            <option value="WRITE">✏️</option>
                        </select>
                    </form>
                </div>
            </div>`
        shareElement = shareElement.firstElementChild;

        const imgContainer = shareElement.querySelector(".img-container");
        const userImg = imgContainer.querySelector("img");
        userImg.onerror = async () => {
            imgContainer.innerHTML = await IconModule.render("account");
        }

        const form = shareElement.querySelector("form");
        const select = shareElement.querySelector("select");
        select.addEventListener("change", async () => {
            await submit(groupShare,
                new FormData(form),
                `link-group/${groupShare.linkGroupId}/shares/${groupShare.id}`,
                "PUT");
            await GroupSharesModule.updateState(groupShare.linkGroupId);
        });

        const options = shareElement.querySelectorAll("option");
        options.forEach(option => {
            if (groupShare.permission === option.value) {
                option.selected = true;
            }
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

        shareElement.querySelector(".share-buttons").appendChild(deleteBtn);
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

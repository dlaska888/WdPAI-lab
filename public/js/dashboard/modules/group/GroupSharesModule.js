import GroupModule from "./GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import ButtonModule from "../ButtonModule.js";
import ShareModule from "./ShareModule.js";

const GroupSharesModule = (function () {

    async function render(group) {
        const groupShares = document.createElement("div");
        groupShares.id = `share-${group.id}`

        const sharesContainer = document.createElement("div");

        for (let share of group.groupShares) {
            const user = await fetchUserDataById(share.userId);
            sharesContainer.appendChild(await ShareModule.render(share, user));
        }
        groupShares.appendChild(sharesContainer);

        const form = document.createElement("form");
        form.classList = "flex flex-center text-secondary";

        const emailInput = document.createElement("input");
        emailInput.classList = "input";
        emailInput.type = "text";
        emailInput.name = "email";
        emailInput.placeholder = "Email";
        emailInput.required = true;

        form.appendChild(emailInput);

        const permissionSelect = document.createElement("select");
        permissionSelect.className = "input";
        permissionSelect.setAttribute("name", "permission");

        const permissionOptions = [{value: "READ", text: "Read"}, {value: "WRITE", text: "Write"}];

        permissionOptions.forEach(permission => {
            const option = createOption(permission);
            permissionSelect.appendChild(option);
        });

        form.appendChild(permissionSelect);

        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            await submit(group.id, new FormData(e.target));
        });

        const addBtn = await ButtonModule.render("share", null);
        addBtn.type = "submit";
        addBtn.title = "Share";

        form.appendChild(addBtn);

        groupShares.appendChild(form);
        return groupShares;
    }
    
    async function submit(groupId, formData) {
        try {
            const response = await ApiClient.fetchData(
                `http://localhost:8080/link-group/${groupId}/shares`,
                {
                    method: "POST",
                    body: JSON.stringify(Object.fromEntries(formData))
                }
            );

            if (response.success) {
                await GroupModule.updateState(groupId);
                await updateState(groupId);
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred", "error");
        }
    }

    function updateState(groupId) {
        ApiClient.fetchData(`http://localhost:8080/link-group/${groupId}`)
            .then(async response => {
                if (response.success) {
                    const groupSharesElement = document.querySelector(`[id="share-${groupId}"]`);
                    if (groupSharesElement) {
                        groupSharesElement.replaceWith(await render(response.data));
                    } else {
                        console.error(`Group shares with id ${groupId} not found`);
                    }
                }
            });
    }

    function fetchUserDataById(userId) {
        return ApiClient.fetchData(`http://localhost:8080/account/public/${userId}`)
            .then(result => {
                if (result.success) return result.data;
                NotificationService.notify(result.message || "Could not get user data", "error")
            })
    }

    function createOption(option) {
        const optionEl = document.createElement("option");
        optionEl.value = option.value;
        optionEl.text = option.text;
        return optionEl;
    }

    return {
        render: render,
        updateState: updateState
    };
})();

export default GroupSharesModule;

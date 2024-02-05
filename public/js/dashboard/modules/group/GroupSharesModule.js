import GroupModule from "./GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import ButtonModule from "../ButtonModule.js";
import ShareModule from "./ShareModule.js";

const GroupSharesModule = (function () {

    async function render(group) {
        let groupSharesElement = document.createElement("div");
        
        groupSharesElement.innerHTML = `
            <div id="share-${group.id}" class="shares-container flex-column flex-center">
                <h2 class="flex flex-center text-secondary text-shadow">Group Shares</h2>
                <form class="share-form flex flex-center text-secondary">
                    <div class="input-container flex flex-center">
                        <input type="email" name="email" placeholder="Email" class="input" required>
                    </div>
                    <select name="permission" class="input">
                        <option value="READ">👁</option>
                        <option value="WRITE">✏️</option>
                    </select>
                </form>
                <div class="group-shares flex-column flex-center"></div>
            </div>`
        groupSharesElement = groupSharesElement.firstElementChild;        
        
        const form = groupSharesElement.querySelector("form");
        
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            await submit(group.id, new FormData(e.target));
        });

        const addBtn = await ButtonModule.render("share", null);
        addBtn.type = "submit";
        addBtn.title = "Share";

        form.appendChild(addBtn);

        const sharesContainer = groupSharesElement.querySelector(".group-shares");

        for (let share of group.groupShares) {
            const user = await fetchUserDataById(share.userId);
            sharesContainer.appendChild(await ShareModule.render(share, user));
        }
        
        return groupSharesElement;
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

    return {
        render: render,
        updateState: updateState
    };
})();

export default GroupSharesModule;

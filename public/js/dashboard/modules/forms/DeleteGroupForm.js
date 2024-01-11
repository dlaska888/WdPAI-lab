import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const DeleteGroupForm = (function () {
    async function submit(group) {
        const submitUrl = `link-group/${group.id}`;
        const method = "DELETE";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
            });

            if (response.success) {
                await GroupModule.removeElement(group.id);
                NotificationService.notify("Group deleted!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while deleting the group", "error");
        }
    }

    async function render(group) {
        return await FormModule.render(() => submit(group), "Delete group?");
    }

    return {
        render: render,
    };
})();

export default DeleteGroupForm;

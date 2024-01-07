import FormModule from "./FormModule.js";
import LinkModule from "../LinkModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const DeleteLinkForm = (function () {
    async function submit(link) {
        const submitUrl = `link-group/${link.link_group_id}/link/${link.link_id}`;
        const method = "DELETE";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
            });

            if (response.success) {
                await LinkModule.removeElement(link.link_id);
                NotificationService.notify("Link deleted!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while deleting the link", "error");
        }
    }

    async function render(link) {
        return await FormModule.render(() => submit(link), "Delete link?");
    }

    return {
        render: render,
    };
})();

export default DeleteLinkForm;

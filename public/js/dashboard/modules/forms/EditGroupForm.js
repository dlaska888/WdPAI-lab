import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const EditGroupForm = (function () {
    async function submit(group, formData) {
        const submitUrl = `link-group/${group.link_group_id}`;
        const method = "PUT";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await GroupModule.updateState(group.link_group_id);
                NotificationService.notify("Group edited!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while updating the group", "error");
        }
    }

    async function render(group) {
        const formFields = [
            { type: "text", name: "name", placeholder: "Group Name", required: true, value: group.name || " " },
        ];

        return await FormModule.render((e) => submit(group, new FormData(e.currentTarget)), "Update group", formFields);
    }

    return {
        render: render
    };
})();

export default EditGroupForm;
import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const ShareGroupForm = (function () {
    async function submit(group, formData) {
        const submitUrl = `link-group/${group.id}/share`;
        const method = "POST";

        formData.set("permission", formData.get("permission").toUpperCase());
        
        
        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await GroupModule.updateState(group.id);
                NotificationService.notify("Group shared!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while sharing the group", "error");
        }
    }

    async function render(group) {
        const formFields = [
            { type: "email", name: "email", placeholder: "Email", required: true },
            { type: "select", name: "permission", options: ["Read", "Write"], required: true },
        ];

        return await FormModule.render((e) => submit(group, new FormData(e.currentTarget)), "Share group", formFields);
    }

    return {
        render: render
    };
}());

export default ShareGroupForm;

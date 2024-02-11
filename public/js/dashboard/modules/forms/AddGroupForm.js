import FormModule from "./FormModule.js";
import LinksPage from "../pages/LinksPage.js";
import ApiClient from "../../ApiClient.js";
import NotificationService from "../../NotificationService.js";

const AddGroupForm = (function () {
    async function submit(e) {
        try {
            const formData = new FormData(e.currentTarget);
            const submitUrl = "link-group";
            const method = "POST";

            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await LinksPage.addGroup(response.data, "page-home");
                NotificationService.notify("Group added!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while submitting the form", "error");
        }
    }

    function render() {
        const formFields = [
            { type: "text", name: "name", placeholder: "Group Name", minLength: 3, maxLength: 50, required: true }
        ];

        return FormModule.render(submit, "Add Group", formFields);
    }

    return {
        render: render
    };
}());

export default AddGroupForm;

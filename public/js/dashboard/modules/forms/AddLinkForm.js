import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import StringHelper from "../../../dashboard/StringHelper.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const AddLinkForm = (function () {
    async function submitLink(group, formData) {
        const submitUrl = `link-group/${group.link_group_id}/link`;
        const method = "POST";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await GroupModule.updateState(group.link_group_id);
                NotificationService.notify("Link added!", "okay");
            }
        } catch (error) {
            console.error("Error submitting link:", error);
            NotificationService.notify("An error occurred while adding the link", "error");
        }
    }

    async function render(group) {
        const formFields = [
            { type: "url", name: "url", placeholder: "Url", required: true },
            { type: "text", name: "title", placeholder: "Title" }
        ];

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);

            formData.get("title") || formData.set("title", StringHelper.getDomainName(formData.get("url")));

            await submitLink(group, formData);
        }

        return await FormModule.render(submit, "Add link", formFields);
    }

    return {
        render: render
    };
}());

export default AddLinkForm;

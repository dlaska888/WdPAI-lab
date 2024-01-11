import FormModule from "./FormModule.js";
import LinkModule from "../LinkModule.js";
import StringHelper from "../../../dashboard/StringHelper.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const EditLinkForm = (function () {
    async function submit(link, formData) {
        console.log(link);
        const submitUrl = `link-group/${link.linkGroupId}/link/${link.id}`;
        const method = "PUT";

        formData.get("title") || formData.set("title", StringHelper.getDomainName(formData.get("url")));

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await LinkModule.updateState(link.id, link.linkGroupId);
                NotificationService.notify("Link edited!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while updating the link", "error");
        }
    }

    async function render(link) {
        const formFields = [
            { type: "text", name: "url", placeholder: "Url", required: true, value: link.url || "" },
            { type: "text", name: "title", placeholder: "Title", value: link.title || "" }
        ];

        return await FormModule.render((e) => submit(link, new FormData(e.currentTarget)), "Update link", formFields);
    }

    return {
        render: render
    };
}());

export default EditLinkForm;

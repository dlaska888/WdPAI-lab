import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import StringHelper from "../../../dashboard/StringHelper.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const AddLinkForm = (function () {
    async function submit(group, formData) {
        const submitUrl = `link-group/${group.link_group_id}/link`;
        const method = "POST";
        
        formData.get("title") || formData.set("title", StringHelper.getDomainName(formData.get("url")));

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await GroupModule.updateState(group.link_group_id);
                NotificationService.notify("Link added!", "okay");
            }else {
                NotificationService.notify(response.message, "error", response.data);
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


        return await FormModule.render((e) => submit(group, new FormData(e.currentTarget)), "Add link", formFields);
    }

    return {
        render: render
    };
}());

export default AddLinkForm;

import FormModule from "./FormModule.js";
import GroupModule from "../group/GroupModule.js";
import StringHelper from "../../../dashboard/StringHelper.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const AddLinkForm = (function () {
    async function submit(group, formData) {
        const submitUrl = `link-group/${group.id}/link`;
        const method = "POST";
        
        const url = StringHelper.getFullUrl(formData.get("url"));
        const title = formData.get("title") || StringHelper.getDomainName(url);
        
        formData.set("url", url);
        formData.set("title", title);
        
        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await GroupModule.updateState(group.id);
                NotificationService.notify("Link added!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting link:", error);
            NotificationService.notify("An error occurred while adding the link", "error");
        }
    }

    async function render(group) {
        const formFields = [
            {type: "text", name: "url", placeholder: "Url", minLength: 3, maxLength: 2000, required: true},
            {type: "text", name: "title", minLength: 3, maxLength: 50, placeholder: "Title"}
        ];


        return FormModule.render((e) => submit(group, new FormData(e.currentTarget)), "Add link", formFields);
    }

    return {
        render: render
    };
}());

export default AddLinkForm;

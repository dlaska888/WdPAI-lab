import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import StringHelper from "../../../dashboard/StringHelper.js";
import NotificationService from "../../NotificationService.js";

const AddLinkForm = (function () {
    async function render(group) {
        const formFields = [
            {type: "url", name: "url", placeholder: "Url", required: true},
            {type: "text", name: "title", placeholder: "Title", }
        ];

        const submitUrl = `link-group/${group.link_group_id}/link`;
        const method = "POST";

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);
            formData.get("title") || formData.set("title", StringHelper.getDomainName(formData.get("url")));

            fetch(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            })
                .then(async res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text);
                        });
                    } else {
                        await GroupModule.updateState(group.link_group_id);
                        NotificationService.notify("Link added!", "okay");
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                    NotificationService.notify(error.message, "error");
                });
        }

        return await FormModule.render(submit, "Add link",  formFields);
    }

    
    
    return {
        render: render
    };
}());

export default AddLinkForm;
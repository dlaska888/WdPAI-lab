import FormModule from "./FormModule.js";
import LinkModule from "../LinkModule.js";
import NotificationService from "../../NotificationService.js";

const DeleteLinkForm = (function () {
    async function render(link) {

        const submitUrl = `link-group/${link.link_group_id}/link/${link.link_id}`;
        const method = "DELETE";

        async function submit() {
            fetch(submitUrl, {method})
                .then(async res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text);
                        });
                    } else {
                        await LinkModule.removeElement(link.link_id);
                        NotificationService.notify("Link deleted!", "okay");
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                    NotificationService.notify(error.message, "error");
                });
        }

        return await FormModule.render(submit, "Delete link?");
    }

    return {
        render: render
    };
}());

export default DeleteLinkForm;

import FormModule from "./FormModule.js";
import LinkModule from "../LinkModule.js";

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
                        await LinkModule.deleteLink(link);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                });
        }

        return await FormModule.render(submit, "Delete link?");
    }

    return {
        render: render
    };
}());

export default DeleteLinkForm;

import FormModule from "./FormModule.js";
import LinkModule from "../LinkModule.js";

const EditLinkForm = (function () {
    async function render(link) {
        const formFields = [
            { type: "text", name: "url", placeholder: "Url", required: true, value: link.url || "" },
            { type: "text", name: "title", placeholder: "Title", value: link.title || "" }
        ];

        const submitUrl = `link-group/${link.link_group_id}/link/${link.link_id}`;
        const method = "PUT";

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);
            formData.get("title") || formData.set("title", "Link");

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
                        await LinkModule.updateState(link.link_id, link.link_group_id)
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                });
        }

        return await FormModule.render(submit, "Update link", formFields);
    }

    return {
        render: render
    };
}());

export default EditLinkForm;

import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";

const AddLinkForm = (function () {
    async function render(group) {
        const formFields = [
            {type: "text", name: "url", placeholder: "Url", required: true},
            {type: "text", name: "title", placeholder: "Title", }
        ];

        const submitUrl = `link-group/${group.link_group_id}/link`;
        const method = "POST";

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
                        await GroupModule.updateState(group.link_group_id);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                });
        }

        return await FormModule.render(submit, "Add link",  formFields);
    }
    
    return {
        render: render
    };
}());

export default AddLinkForm;
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
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text)
                        })
                    } else {
                        return res.json();
                    }
                })
                .then(async responseData => {
                    responseData.group = group;
                    group.links.push(responseData);
                    await GroupModule.updateGroup(group);
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
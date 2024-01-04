import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";

const ShareGroupForm = (function () {
    async function render(group) {
        const formFields = [
            { type: "email", name: "email", placeholder: "Email", required: true },
            { type: "select", name: "permission", options: ["READ", "WRITE"], required: true },
        ];

        const submitUrl = `http://localhost:8080/link-group/${group.link_group_id}/share`;
        const method = "POST";

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);

            fetch(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                },
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

        return await FormModule.render(submit, "Share group", formFields);
    }

    return {
        render: render
    };
}());

export default ShareGroupForm;

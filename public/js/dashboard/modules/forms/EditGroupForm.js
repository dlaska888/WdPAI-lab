import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";
import NotificationService from "../../NotificationService.js";

const EditGroupForm = (function () {
    async function render(group) {
        const formFields = [
            { type: "text", name: "name", placeholder: "Group Name", required: true, value: group.name || " " },
        ];

        const submitUrl = `link-group/${group.link_group_id}`;
        const method = "PUT";

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);

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
                        NotificationService.notify("Group edited!", "okay");
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                    NotificationService.notify(error.message, "error");
                });
        }

        return await FormModule.render(submit, "Update group", formFields);
    }

    return {
        render: render
    };
}());

export default EditGroupForm;

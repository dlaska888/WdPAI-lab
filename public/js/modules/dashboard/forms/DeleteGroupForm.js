import FormModule from "./FormModule.js";
import GroupModule from "../GroupModule.js";

const DeleteGroupForm = (function () {
    async function render(group) {
        const submitUrl = `link-group/${group.link_group_id}`;
        const method = "DELETE";

        async function submit() {
            fetch(submitUrl, {method})
                .then(async res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text);
                        });
                    } else {
                        await GroupModule.removeElement(group.link_group_id);
                    }
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                });
        }

        return await FormModule.render(submit, "Delete group?");
    }

    return {
        render: render
    };
}());

export default DeleteGroupForm;

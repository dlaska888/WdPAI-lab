import FormModule from "./FormModule.js";
import {addGroup} from "../../dashboard/home.js";

const AddGroupForm = (function () {
    async function render() {
        const formFields = [
            {type: "text", name: "name", placeholder: "Group Name", required: true}
        ];

        const submitUrl = "link-group"; // Assuming this is the endpoint to add a new group
        const method = "POST";

        async function submit(e) {
            const form = e.currentTarget;
            const formData = new FormData(form);

            fetch(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            })
                .then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(text);
                        });
                    } else {
                        return res.json();
                    }
                })
                .then(async responseData => {
                    await addGroup(responseData);
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                });
        }

        return await FormModule.render(submit, "Add Group", formFields);
    }

    return {
        render: render
    };
}());

export default AddGroupForm;

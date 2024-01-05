import FormModule from "./FormModule.js";
import LinksPage from "../pages/LinksPage.js";
import NotificationService from "../../NotificationService.js";

const AddGroupForm = (function () {
    async function render() {
        const formFields = [
            {type: "text", name: "name", placeholder: "Group Name", required: true}
        ];

        const submitUrl = "link-group"; 
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
                .then(async group => {
                    await LinksPage.addGroup(group, "page-home");
                    NotificationService.notify("Group added!", "okay");
                })
                .catch(error => {
                    console.error('Error submitting form:', error.message);
                    NotificationService.notify(error.message, "error");
                });
        }

        return await FormModule.render(submit, "Add Group", formFields);
    }

    return {
        render: render
    };
}());

export default AddGroupForm;

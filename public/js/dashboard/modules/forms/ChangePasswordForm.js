import FormModule from "./FormModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";

const ChangePasswordForm = (function () {
    async function submit(formData) {
        const submitUrl = `account/change-password`;
        const method = "PUT";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                NotificationService.notify("Password updated!", "okay");
            }else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while updating password", "error");
        }
    }

    async function render() {
        const formFields = [
            { type: "password", name: "password", placeholder: "Password" },
            { type: "text", name: "newPassword", placeholder: "New password" },
            { type: "text", name: "newPasswordConfirm", placeholder: "New password confirm" }
        ];

        return await FormModule.render((e) => submit(new FormData(e.currentTarget)), "Add link", formFields);
    }

    return {
        render: render
    };
}());

export default ChangePasswordForm;

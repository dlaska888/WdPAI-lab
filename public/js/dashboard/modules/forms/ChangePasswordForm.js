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
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while updating password", "error");
        }
    }

    async function render() {
        const formFields = [
            {type: "password", name: "password", placeholder: "Password", required: true},
            {
                type: "password",
                name: "newPassword",
                placeholder: "New password",
                pattern: "(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}",
                title: "Password must be at least 8 characters long, contain at least 1 lowercase letter, 1 uppercase letter, 1 number, and 1 special character",
                required: true
            },
            {
                type: "password",
                name: "newPasswordConfirm",
                placeholder: "Confirm new password",
                pattern: "(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}",
                title: "Password must be at least 8 characters long, contain at least 1 lowercase letter, 1 uppercase letter, 1 number, and 1 special character",
                required: true
            }
        ];

        return FormModule.render((e) => submit(new FormData(e.currentTarget)), "Change password", formFields);
    }

    return {
        render: render
    };
}());

export default ChangePasswordForm;

import FormModule from "./FormModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import SettingsPage from "../pages/SettingsPage.js";

const ChangeUserNameForm = (function () {
    async function submit(formData) {
        const submitUrl = `account/change-username`;
        const method = "PUT";

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
            });

            if (response.success) {
                await SettingsPage.updateState();
                NotificationService.notify("Username updated!", "okay");
            }else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while updating username", "error");
        }
    }

    async function render() {
        const formFields = [
            { type: "text", name: "userName", placeholder: "New username" }
        ];

        return await FormModule.render((e) => submit(new FormData(e.currentTarget)), "Change username", formFields);
    }

    return {
        render: render
    };
}());

export default ChangeUserNameForm;

import FormModule from "./FormModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import SettingsPage from "../pages/SettingsPage.js";
import MobileNavigationModule from "../MobileNavigationModule.js";

const ChangeProfilePictureForm = (function () {
    async function submit(formData) {
        const submitUrl = "account/profile-picture";
        const method = "POST";

        try {
            const formDataToSend = new FormData();
            formDataToSend.append("file", formData.get("file")); // Assuming "file" is the name of the file input

            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: formDataToSend,
            });

            if (response.success) {
                await SettingsPage.updateState();
                await MobileNavigationModule.updateState();
                
                NotificationService.notify("Profile picture uploaded!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while uploading profile picture", "error");
        }
    }

    async function render() {
        const formFields = [
            { type: "file", name: "file", accept: ".jpg, .jpeg, .png, .webp"},
        ];

        return FormModule.render((e) => submit(new FormData(e.currentTarget)), "Upload picture", formFields);
    }

    return {
        render: render
    };
}());

export default ChangeProfilePictureForm;

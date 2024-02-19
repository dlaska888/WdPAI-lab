import FormModule from "./FormModule.js";
import NotificationService from "../../NotificationService.js";
import ApiClient from "../../ApiClient.js";
import SettingsPage from "../pages/SettingsPage.js";
import MobileUserInfo from "../MobileUserInfo.js";

const ChangeProfilePictureForm = (function () {
    async function submit(formData) {
        const submitUrl = "account/profile-picture";
        const method = "POST";

        try {
            const formDataToSend = new FormData();

            // Resize the image before appending it to formDataToSend
            const resizedImage = await resizeImage(formData.get("file"));
            formDataToSend.append("file", resizedImage, resizedImage.name);

            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: formDataToSend,
            });

            if (response.success) {
                await SettingsPage.updateState();
                await MobileUserInfo.updateState();

                NotificationService.notify("Profile picture uploaded!", "okay");
            } else {
                NotificationService.notify(response.message, "error", response.data);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            NotificationService.notify("An error occurred while uploading profile picture", "error");
        }
    }

    function resizeImage(file) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement("img");
                img.onload = function () {
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    // Calculate the new dimensions while maintaining the aspect ratio
                    let newWidth, newHeight;
                    if (img.width > img.height) {
                        newWidth = Math.min(500, img.width);
                        newHeight = (newWidth / img.width) * img.height;
                    } else {
                        newHeight = Math.min(500, img.height);
                        newWidth = (newHeight / img.height) * img.width;
                    }

                    // Set the canvas dimensions to the calculated size
                    canvas.width = newWidth;
                    canvas.height = newHeight;

                    // Draw the image onto the canvas with the calculated dimensions
                    ctx.drawImage(img, 0, 0, newWidth, newHeight);

                    canvas.toBlob((blob) => {
                        resolve(new File([blob], "profile_picture.webp", { type: "image/webp" }));
                    }, "image/webp");

                }
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);
        })
    }

    async function render() {
        const formFields = [
            {type: "file", name: "file", accept: ".jpg, .jpeg, .png, .webp"},
        ];

        return FormModule.render((e) => submit(new FormData(e.currentTarget)), "Upload picture", formFields);
    }

    return {
        render: render
    };
}());

export default ChangeProfilePictureForm;

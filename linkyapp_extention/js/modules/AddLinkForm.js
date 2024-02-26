import FormModule from "./FormModule.js";
import NotificationService from "../NotificationService.js";
import ApiClient from "../ApiClient.js";
import StringHelper from "../StringHelper.js";

const AddLinkForm = (function () {
    async function submit(formData) {
        const submitUrl = `/link-group/a4fd05e3-3690-4eea-a397-377c9e81884c/link`;
        const method = "POST";
        const cookie = {
            'Cookie': chrome.cookies.get({
                url: 'http://srv24.mikr.us:20136',
                name: 'PHPSESSID'
            }, cookie => cookie)
        }

        try {
            const response = await ApiClient.fetchData(submitUrl, {
                method,
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {cookie}
            });

            if (response.success) {
                NotificationService.notify("Link added!", "okay", null, "body");
            } else {
                NotificationService.notify(response.message, "error", response.data, "body");
            }
        } catch (error) {
            console.error("Error submitting link:", error);
            NotificationService.notify("An error occurred while adding the link", "error");
        }
    }

    async function render(link) {
        const formFields = [
            {type: "url", name: "url", placeholder: "Url", value: link, required: true},
            {type: "text", name: "title", placeholder: "Title", value: StringHelper.getDomainName(link)}
        ];


        return await FormModule.render((e) => submit(new FormData(e.currentTarget)), null, formFields);
    }

    return {
        render: render
    };
}());

export default AddLinkForm;

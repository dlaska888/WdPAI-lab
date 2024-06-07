import FormModule from "./FormModule.js";
import NotificationService from "../NotificationService.js";
import ApiClient from "../ApiClient.js";
import {domain} from "../../appConfig.js"

const AddLinkForm = (function () {
    async function submit(formData) {
        const submitUrl = domain + `/link-group/${formData.get("groupId")}/link`;
        const method = "POST";
        const cookie = {
            'Cookie': chrome.cookies.get({
                url: domain,
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
            NotificationService.notify("An error occurred while adding link", "error", null, "body");
        }
    }

    async function render(tab) {
        const groups = (await ApiClient.fetchData(domain + "/link-groups")).data;
        const options = groups.map((group) => ({ text: group.name, value: group.id }));

        const formFields = [
            {type: "select", name: "groupId", options: options},
            {type: "text", name: "title", placeholder: "Title", value: tab.title.slice(0, 50)},
            {type: "url", name: "url", placeholder: "Url", value: tab.url, required: true},
        ];

        return await FormModule.render((e) => submit(new FormData(e.currentTarget)), null, formFields);
    }

    return {
        render: render
    };
}());

export default AddLinkForm;

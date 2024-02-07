import NotificationModule from "./modules/NotificationModule.js";

export default class NotificationService{
    static notify(message, type = "info", details = null, parent = "main"){
        const element = document.querySelector(parent);
        if (!element)
            console.error(`NotificationService error: Parent ${parent} not found`);
        
        element.appendChild(NotificationModule.render(message, type, details));
    }
}
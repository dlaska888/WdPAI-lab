const NotificationModule = (function () {
    function render(message, type = "info", timeout = 3000) {

        // Create new notification
        const notification = document.createElement("div");
        notification.className = `notification ${type} text-secondary flex flex-center`;
        notification.textContent = message.replaceAll("\"", "");

        // Handle click to close
        notification.addEventListener("click", () => close(notification));

        // Show after a second
        show(notification);

        // Set timeout to automatically close after a certain time
        setTimeout(() => {
            close(notification);
        }, timeout);

        return notification;
    }

    function show(notification) {
        notification.classList.add("show");
    }

    function close(notification) {
        notification.classList.remove("show");

        notification.remove();
    }

    return {
        render: render
    };
})();

export default NotificationModule;
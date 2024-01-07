const NotificationModule = (function () {
    function render(message, type = "info", details = null) {
        // Create new notification
        const notification = document.createElement("div");
        notification.className = `notification ${type} text-secondary flex flex-column flex-center`;

        // Create message element
        const messageElement = document.createElement("div");
        messageElement.textContent = message.replaceAll("\"", "");
        notification.appendChild(messageElement);

        // Add details if they are not null
        if (details !== null) {
            const detailsList = document.createElement("ul");

            // Iterate through key-value pairs in the details object
            Object.entries(details).forEach(([key, value]) => {
                const detailItem = document.createElement("li");
                detailItem.textContent = `${key}: ${value}`;
                detailsList.appendChild(detailItem);
            });

            notification.appendChild(detailsList);
        }


        // Handle click to close
        notification.addEventListener("click", () => close(notification));

        // Show after a second
        show(notification);
        
        // Set timeout to automatically close after a certain time
        setTimeout(() => {
            close(notification);
        }, 5000); // css timeout needs to be similar

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
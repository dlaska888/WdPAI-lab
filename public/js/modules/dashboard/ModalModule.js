import ButtonModule from "./ButtonModule.js";

const ModalModule = (function () {
    let modal;

    async function render(content, classes = null) {
        modal = document.createElement("div");
        modal.className = "modal flex flex-center " + classes || " ";

        const modalContent = document.createElement("div");
        const closeButton = await ButtonModule.render("cancel", close, "cancel-btn");

        modalContent.appendChild(closeButton);
        modalContent.className = "modal-content flex flex-column";
        modalContent.appendChild(content);

        // form submission should close the modal
        const form = modalContent.querySelector("form");
        if (form) {
            form.addEventListener("submit", close);
        }

        modal.appendChild(modalContent);
        
        modal.addEventListener("click", closeOnModalClick);
        document.body.addEventListener("keydown", closeOnEscape);

        return modal;
    }

    function close() {
        // Remove modal from the DOM
        modal.remove();
    }

    function closeOnModalClick(e) {
        if (e.target === modal) {
            close();
        }
    }

    function closeOnEscape(e) {
        if (e.key === "Escape") {
            close();
        }
    }

    return {
        render: render
    };
}());

export default ModalModule;

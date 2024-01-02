import ButtonModule from "./ButtonModule.js";

const ModalModule = (function () {
    async function render(content, classes = null) {
        const modal = document.createElement("div");
        modal.className = "modal" + classes;

        const modalContent = document.createElement("div");
        modalContent.className = "modal-content";
        modalContent.appendChild(content);

        const closeButton = await ButtonModule.render("cancel", close);

        modal.appendChild(modalContent);
        modal.appendChild(closeButton);

        return modal;
    }
    
    function close(e){
        e.target.classList.toggle("show");
    }

    return {
        render: render
    };
}());

export default ModalModule;


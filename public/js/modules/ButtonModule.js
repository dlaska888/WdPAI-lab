import IconModule from "./IconModule.js";

const ButtonModule = (function () {
    async function render(icon, callback, classes = null) {
        const button = document.createElement("button");

        button.className = "flex flex-center" + classes;
        button.innerHTML = await IconModule.render(icon);
        button.addEventListener("click", callback, true);
        
        return button;
    }

    return {
        render: render
    }
}());

export default ButtonModule;
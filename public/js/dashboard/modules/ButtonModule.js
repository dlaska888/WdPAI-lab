import IconModule from "./IconModule.js";

const ButtonModule = (function () {
    async function render(icon, callback, classes = null) {
        const button = document.createElement("button");

        button.className = "flex flex-center btn-hover-dim " + classes || " ";
        button.innerHTML = await IconModule.render(icon);
        
        button.addEventListener("click", (e) => {
            e.preventDefault();
            callback(e);
        });
        
        return button;
    }

    return {
        render: render
    }
}());

export default ButtonModule;
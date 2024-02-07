import IconModule from "./IconModule.js";

const ButtonModule = (function () {
    async function render(icon, callback = null, classes = null) {
        const button = document.createElement("button");

        button.className = "flex flex-center btn-hover-dim " + classes || " ";
        button.innerHTML = await IconModule.render(icon);
        
        if (callback === null){
            return button;
        }
        
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
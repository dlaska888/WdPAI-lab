const FormModule = (function () {
    async function render(submitCallback, header = null, fields = null, classes = null) {
        let form = document.createElement("div");
        form.innerHTML = `
        <form class="main-form flex-column flex-center">
            <h2 class="form-header text-secondary text-shadow">${header || " "}</h2>
            <button class="btn-primary" type="submit" title="LinkyApp Sign In">
                <span class="btn-primary-top">Confirm</span>
            </button>
        </form>`
        form = form.firstElementChild;
        
        form.className += classes || " ";
        
        if (fields !== null){
            form.insertBefore(renderInputContainer(fields), form.querySelector("button"));
        }

        form.addEventListener("submit", (e) => {
            e.preventDefault();
            submitCallback(e);  
        });

        return form;
    }

    function renderInputContainer(fields) {
        const inputContainer = document.createElement("div");
        inputContainer.className = "input-container flex-column flex-center";
        
        fields.forEach(field => {
            const input = document.createElement("input");
            input.className = "input";
            Object.assign(input, field);
            inputContainer.appendChild(input);
        });
        
        return inputContainer;
    }
    
    return {
        render: render
    };
}());

export default FormModule;
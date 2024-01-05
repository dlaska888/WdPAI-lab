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
            const input = createInputField(field);
            inputContainer.appendChild(input);
        });

        return inputContainer;
    }

    function createInputField(field) {
        if (field.type === 'select') {
            return createSelectInput(field);
        } else {
            return createTextInput(field);
        }
    }

    function createTextInput(field) {
        const input = document.createElement("input");
        input.className = "input";
        Object.assign(input, field);
        return input;
    }

    function createSelectInput(field) {
        const select = document.createElement("select");
        select.className = "input";
        select.setAttribute("name", field.name); // Set the name attribute using setAttribute

        if (field.options) {
            field.options.forEach(optionValue => {
                const option = document.createElement("option");
                option.value = optionValue;
                option.text = optionValue;
                select.appendChild(option);
            });
        }

        return select;
    }


    return {
        render: render
    };
}());

export default FormModule;

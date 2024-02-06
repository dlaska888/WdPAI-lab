const FormModule = (function () {
    function render(submitCallback, header = null, fields = null, classes = null) {
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

        if (fields !== null) {
            const inputContainer = renderInputContainer(fields);
            form.insertBefore(inputContainer, form.querySelector("button"));
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
        const inputTypeMap = {
            'text': createTextInput,
            'select': createSelectInput,
            'file': createFileInput, // Added file input type
            // Add more input types here
        };

        const inputType = field.type || 'text';
        const createInputFn = inputTypeMap[inputType] || createTextInput;

        return createInputFn(field);
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
        select.setAttribute("name", field.name || ''); // Set the name attribute using setAttribute

        if (field.options) {
            field.options.forEach(option => {
                const optionEl = document.createElement("option");
                optionEl.value = option.value;
                optionEl.text = option.text || option.value;
                select.appendChild(optionEl);
            });
        }

        return select;
    }

    function createFileInput(field) {
        let inputElement = document.createElement("div");
        inputElement.innerHTML = `<!-- actual upload which is hidden -->
            <div class="file-input flex flex-center">
                <input id="picture-input" type="file" hidden/>
                <label class="input flex" for="picture-input">Choose File</label>
                <p id="file-chosen" class="text-secondary text-ellipsis"></p>
            </div>`
        inputElement = inputElement.firstElementChild;

        const input = inputElement.querySelector("input");
        Object.assign(input, field);
        
        const pictureInput = inputElement.querySelector("#picture-input");
        const fileChosen = inputElement.querySelector("#file-chosen");

        pictureInput.addEventListener('change', function() {
            fileChosen.textContent = this.files[0].name;
        });

        return inputElement;
    }

    return {
        render: render
    };
}());

export default FormModule;

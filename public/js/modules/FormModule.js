const FormModule = (function () {
    async function render(fields, submitCallback, classes = null) {
        const form = document.createElement("form");
        form.className = "form" + classes;

        fields.forEach(field => {
            const input = document.createElement("input");
            input.type = field.type || "text";
            input.name = field.name || "";
            input.placeholder = field.placeholder || "";

            form.appendChild(input);
        });

        const submitButton = document.createElement("button");
        submitButton.type = "submit";
        submitButton.innerHTML = "Submit";
        
        form.addEventListener("submit", submitCallback, true);

        form.appendChild(submitButton);

        return form;
    }

    return {
        render: render
    };
}());

export default FormModule;
import IconModule from "./IconModule.js";

const KebabMenuModule = (function  () {

    async function render(options) {
        const kebab = document.createElement('button');
        kebab.classList.add('kebab');

        const kebabFigures = Array.from({length: 3}, () => document.createElement('figure'));
        kebabFigures[1].classList.add('middle');

        const dropdown = document.createElement('ul');
        dropdown.classList.add('dropdown');

        for (const option of options){
            const {optionTitle, optionIcon, callback} = option;

            const li = document.createElement('li');
            const btn = document.createElement('button');
            
            btn.classList = "flex btn-hover-dim";
            btn.innerHTML = await IconModule.render(optionIcon);
            btn.innerHTML += `<p class="flex flex-center">${optionTitle}</p>`;
            
            btn.addEventListener("click", callback);
            
            li.appendChild(btn);
            dropdown.appendChild(li);
        }

        kebab.append(...kebabFigures, dropdown);

        // Add event listeners for kebab menu interactions
        const dropdownContainer = kebab.querySelector('.dropdown');

        kebab.addEventListener('click', () => {
            dropdownContainer.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!kebab.contains(event.target) && dropdownContainer.classList.contains('active')) {
                dropdownContainer.classList.remove('active');
            }
        });
        
        return kebab;
    }
    
    return {
        render: render
    }
})();
export default KebabMenuModule;
